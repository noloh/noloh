<?php
/**
 * @package Web.UI.Controls
 */
class RichMarkupRegion extends MarkupRegion
{
	private $Eventees;
	private $EventSpace;
	private $Larvae;
	public $ComponentSpace;
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
        if($markupStringOrFile == null)
        	return;
        if(is_file($markupStringOrFile))
			$text = file_get_contents($markupStringOrFile);
		else
			$text = $markupStringOrFile;
		$tmpFullString = $this->ParseItems($text);
		$text = str_replace(array("\r\n", "\n", "\r", "\"", "'"), array('<Nendl>', '<Nendl>', '<Nendl>', '<NQt2>', '<NQt1>'), $tmpFullString);
		/*$text = preg_replace  ("/\r\n/", '<Nendl>', $tmpFullString); 
		$text = preg_replace  ("/\n/", '<Nendl>', $text); 
		$text = preg_replace  ("/\r/", '<Nendl>', $text); 
		$text = preg_replace  ("/\"/", '<NQt2>', $text); 
		$text = preg_replace  ("/'/", '<NQt1>', $text); */
		//		$this->AutoWidthHeight($tmpFullString);
		if($this->GetShowStatus()!==0)
			//QueueClientFunction($this, "SetMarkupString", array("'$this->Id'", "'$markupStringOrFile'"), true, Priority::High);
			AddScript("SetMarkupString('$this->Id', '$text')", Priority::High);
		else
			$this->TempString = $text;
	}
	// New one's. Has issues.
	
	private function ParseItems($text)
	{
		//return;
//		do
//		{
/*        $text = preg_replace_callback('!<n:(.*?)(\s+.*?)?\s*descriptor\s*=\s*([”"\'])([^”"\']+)\3(.*?)>(.*?)</n:(\w+)>!is',
           	array($this, 'MarkupReplace'), $text, -1, $count);*/
//        $text = preg_replace_callback('!<n:(.*?)(\s+.*?)?\s*?descriptor\s*?=\s*?([”"\'])([^”"\']+)\3(.*?)>(.*?)</n:(\w+)>!is',
        $text = preg_replace_callback('!<n:(.*?)(\s+.*?)?\s*?descriptor\s*?=\s*?([”"\'])([\w]+)(?::([^"\']+))?\3(.*?)>(.*?)</n:\1>!is',
//        $text = preg_replace_callback('!<n:(.*?)(\s+.*?)?\s*?descriptor\s*?=\s*?([”"\'])([\w]+)(?::([^"\']+))?\3(.*?)>(.*?)</n:(\1)>!is',
           	array($this, 'MarkupReplace'), $text, -1, $count);
//           $text = preg_replace('!<n:(.*?)(\s+.*?)?\s*descriptor\s*=\s*([”"\'])([^”"\']+)\3(.*?)>(.*?)</n:(\1)>!is', "<replacement></replacement>", $text);
//           	array($this, 'MarkupReplace'), $text, 1, $count);  	
          
//            $text = preg_replace_callback('/\s\s+/',
//            	array($this, 'MarkupReplace'), $text, -1, $count);
//  		}while ($count);
  		return $text;
	}
	private function MarkupReplace($matches)
	{
		$id = $this->Id . 'i' . ++$this->ItemCount;
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
	}/*
	private function MarkupReplace($matches)
	{
	    
//	    print_r($matches);
//	    Alert(serialize($matches));
//	    return "<replacement></replacement>";
		//static $count = 1;
		//$id = $this->Id . 'i' . ++$count;
		$id = $this->Id . 'i' . ++$this->ItemCount;
		//return;
//		$tmp2 = explode(':', $matches[4]);
//		return "<$matches[1]$matches[2] id=\"$id\"$matches[5]>$matches[6]</$matches[7]>";
		$keyval = explode(':', $matches[4], 2);
//		$keyval = array();
//		$pos = strpos($matches[4], ':');
//		if($pos === false)
//		{
//			$keyval[0] = $matches[4];
//			$keyval[1] = "";
//		}
//		else 
//		{
//			$keyval[0] = substr($matches[4], 0, $pos);
//			$keyval[1] = substr($matches[4], $pos+1, strlen($matches[4])-$pos-1);
//		}
		//return;
		if(strtolower($matches[1]) == 'component')
		{
			$this->Larvae[$id] = array($keyval[0], $keyval[1]);
			return "<div id=\"$id\"$matches[2]$matches[5]>$matches[6]</div>";
		}
		else 
		{
			$this->Eventees[$id] = array($matches[1], $keyval[0], $keyval[1]);
//			if($this->Eventees[$id][1] == "tools_services")
//				Alert("Blah: " . $this->Eventees["N30i1"][2]);
			//	Alert("Blah2: " . $this->Eventees[$id][2]);
			return "<$matches[1]$matches[2] id=\"$id\"$matches[5]>$matches[6]</$matches[7]>";
		}
	}*/
	
	// Temporary one.
	/*private function MarkupReplace($matches)
	{
		static $id;
		$Id = $this->Id . "e" . ++$id;
		$keyval = explode(':', $matches[3]);
		if(strtolower($matches[1]) == 'component')
		{
			$this->Larvae[$Id] = array($keyval[0], $keyval[1]);
			return "<div id=\"$Id\">$matches[4]</div>";
		}
		else 
		{
			$this->Eventees[$Id] = array($matches[1], $keyval[0], $keyval[1]);
			return "<$matches[1] id=\"$Id\">$matches[4]</$matches[5]>";
		}
	}*/
	// Old one's. No Larvae.
	/*private function ParseItems($text)
	{
		$this->Eventees = array();
		$this->Larvae = array();
		do 
		{
			$text = preg_replace_callback('!<n:(.*?)\s+descriptor\s*=\s*([”"\'])([^”"\']+)\2.*?>(.*?)</n:(\w+)>!is', array($this,'MarkupReplace'), $text, -1, $count);
  		}while ($count);
  		return $text;
	}
	/*
	private function MarkupReplace($matches)
	{
		//global $lookup;
		static $id;
		$Id = $this->Id . "e" . ++$id;
		$keyval = explode(':', $matches[3]);
		$this->Eventees[$Id] = array($matches[1], $keyval[0], $keyval[1]);
		return "<$matches[1] id=\"$Id\">$matches[4]</$matches[5]>";
	}
	*/
	public function GetEventees($byValue=null)
	{
		$eventees = array();
		if($byValue===null)
			foreach($this->Eventees as $id => $info)
			{
//				if($info[1] == "tools_services")
//					Alert("Blah: " . $this->Eventees["N30i1"][2]);
				$eventees[] = new Eventee($id, $info[0], $info[1], $info[2], $this->Id);
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
//		return $larvae;
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
		$pair = array(array($this->PanelId, $eventeeId), $eventType);
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