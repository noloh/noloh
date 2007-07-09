<?php
	
class Form extends Guardian 
{
	private $Action;
	private $Method;
	private $EncType;
	
	function Form($whatAction ="", $whatLeft = 0, $whatTop = 0, $whatWidth = 800, $whatHeight = 600, $method = "POST" )  
	{
		parent::Guardian($whatLeft, $whatTop, $whatWidth, $whatHeight);
		$this->HtmlName = $this->DistinctId;
		$this->Method = $method;
		$this->Action = $whatAction;
	}
	
	function GetAction()
	{
		return $this->Action;
	}
	
	function SetAction($newAction)
	{
		$this->Action = $newAction;
		NolohInternal::SetProperty("action", $newAction, $this);
	}
	
	function GetMethod()
	{
		return $this->Method;
	}
	
	function SetMethod($newMethod)
	{
		$this->Method = $newMethod;
		NolohInternal::SetProperty("method", $newMethod, $this);
	}
	
	function GetEncType()
	{
		return $this->EncType;
	}
	
	function SetEncType($newEncType)
	{
		$this->EncType = $newEncType;
		NolohInternal::SetProperty("enctype", $newEncType, $this);
	}
	
	function Show()
	{
		$initialProperties = parent::Show();
		NolohInternal::Show("FORM", $initialProperties, $this);
	}
}
?>