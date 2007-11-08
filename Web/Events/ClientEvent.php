<?php
/**
 * @package Web.Events
 * ClientEvent class file.
 */

/**
 * A ClientEvent is a kind of Event that is executed on the client without communicating with the server.<br>
 * As a ClientEvent is more responsive than a {@see ServerEvent}, they are useful for running events that make the simplest changes<br>
 * to the visual aspects of an application. In order to enable client viewstate with your own ClientEvents, however, requires calling<br>
 * special NOLOH JavaScript functions. {@see JavaScriptFunctions}
 * 
 * <code>
 * 	// Instantiates a new Button
 *  $btn = new Button("Click Me");
 * 	// Sets the click of the button to an event which will alert without going to the server.
 * 	$btn->Click = new ClientEvent('alert("I have been clicked")');
 * 	// Launches that event. In particular, it will alert.
 *  $btn->Click->Exec();
 * </code>
 * 
 * For more information, please see
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
	 * @param string $allCodeAsString The JavaScript code to be executed when 
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
			: '';
	}
	/**
	 * @ignore
	 */
	function Blank()
	{
		return false;
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
	 * @param boolean $execClientEvents Indicates whether client-side code will execute. <br>
	 * Modifying this parameter is highly discouraged as it may lead to unintended behavior.<br>
	 */
	function Exec(&$execClientEvents=true)
	{
		if(!$GLOBALS['_NQueueDisabled'] && $execClientEvents && $this->Enabled===null)
			AddScript($this->ExecuteFunction);
	}
}

?>