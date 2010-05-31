<?php
/**
 * Timer class
 *
 * The Timer Component gives the developer the ability to have events launch after a certain period of time has elapsed.<br>
 * It may either launch the event once, or continue launching it periodically, depending on the value of the $Repeat property.<br>
 * <pre>
 * function Init()
 * {
 * 	// Creates a Timer that will execute every time 5 seconds expires
 *	$timer = new Timer(5000, true);
 * 	// Tells the Timer to execute the AlertIt Event on this object every time the 5 seconds expires
 *	$timer->Elapsed = new ServerEvent($this, "AlertIt");
 * }
 * function AlertIt()
 * {
 * 	Alert("5 seconds has passed");
 * }
 * </pre>
 *
 * @package Controls/Core
 */
class Timer extends Component
{
	private $Interval;
	private $Repeat;
	/**
	 * Constructor. 
	 * Be sure to call this from the constructor of any class that extends Timer
	 * @param integer $interval Specifies the number of milliseconds until event executes
	 * @param boolean $repeats Indicates whether or not the Event will keep executing periodically
	 * @return Timer
	 */
	function Timer($interval, $repeats=false)
	{
		parent::Component();
		$this->Interval = $interval;
		$this->Repeat = $repeats;
	}
	/**
	 * Gets the number of miliseconds until event executes
	 * @return integer
	 */
	function GetInterval()
	{
		return $this->Interval;
	}
	/**
	 * Sets the number of miliseconds until event executes
	 * @param integer $interval
	 */
	function SetInterval($interval)
	{
		if($this->Interval != $interval)
		{
			$this->Interval = $interval;
			$this->Reshow();
		}
	}
	/**
	 * Gets the Event associated with the Timer elapsing
	 * @return Event
	 */
	function GetElapsed()
	{
		return $this->GetEvent('Elapsed');
	}
	/**
	 * Sets the Event associated with the Timer elapsing
	 * @param Event $elapsed
	 */
	function SetElapsed($elapsed)
	{
		$this->SetEvent($elapsed, 'Elapsed');
	}
	/**
	 * Gets whether or not the Elapsed Event will execute periodically or just once
	 * @return boolean
	 */
	function GetRepeat()
	{
		return $this->Repeat;
	}
	/**
	 * Sets whether or not the Elapsed Event will execute periodically or just once
	 * @param boolean $repeat
	 */
	function SetRepeat($repeat)
	{
		if($this->Repeat != $repeat)
		{
			$this->Repeat = $repeat;
			$this->Reshow();
		}
	}
	/**
	 * @ignore
	 */
	function UpdateEvent($eventType)
	{
		QueueClientFunction($this, '_NChangeByObj', array('_N.'.$this->Id, '\'onelapsed\'', '\''.$this->GetEvent($eventType)->GetEventString($eventType,$this->Id).'\''));
		//QueueClientFunction($this, 'alert', array('window[\''.$this->Id.'\'].Interval'));
	}
	/**
	 * Stops the timer from running.
	 */
	function Stop()
	{
		if($this->GetShowStatus() === 1)
			QueueClientFunction($this, '_N.'.$this->Id.'.Stop', array(), true, Priority::High);
	}
	/**
	 * Resets the Timer. That is, regardless of how close the Timer was to completing its Interval, it will start over.
	 */
	function Reset()
	{
		if($this->GetShowStatus() === 1)
			QueueClientFunction($this, 'var t=_N.'.$this->Id.';t.Stop();t.Start', array(), true, Priority::High);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		parent::Show();
		AddNolohScriptSrc('Timer.js');
		$this->Reshow();
	}
	/**
	 * @ignore
	 */
	function Reshow()
	{
		if($this->GetShowStatus() === 1 && ($parentId = $this->GetParentId()))
			QueueClientFunction($this, 'new _NTimer', array('\''.$parentId.'\'', '\''.$this->Id.'\'', $this->Interval, (int)$this->Repeat), true, Priority::High);
	}
	/**
	 * @ignore
	 */
	function Bury()
	{
		AddScript('_N.'.$this->Id.'.Destroy();', Priority::High);
		parent::Bury();
	}
	/**
	 * @ignore
	 */
	function Adopt()
	{
		AddScript('_N.'.$this->Id.'.Destroy();', Priority::High);
		$this->Reshow();
	}
	/**
	 * @ignore
	 */
	function Resurrect()
	{
		parent::Resurrect();
		$this->Reshow();
	}
}

?>