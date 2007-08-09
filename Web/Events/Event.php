<?php
/**
 * @package Web
 * @subpackage Events
 */

/**
 * The Event class serves a several purposes. 
 * 
 * First of all, it is the parent class of ServerEvent and ClientEvent and 
 * allows them to have some common functionality, for instance the Enabled property, and using the [] notation to 
 * chain events. 
 * 
 * Also, an Event object serves as 
 * 
 * For more information, please see
 * @link /Tutorials/Events.html
 */
class Event implements ArrayAccess
{
	public $Handles;
	public $ExecuteFunction;
	private $Enabled;
	
	public static $Key;	
	public static $Caught;
	public static $MouseX;
	public static $MouseY;
	
	private static $Conversion = array(
		"Change" => "onchange",
		"Click" => "onclick",
		"DoubleClick" => "ondblclick",
		//"DragCatch" => "DragCatch",
		"LoseFocus" => "onblur",
		"MouseDown" => "onmousedown",
		"MouseOut" => "onmouseout",
		"MouseOver" => "onmouseover",
		"MouseUp" => "onmouseup",
		//"ReturnKey" => "onkeypress",
		"RightClick" => "oncontextmenu",
		"Load" => "onload",
		"Scroll" => "onscroll"
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
			$info = $this->GetInfo($arr = array("",array()), $onlyClientEvents);
			$ret = "";
			if($info[0] != "")
				$ret .= ClientEvent::GenerateString($info[0]);
			if(!$onlyClientEvents)
				$ret .= ServerEvent::GenerateString($eventType, $objsId, $info[1]);
			return $ret;
		}
		else 
			return "";
	}
	
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
	 * 
	 * @return boolean
	 */
	function GetEnabled()
	{
		return $this->Enabled===null;
	}
	/**
	 * 
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
		return (get_class($this)=="Event" && count($this->ExecuteFunction)==0);
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
		if(get_class($this) == "Event")
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
		if($nm == "Enabled")
			return $this->GetEnabled();
		elseif($nm == "Uploads" && is_array($this->ExecuteFunction))
			foreach($this->ExecuteFunction as $event)
				if($event instanceof ServerEvent)
					return $event->Uploads;
	}
	/**
	 * @ignore
	 */	
	function __set($nm, $val)
	{
		if($nm == "Enabled")
			$this->SetEnabled($val);
	}
}

?>