<?php
/**
 * @package UI
 * @subpackage Controls
 */
class Timer extends Component
{
	private $Interval;
	private $Elapsed;
	private $Repeat;
	
	function Timer($interval, $repeats=false)
	{
		parent::Component();
		$this->Interval = $interval;
		$this->Repeat = $repeats;
	}
	
	function GetInterval()
	{
		return $this->Interval;
	}
	
	function SetInterval($newInterval)
	{
		$this->Interval = $newInterval;
		$this->Show();
	}
	
	function GetElapsed()
	{
		if($this->Elapsed == null)
			$this->Elapsed = new Event(array(), array(array($this->Id, "Elapsed")));
		return $this->Elapsed;
	}
	
	function SetElapsed($newElapsed)
	{
		$this->Elapsed = $newElapsed;
		$pair = array($this->Id, "Elapsed");
		if($newElapsed != null && !in_array($pair, $newElapsed->Handles))
			$newElapsed->Handles[] = $pair;
		$this->UpdateEvent("Elapsed");
	}
	
	function SetEvent($eventObj, $eventType)
	{
		$this->SetElapsed($eventObj);
	}
	
	function UpdateEvent($eventType)
	{
		QueueClientFunction($this, "NOLOHChangeByObj", array("window.$this->Id", "'onelapsed'", "'".$this->Elapsed->GetEventString("Elapsed",$this->Id)."'"));
	}
	
	function GetRepeat()
	{
		return $this->Repeat;
	}
	
	function SetRepeat($newRepeat)
	{
		if($this->Repeat != $newRepeat)
		{
			$this->Repeat = $newRepeat;
			$this->Show();
		}
	}
	
	function Show()
	{
		parent::Show();
		$ref = "window.$this->Id";
		AddScript("$ref = new Object(); $ref.timer = set" . ($this->Repeat?"Interval":"Timeout")
			. "('if($ref.onelapsed!=null) $ref.onelapsed.call();'," . $this->Interval . ");");
	}
	
	function Reset()
	{
		$this->Show();
	}
}

?>