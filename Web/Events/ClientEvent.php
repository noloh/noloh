<?php
/**
 * @package Web
 * @subpackage Events
 * ClientEvent class file.
 */

/**
 * A ClientEvent is a kind of Event that is executed on the client without communicating with the server.
 * @link /Tutorials/Events.html#ClientEvents
 * 
 */
class ClientEvent extends Event
{
	/**
	 * @ignore
	 */
	static function GenerateString($str)
	{
		return addslashes(str_replace("'", stripslashes("\""), $str));
	}
	/**
	 * Constructor.
	 * @param string $allCodeAsString The JavaScript code to be executed
	 */
	function ClientEvent($allCodeAsString)
	{
		parent::Event(str_replace("\n", ' ', $allCodeAsString));
	}
	/**
	 * @ignore
	 */
	function GetInfo(&$arr, $onlyClientEvents)
	{
		if($onlyClientEvents)
			$arr[0] .= $this->ExecuteFunction;
		return $arr;
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventType, $ObjsId)
	{
		return $this->GetEnabled() 
			? $this->ParseToJS($eventType, $ObjsId)
			: "";
	}
	/**
	 * @ignore
	 */
	function ParseToJS($eventTypeAsString, $ObjsId)
	{
		//Temporary solution until it parses
		if(is_string($this->ExecuteFunction))
			return addslashes(str_replace("'", stripslashes("\""), $this->ExecuteFunction));
		elseif($this->ExecuteFunction instanceof ArrayList)
		{ 
			$Code = '';
			$EventCount = $this->ExecuteFunction->Count();
			for($i=0; $i<$EventCount; $i++)
				if(get_class($this->ExecuteFunction[$i]) == 'ServerEvent')
					$Code .= $this->ExecuteFunction[$i]->GetEventString($eventTypeAsString."->ExecuteFunction[$i]", $ObjsId);
				elseif(get_class($this->ExecuteFunction[$i]) == 'ClientEvent')
					$Code .= $this->ExecuteFunction[$i]->ParseToJS($eventTypeAsString."->ExecuteFunction[$i]", $ObjsId);
			return $Code;
		}
	}
	/**
	 * Launches the particular event. That is, the client will be notified to execute the given JavaScript.
	 */
	function Exec(&$execClientEvents=true)
	{
		if(!isset($GLOBALS["PropertyQueueDisabled"]) && $execClientEvents)
			AddScript($this->ExecuteFunction);
	}
}

?>