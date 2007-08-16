<?php
/**
 * @package Web.Events
 */

/**
 * The Event class serves several purposes. 
 * 
 * First of all, it is the parent class of {@see ServerEvent} and {@see ClientEvent} and allows them to have some common functionality, 
 * for instance the Enabled property, and using the [] notation to chain events. 
 * 
 * Also, an Event object serves as a collection of events when multiple events are used. Consider the following:
 * <code>
 * // Instantiate a new Button
 * $pnl = new Button("Click Me");
 * // Alert the class of the button's Click
 * Alert(get_class($pnl->Click)); // Will alert "Event" because Control Events are never null. {@see Event::Blank()}
 * // Give its Click a ServerEvent
 * $pnl->Click = new ServerEvent($this, "ButtonClicked");
 * // Alert the class of the button's Click
 * Alert(get_class($pnl->Click)); // Will alert "ServerEvent" because that is what has been set
 * $pnl->Click[] = new ServerEvent($this, "AlsoDoThisFunction");
 * // Alert the class of the button's Click
 * Alert(get_class($pnl->Click)); // Will alert "Event" because Click now holds multiple events.
 * </code>
 * 
 * Finally, the Event class contains several static variables that contain information about the particular events,
 * such as the position of the mouse when the event triggered.
 * 
 * For more information, please see
 * @link /Tutorials/Events.html
 */
class Event extends Object implements ArrayAccess
{
	/**
	 * @ignore
	 */
	public $Handles;
	/**
	 * @ignore
	 */
	public $ExecuteFunction;
	/**
	 * @ignore
	 */
	protected $Enabled;
	
	/**
	 * When relevant, the ASCII value of the keyboard key at the time the event triggered. {@see Control::KeyPress}
	 * @var integer
	 */
	public static $Key;	
	/**
	 * When relevant, an array of all objects caught at the time the event triggered. {@see Control::DragCatch}
	 * @var array
	 */
	public static $Caught;
	/**
	 * When relevant, the x-position of the mouse at the time the event triggered. {@see Control::Click}
	 * @var integer
	 */
	public static $MouseX;
	/**
	 * When relevant, the y-position of the mouse at the time the event triggered. {@see Control::Click}
	 * @var integer
	 */
	public static $MouseY;
	
	/**
	 * @ignore
	 */
	private static $Conversion = array(
		'Change' => 'onchange',
		'Click' => 'onclick',
		'DoubleClick' => 'ondblclick',
		//'DragCatch' => 'DragCatch',
		'LoseFocus' => 'onblur',
		'MouseDown' => 'onmousedown',
		'MouseOut' => 'onmouseout',
		'MouseOver' => 'onmouseover',
		'MouseUp' => 'onmouseup',
		//'ReturnKey' => 'onkeypress',
		'RightClick' => 'oncontextmenu',
		'Load' => 'onload',
		'Scroll' => 'onscroll'
	);
	
