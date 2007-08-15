<?php
/**
 * @package Web.UI.Controls
 */	
class Form extends Guardian 
{
	private $Action;
	private $Method;
	private $EncType;
	
	function Form($action = "", $left = 0, $top = 0, $width = 800, $height = 600, $method = "POST" )  
	{
		parent::Guardian($left, $top, $width, $height);
		$this->Method = $method;
		$this->Action = $action;
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