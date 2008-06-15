<?php
/**
 * RichMarkupRegion class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Extended
 */
class RichMarkupRegion extends MarkupRegion
{
	public $ComponentSpace;
	
	private $Eventees;
	private $EventSpace;
	private $Larvae;
	private $TempString;
	private $ItemCount;
	
	function RichMarkupRegion($markupStringOrFile, $left=0, $top=0, $width = 200, $height = 200)
	{
		$this->ComponentSpace = array();
		$this->ItemCount = 0;
		parent::MarkupRegion($markupStringOrFile, $left, $top, $width, $height);
	}
	function SetText($markupStringOrFile)
	{
		$this->Eventees = array();
		$this->Larvae = array();
		foreach($this->ComponentSpace as $component)
			$component->SecondGuessShowStatus();
		$this->ComponentSpace = array();
		//$this->MarkupString = $markupStringOrFile;
        Control::SetText($markupStringOrFile);
        if($markupStringOrFile != null)
        {
        	//file_put_contents('/tmp/WeirdString', $markupStringOrFile);
	        if(is_file($markupStringOrFile))
				$text = file_get_contents($markupStringOrFile);
			else
				$text = &$markupStringOrFile;
			$tmpFullString = &$this->ParseItems($text);
			$text = &str_replace(array("\r\n", "\n", "\r", "\"", "'"), array('<Nendl>', '<Nendl>', '<Nendl>', '<NQt2>', '<NQt1>'), $tmpFullString);
        }
        else
        	$text = '';
		//		$this->AutoWidthHeight($tmpFullString);
		if($this->GetShowStatus()!==0)
			//QueueClientFunction($this, "SetMarkupString", array("'$this->Id'", "'$markupStringOrFile'"), true, Priority::High);
			AddScript('SetMarkupString(\'' . $this->Id. '\',\'' . $text. '\')', Priority::High);
		else
			$this->TempString = $text;
//		file_put_contents('/tmp/snakeinthegrass2', var_export($this->Eventees, true));
		
	}
	// New one's. Has issues.
	
	private function ParseItems($text)
	{
//		do
//		{
//        	$tmpText = preg_replace_callback('!<n:(.*?)(\s+.*?)?\s*descriptor\s*=\s*([”"\'])([\w\s?_-]+)(?::([^"\']+))?\3(.*?)>(.*?)</n:\1>!is',
        	$tmpText = preg_replace_callback('!<n:(.*?)(\s+.*?)?\s*descriptor\s*=\s*([”"\'])([^:]+)(?::([^"\']+))?\3(.*?)(?:/\s*>|(?:>(.*?)</n:\1>))!is',
        	array($this, 'MarkupReplace'), $text, -1, $count);
//  	}while ($count);
  		return $tmpText;
	}
	private function MarkupReplace($matches)
	{
		++$this->ItemCount;
		$id = $this->Id . 'i' . $this->ItemCount;
	
		if(strtolower($matches[1]) == 'larva')
		{
			$this->Larvae[$id] = array($matches[4], $matches[5]);
			return '<div id=<NQt2>' . $id . '<NQt2>' . $matches[2].$matches[6].'>'.$matches[7].'</div>';
		}
		else 
		{
			$this->Eventees[$id] = array($matches[1], $matches[4], $matches[5]);
			return '<'.$matches[1].$matches[2]. 'id=<NQt2>'.$id.'<NQt2>'.$matches[6].'>'.$matches[7].'</'.$matches[1].'>';
		}
	}
	public function GetEventees($byValue=null)
	{
		$eventees = array();
		if($byValue===null)
			foreach($this->Eventees as $id => $info)
			{
//				if($info[1] == "tools_services")
//					Alert("Blah: " . $this->Eventees["N30i1"][2]);
				if(isset($info[0]))
					$eventees[] = new Eventee($id, $info[0], $info[1], $info[2], $this->Id);
//				else
//					file_put_contents('/tmp/snakeinthegrass', var_export($info, true), FILE_APPEND);
			}	
		else 
			foreach($this->Eventees as $id => $info)
				if($info[1] == $byValue)
					$eventees[] = new Eventee($id, $info[0], $info[1], $info[2], $this->Id);
		return $eventees;
	}
	public function GetLarvae($byValue=null)
	{
		$larvae = array();

		if($byValue===null)
			foreach($this->Larvae as $id => $info)
				$larvae[] = new Larva($id, $info[0], $info[1], $this->Id);
		else 
			foreach($this->Larvae as $id => $info)
				if($info[0] == $byValue)
					$larvae[] = new Larva($id, $info[0], $info[1], $this->Id);
		return $larvae;
	}
	public function GetMarkupItems($byValue=null)
	{
		return array_merge($this->GetEventees($byValue), $this->GetLarvae($byValue));
	}
	public function UpdateEvent($eventType, $eventeeId=null)
	{
		if($eventeeId == null)
			return parent::UpdateEvent($eventType);
		
		NolohInternal::SetProperty(Event::$Conversion[$eventType], $eventType, $eventeeId);
	}
	public function GetEvent($eventType, $eventeeId=null)
	{
		if($eventeeId == null)
			return parent::GetEvent($eventType);
		
		if(!isset($this->EventSpace[$eventeeId]))
			$this->EventSpace[$eventeeId] = array();
		if(!isset($this->EventSpace[$eventeeId][$eventType]))
			$this->EventSpace[$eventeeId][$eventType] = new Event(array(), array(array(array($this->Id, $eventeeId), $eventType)));
		return $this->EventSpace[$eventeeId][$eventType];
	}
	public function SetEvent($eventObj, $eventType, $eventeeId=null)
	{
		if($eventeeId == null)
			return parent::SetEvent($eventObj, $eventType);
		
		if(!isset($this->EventSpace[$eventeeId]))
			$this->EventSpace[$eventeeId] = array();		
		$this->EventSpace[$eventeeId][$eventType] = $eventObj;
		//$pair = array(array($this->PanelId, $eventeeId), $eventType);
		$pair = array(array($this->Id, $eventeeId), $eventType);
		if($eventObj != null && !in_array($pair, $eventObj->Handles))
			$eventObj->Handles[] = $pair;
		$this->UpdateEvent($eventType, $eventeeId);
	}
	public function GetEventString($eventType, $eventeeId=null)
	{
		if($eventeeId == null)
			return parent::GetEventString($eventType);
		
		return $this->EventSpace[$eventeeId][$eventType]->GetEventString($eventType, $eventeeId);
	}
	public function ExecEvent($eventType, $eventeeId)
	{
		return $this->EventSpace[$eventeeId][$eventType]->Exec($execClientEvents=false);
	}
	public function Show()
	{
		parent::Show();
		AddScript("SetMarkupString('$this->Id', '$this->TempString')", Priority::High);
		$this->TempString = null;
	}
}
?>