	/**
	 * @ignore
	 */
	static function ValidType($eventName)
	{
		return isset(self::$Conversion[$eventName]);
	}
	/**
	 * @ignore
	 */	
	static function ConvertToJS($eventName)
	{
		return isset(self::$Conversion[$eventName]) ? self::$Conversion[$eventName] : $eventName;
	}
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Event.
	 * @param array $eventarray An array of events that this multiple event will hold
	 * @return Event
	 */
	function Event($eventarray=array(), $handles=array())
	{
		$this->ExecuteFunction = $eventarray;
		$this->Handles = $handles;
	}
	/**
	 * @ignore
	 */
	function GetInfo(&$arr, &$onlyClientEvents)
	{
		foreach($this->ExecuteFunction as $event)
			if(is_object($event) && $event->GetEnabled())
				$event->GetInfo($arr, $onlyClientEvents);
		return $arr;
	}
	/**
	 * @ignore
	 */	
	function GetEventString($eventType, $objsId)
	{
		if($this->GetEnabled())
		{
			$onlyClientEvents = true;
			$info = $this->GetInfo($arr = array('',array()), $onlyClientEvents);
			$ret = '';
			if($info[0] != '')
				$ret .= ClientEvent::GenerateString($info[0]);
			if(!$onlyClientEvents)
				$ret .= ServerEvent::GenerateString($eventType, $objsId, $info[1]);
			return $ret;
		}
		else 
			return '';
	}
	/**
	 * Launches the particular event. That is, all the events of this multiple event will be triggered. If 
	 * @param boolean $execClientEvents Indicates whether client-side code will execute. <br>
	 * Modifying this parameter is highly discouraged as it may lead to unintended behavior.<br>
	 */
	function Exec(&$execClientEvents=true)
	{
		foreach($this->ExecuteFunction as $event)
			if($event->GetEnabled())
				$event->Exec($execClientEvents);
	}
	/**
	 * @ignore
	 */
	function UpdateClient()
	{
		foreach($this->Handles as $pair)
			if(is_string($pair[0]))
				GetComponentById($pair[0])->UpdateEvent($pair[1]);
			elseif(is_object($pair[0])) 
				$pair[0]->UpdateClient();
			else 
				GetComponentById($pair[0][0])->UpdateEvent($pair[1], $pair[0][1]);
	}
	/**
	 * Gets whether or not the Event is enabled. A Disabled event will not launch, and execing it has no effect.
	 * @return boolean
	 */
	function GetEnabled()
	{
		return $this->Enabled===null;
	}
	/**
	 * Sets whether or not the Event is enabled. A Disabled event will not launch, and execing it has no effect.
	 * @param boolean $bool
	 */
	function SetEnabled($bool)
	{
		$this->Enabled = ($bool ? null : false);
		$this->UpdateClient();
	}
	/**
	 * For the events of Controls, checking to see if they are null will always return false as an Event object will always
	 * be automatically instantiated for you. You must therefore check to see if it is blank instead.<br>
	 * <code>
	 * // Will always be false. Do not do this:
	 * if($this->Click == null) {...}
	 * // Use the Blank function instead:
	 * if($this->Click->Blank()) {...}
	 * </code>
	 * @return boolean
	 */
	function Blank()
	{
		return count($this->ExecuteFunction)==0;
	}
	/**
	 * @ignore
	 */
	function offsetExists($index)
	{
		return(is_array($this->ExecuteFunction) && isset($this->ExecuteFunction[$index]));
	}
	/**
	 * @ignore
	 */
	function offsetGet($index)
	{
		return $this->offsetExists($index) ? $this->ExecuteFunction[$index] : null;
	}
	/**
	 * @ignore
	 */
	function offsetSet($index, $val)
	{
		if(get_class($this) == 'Event')
			if($index !== null)
			{
				$this->ExecuteFunction[$index] = $val;
				if(count($this->ExecuteFunction)==1)
					foreach($this->Handles as $pair)
						if(is_string($pair[0]))
							GetComponentById($pair[0])->SetEvent($this, $pair[1]);
						elseif(is_object($pair[0]))
							$pair[0][$pair[1]] = $this;
						else 
							GetComponentById($pair[0][0])->SetEvent($this, $pair[1], $pair[0][1]);
			}
			else 
				if(count($this->ExecuteFunction)==0)// && count($this->Handles)!=0)
					foreach($this->Handles as $pair)
						if(is_string($pair[0]))
							GetComponentById($pair[0])->SetEvent($val, $pair[1]);
						elseif(is_object($pair[0]))
							$pair[0][$pair[1]] = $val;
						else 
							GetComponentById($pair[0][0])->SetEvent($val, $pair[1], $pair[0][1]);
				else 
				{
					$this->ExecuteFunction[] = $val;
					$this->UpdateClient();
				}
		else
		{
			if($index === null)
				$event = new Event(array($this, $val), $this->Handles);
			else 
				$event = new Event(array($this, $index => $val), $this->Handles);
			$this->Handles = array(array($event, 0));
			foreach($event->Handles as $pair)
				if(is_string($pair[0]))
					GetComponentById($pair[0])->SetEvent($event, $pair[1]);
				elseif(is_object($pair[0]))
					$pair[0][$pair[1]] = $event;
				else 
					GetComponentById($pair[0][0])->SetEvent($val, $pair[1], $pair[0][1]);
		}
	}
	/**
	 * @ignore
	 */
	function offsetUnset($index)
	{
		unset($this->ExecuteFunction[$index]);
	}
	/**
	 * @ignore
	 */
	function __get($nm)
	{
		if($nm == 'Uploads' && is_array($this->ExecuteFunction))
			foreach($this->ExecuteFunction as $event)
				if($event instanceof ServerEvent)
					return $event->GetUploads();
		else 
			return parent::__get($nm);
	}
}

?>