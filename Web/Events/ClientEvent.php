<?php
/**
 * @package Web
 * @subpackage Events
 */
class ClientEvent extends Event
{
	static function GenerateString($str)
	{
		return addslashes(str_replace("'", stripslashes("\""), $str));
	}
	
	function ClientEvent($AllCodeAsString)
	{
		parent::Event(str_replace("\n"," ", $AllCodeAsString));
	}
	
	function GetInfo(&$arr, $onlyClientEvents)
	{
		if($onlyClientEvents)
			$arr[0] .= $this->ExecuteFunction;
		return $arr;
	}
	
	function GetEventString($eventType, $ObjsId)
	{
		return $this->GetEnabled() 
			? $this->ParseToJS($eventType, $ObjsId)
			: "";
	}
	
	function ParseToJS($whatEventTypeAsString, $ObjsId)
	{
		//Temporary solution until it parses
		if(is_string($this->ExecuteFunction))
			return addslashes(str_replace("'", stripslashes("\""), $this->ExecuteFunction));
		elseif($this->ExecuteFunction instanceof ArrayList)
		{ 
			$Code = "";
			$EventCount = $this->ExecuteFunction->Count();
			for($i=0; $i<$EventCount; $i++)
				if(get_class($this->ExecuteFunction[$i]) == "ServerEvent")
					$Code .= $this->ExecuteFunction[$i]->GetEventString($whatEventTypeAsString."->ExecuteFunction[$i]", $ObjsId);
				elseif(get_class($this->ExecuteFunction[$i]) == "ClientEvent")
					$Code .= $this->ExecuteFunction[$i]->ParseToJS($whatEventTypeAsString."->ExecuteFunction[$i]", $ObjsId);
			return $Code;
		}
	}
	
	function Exec(&$execClientEvents=true)
	{
		if(!isset($GLOBALS["PropertyQueueDisabled"]) && $execClientEvents)
			AddScript($this->ExecuteFunction);
	}
}

?>