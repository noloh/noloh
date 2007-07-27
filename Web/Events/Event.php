<?php
/**
 * @package Web
 * @subpackage Events
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
	
	static function ValidType($eventName)
	{
		return isset(self::$Conversion[$eventName]);
	}
	
	static function ConvertToJS($eventName)
	{
		return isset(self::$Conversion[$eventName]) ? self::$Conversion[$eventName] : $eventName;
	}
	
	function Event($whatFunctionAsString, $handles=array())
	{
		$this->ExecuteFunction = $whatFunctionAsString;
		$this->Handles = $handles;
	}

	function GetInfo(&$arr, &$onlyClientEvents)
	{
		foreach($this->ExecuteFunction as $event)
			if(is_object($event) && $event->GetEnabled())
				$event->GetInfo($arr, $onlyClientEvents);
		return $arr;
	}
	
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
	
	function GetEnabled()
	{
		return $this->Enabled===null;
	}
	
	function SetEnabled($whatBool)
	{
		$this->Enabled = ($whatBool ? null : false);
		$this->UpdateClient();
	}
	
	function Blank()
	{
		return (get_class($this)=="Event" && count($this->ExecuteFunction)==0);
	}
	
	function offsetExists($index)
	{
		return(is_array($this->ExecuteFunction) && isset($this->ExecuteFunction[$index]));
	}
	
	function offsetGet($index)
	{
		return $this->offsetExists($index) ? $this->ExecuteFunction[$index] : null;
	}
	
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
	
	function offsetUnset($index)
	{
		unset($this->ExecuteFunction[$index]);
	}
	
	function __get($nm)
	{
		if($nm == "Enabled")
			return $this->GetEnabled();
		elseif($nm == "Uploads" && is_array($this->ExecuteFunction))
			foreach($this->ExecuteFunction as $event)
				if($event instanceof ServerEvent)
					return $event->Uploads;
	}
	
	function __set($nm, $val)
	{
		if($nm == "Enabled")
			$this->SetEnabled($val);
	}
}

?>