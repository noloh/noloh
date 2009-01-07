<?php
/**
 * Event class
 *
 * The Event class serves several purposes.
 * 
 * First of all, it is the parent class of {@see ServerEvent} and {@see ClientEvent} and allows them to have some common functionality, 
 * for instance the Enabled property, and using the [] notation to chain events. 
 * 
 * Also, an Event object serves as a collection of events when multiple events are used. Consider the following:
 * <pre>
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
 * </pre>
 * 
 * Finally, the Event class contains several static variables that contain information about the particular events,
 * such as the position of the mouse when the event triggered.
 * 
 * For more information, please see
 * @link /Tutorials/Events.html
 * 
 * @package Events
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
	
	private $Liquid;

	/**
	* When relevant, the object on which the event is happening
	*/
	public static $Source;
	/**
	* When a ServerEvent is used as a callback for Bind(), this will contain the data pertaining to the particular iteration.
	* @var string
	*/
    public static $BoundData;
    /**
	 * When relevant, the Component that was focused {@see Control::Focus}
	 * @var string
	 */
    public static $FocusedComponent;
    /**
	 * @ignore
	 */
    public static $FlashArgs;
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
	* When relevant, the text of the focused component.
	* @var string
	*/
    public static $SelectedText;
    /**
     * @ignore
     */
    public static $LiquidExec;
    
	
	/**
	 * @ignore
	 */
	public static $Conversion = array(
		'Change' => 'onchange',
		'Click' => 'onclick',
		'DoubleClick' => 'ondblclick',
		'DragCatch' => 'DragCatch',
		'Focus' => 'onfocus',
		'KeyPress' => 'KeyPress',
		'LoseFocus' => 'onblur',
		'MouseDown' => 'onmousedown',
		'MouseOut' => 'onmouseout',
		'MouseOver' => 'onmouseover',
		'MouseUp' => 'onmouseup',
		'ReturnKey' => 'ReturnKey',
		'RightClick' => 'oncontextmenu',
		'Load' => 'onload',
		'Scroll' => 'onscroll',
		'TypePause' => 'TypePause',
        'Unload' => 'onunload'
	);
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Event.
	 * @param array $eventArray An array of events that this multiple event will hold
	 * @return Event
	 */
	function Event($eventArray=array(), $handles=array())
	{
		$this->ExecuteFunction = $eventArray;
		if(is_array($eventArray))
			foreach($eventArray as $index => $event)
				$event->Handles[] = array($this, $index);
		$this->Handles = $handles;
	}
	/**
	 * @ignore
	 */
	function GetInfo(&$arr, &$onlyClientEvents, $parentLiquid = false, &$liquidClientEvent = '')
	{
		if($parentLiquid)
			$liquid = true;
		elseif($this->Liquid)
			$liquid = $becameLiquid = true;
		else 
			$liquid = false;
		foreach($this->ExecuteFunction as $event)
			if(is_object($event) && $event->GetEnabled())
				$event->GetInfo($arr, $onlyClientEvents, $liquid, $liquidClientEvent);
		if($becameLiquid && $liquidClientEvent)
		{
			$arr[0] .= 'if(liq){' . $liquidClientEvent . '} ';
			$liquidClientEvent = '';
		}
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
            $arr = array('', array(), 0, 0);
			$info = $this->GetInfo($arr, $onlyClientEvents);
			$ret = '';
			if($info[0] !== '')
				$ret .= ClientEvent::GenerateString($eventType, $info[0]);
			if(!$onlyClientEvents)
				$ret .= ServerEvent::GenerateString($eventType, $objsId, $info[1], $info[3]===0?0 : $info[2]===$info[3]?1 : 2);
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
	function Exec(&$execClientEvents=true, $liquidParent=false)
	{
		foreach($this->ExecuteFunction as $event)
			if($event->GetEnabled())
				$event->Exec($execClientEvents, $liquidParent || $this->Liquid);
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
	 * Returns whether or not the Event is enabled. A Disabled event will not launch, and execing it has no effect.
	 * @return boolean
	 */
	function GetEnabled()
	{
		return $this->Enabled === null;
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
	 * Returns whether or not the Event is Liquid. A Liquid Event will only launch if the user interacted directly with the object, not as a result of bubbling. Bubbles pass through liquids without affecting or launching them.
	 * @return boolean
	 */
	function GetLiquid()
	{
		return $this->Liquid !== null;
	}
	/**
	 * Sets whether or not the Event is Liquid. A Liquid Event will only launch if the user interacted directly with the object, not as a result of bubbling. Bubbles pass through liquids without affecting or launching them.
	 * @param boolean $bool
	 */
	function SetLiquid($bool)
	{
		$this->Liquid = ($bool ? true : null);
		$this->UpdateClient();
	}
	/**
	 * For the events of Controls, checking to see if they are null will always return false as an Event object will always
	 * be automatically instantiated for you. You must therefore check to see if it is blank instead.<br>
	 * <pre>
	 * // Will always be false. Do not do this:
	 * if($this->Click == null) {...}
	 * // Use the Blank function instead:
	 * if($this->Click->Blank()) {...}
	 * </pre>
	 * @return boolean
	 */
	function Blank()
	{
		return count($this->ExecuteFunction) === 0;
	}
	/**
	 * Returns true if one of the Controls that it handles has been shown and false otherwise
	 * @return boolean
	 */
	function GetShowStatus()
	{
		foreach($this->Handles as $pair)
			if(is_string($pair[0]))
			{
				if(GetComponentById($pair[0])->GetShowStatus())
					return true;
			}
			elseif(is_object($pair[0])) 
			{
				if($pair[0]->GetShowStatus())
					return true;
			}
			else 
			{
				if(GetComponentById($pair[0][0])->GetShowStatus())
					return true;
			}
		return false;
	}
	/**
	 * @ignore
	 */
	function GetDeepHandles(&$arr)
	{
		foreach($this->Handles as $pair)
			if(is_string($pair[0]))
				$arr[] = GetComponentById($pair[0]);
			elseif(is_object($pair[0])) 
				$pair[0]->GetDeepHandles($arr);
			else 
				$arr[] = GetComponentById($pair[0][0]);
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
		if(get_class($this) === 'Event')
			if($index !== null) 
			{
				// I'm multiple Events and you add to a specific index
				if(isset($this->ExecuteFunction[$index]))
				{
					$whatWasThereHandled = &$this->ExecuteFunction[$index]->Handles;
					if(($handleIndex = array_search(array(), $whatWasThereHandled)) !== false);
						array_splice($whatWasThereHandled, $handleIndex, 1);
				}
				$this->ExecuteFunction[$index] = $val;
				$val->Handles[] = array($this, $index);
				if(count($this->ExecuteFunction) === 1)
					foreach($this->Handles as $pair)
						if(is_string($pair[0]))
							GetComponentById($pair[0])->SetEvent($this, $pair[1]);
						elseif(is_object($pair[0]))
							$pair[0][$pair[1]] = $this;
						else 
							GetComponentById($pair[0][0])->SetEvent($this, $pair[1], $pair[0][1]);
				else
					$this->UpdateClient();
			}
			else 
				// I'm multiple Events and you append to next index
				if(count($this->ExecuteFunction) === 0)
					// I was empty (created by GetEvent) and need to become the value
					foreach($this->Handles as $pair)
						if(is_string($pair[0]))
							GetComponentById($pair[0])->SetEvent($val, $pair[1]);
						elseif(is_object($pair[0]))
							$pair[0][$pair[1]] = $val;
						else 
							GetComponentById($pair[0][0])->SetEvent($val, $pair[1], $pair[0][1]);
				else 
				{
					// I was non-empty and need to just append
					$this->ExecuteFunction[] = $val;
					$val->Handles[] = array($this);
					$this->UpdateClient();
				}
		else
		{
			// I'm a ClientEvent or ServerEvent and should become a multiple Event
			$event = new Event($index === null ? array($this, $val) : array($this, $index => $val), $this->Handles);
			$this->Handles = array(array($event, 0));
			foreach($event->Handles as $pair)
				if(is_string($pair[0]))
					GetComponentById($pair[0])->SetEvent($event, $pair[1]);
				elseif(is_object($pair[0]))
					$pair[0][$pair[1]] = $event;
				else 
					GetComponentById($pair[0][0])->SetEvent($event, $pair[1], $pair[0][1]);
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
		{
			foreach($this->ExecuteFunction as $event)
				if($event instanceof ServerEvent)
					return $event->GetUploads();
		}
		else 
			return parent::__get($nm);
	}
	/**
	 * @ignore
	 *
	function __sleep()
	{
		if(isset($GLOBALS['_NChunking']))
			foreach($this->Handles as $pair)
				if(is_string($pair[0]))
					$GLOBALS['_NControlChunk'][$pair[0]] = GetComponentById($pair[0]);
				//elseif(is_object($pair[0]))
				//	$pair[0]->UpdateClient();
				elseif(is_array($pair[0]))
					$GLOBALS['_NControlChunk'][$pair[0][0]] = GetComponentById($pair[0][0]);
		return array_keys((array)$this);
	}*/
}

?>