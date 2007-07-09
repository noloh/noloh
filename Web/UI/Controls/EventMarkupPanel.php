<?php
class EventMarkupPanel extends MarkupPanel
{
	private $Eventees;
	private $EventSpace;
	
	function EventMarkupPanel($markupStringOrFile, $left=0, $top=0, $width = 200, $height = 200)
	{
		parent::MarkupPanel($markupStringOrFile, $left, $top, $width, $height);
	}
	function SetMarkupString($markupStringOrFile)
	{
		$this->MarkupString = $markupStringOrFile;
		$text = is_file($markupStringOrFile)?file_get_contents($markupStringOrFile):$markupStringOrFile;
		$markupStringOrFile =  str_replace(array("\r\n", "\n", "\r", "\"", "'"), array(" ", " ", " ", "<NQt2>", "<NQt1>"), ($tmpFullString = $this->ParseEventees($text)));
		$this->AutoWidthHeight($tmpFullString);
		QueueClientFunction($this, "SetMarkupString", array("'$this->DistinctId'", "'$markupStringOrFile'"));
	}
	private function ParseEventees($text)
	{
		$this->Eventees = array();
		do 
		{
			$text = preg_replace_callback('!<n:(.*?)(\s+.*?)?\sdescriptor\s*=\s*([”"\'])([^”"\']+)\3(.*?)>(.*?)</n:(\w+)>!is',
              array($this, 'MarkupReplace'), $text, -1, $count);
  		}while ($count);
  		return $text;
	}
	private function MarkupReplace($matches)
	{
		static $id;
		$distinctId = $this->DistinctId . "e" . ++$id;
		$keyval = explode(':', $matches[4]);
		$this->Eventees[$distinctId] = array($matches[1], $keyval[0], $keyval[1]);
		if(strtolower($matches[1]) == 'component')
			return "<div id=<NQt2>$distinctId<NQt2>$matches[2]$matches[5]>$matches[6]</div>";
		return "<$matches[1]$matches[2] id=<NQt2>$distinctId<NQt2>$matches[5]>$matches[6]</$matches[7]>";
	}
	public function GetEventees($byValue=null)
	{
		$eventees = array();
		if($byValue==null)
			foreach($this->Eventees as $id => $info)
				$eventees[] = new Eventee($id, $info[1], $info[2], $this->DistinctId);
		else 
			foreach($this->Eventees as $id => $info)
				if($info[1] == $byValue)
					$eventees[] = new Eventee($id, $info[1], $info[2], $this->DistinctId);
		return $eventees;
	}
	public function UpdateEvent($eventType, $eventeeId=null)
	{
		if($eventeeId == null)
			return parent::UpdateEvent($eventType);
		
		NolohInternal::SetProperty(Event::ConvertToJS($eventType), $eventType, $eventeeId);
	}
	public function GetEvent($eventType, $eventeeId=null)
	{
		if($eventeeId == null)
			return parent::GetEvent($eventObj, $eventType);
		
		if(!isset($this->EventSpace[$eventeeId]))
			$this->EventSpace[$eventeeId] = array();
		if(!isset($this->EventSpace[$eventeeId][$eventType]))
			$this->EventSpace[$eventeeId][$eventType] = new Event(array(), array(array(array($this->DistinctId, $eventeeId), $eventType)));
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
}
?>