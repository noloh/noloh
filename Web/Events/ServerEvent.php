<?php
/**
 * @package Web
 * @subpackage Events
 */
class ServerEvent extends Event
{
	public $Source;
	public $Uploads;
	public $Parameters;
	
	static function GenerateString($eventType, $objId, $uploadArray)
	{
		return count($uploadArray) == 0
			? "PostBack(\"$eventType\",\"$objId\",event);"
			: "PostBackWithUpload(\"$eventType\",\"$objId\", Array(" . implode(",", $uploadArray) . "),event);";
	}
	
	function ServerEvent($objOrClassName, $whatFunctionAsString, $ifAnyObjectParamAsObject = null)
	{
		parent::Event($whatFunctionAsString);
		$this->Source = is_object($objOrClassName) && $objOrClassName instanceof Component
			? new Pointer($objOrClassName)
			: $objOrClassName;
		$this->Uploads = new ArrayList();
		$this->Parameters = array_slice(func_get_args(), 2);
	}
	
	function GetUploads()
	{
		$arr = array();
		foreach($this->Uploads as $ul)
			$arr[] = "\\\"" . $ul->Id . "\\\"";
		return $arr;
	}
	
	function GetInfo(&$arr, &$onlyClientEvents)
	{
		$onlyClientEvents = false;
		return $this->Uploads->Count()==0 ? $arr : array_splice($arr[1], -1, 0, $this->GetUploads());
	}
	
	function GetEventString($eventType, $objsId)
	{
		return $this->GetEnabled()
			? ServerEvent::GenerateString($eventType, $objsId, $this->GetUploads())
			: "";
	}
	
	function Exec(&$execClientEvents=true)
	{
		if(isset($GLOBALS["PropertyQueueDisabled"]))
			return;
		$execClientEvents = true;		
		
		if(is_object($this->Source))
			if($this->Source instanceof Pointer)
				$runThisString = '$this->Source->Dereference()->';
			else 
				$runThisString = '$this->Source->';
		else 
			$runThisString = $this->Source . "::";
		$runThisString .= $this->ExecuteFunction;
		
		if(strpos($this->ExecuteFunction,"(") === false)
		{
			$parameterCount = count($this->Parameters);
			$runThisString .= '(';
			for($i = 0; $i < $parameterCount - 1; $i++)
				$runThisString .= '$this->Parameters['.$i.'],';
			$runThisString .= $parameterCount > 0 ? '$this->Parameters['.$i.']);' : ');';
		}
		else 
			$runThisString .= ';';
		eval($runThisString);
	}
}

?>