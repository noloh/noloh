<?php
/**
 * @package Web.UI.Controls
 */	
class Form extends Guardian 
{
	
	const Post = 'POST', Get='GET';
	private $Action;
	private $Method;
	private $EncType;
	
	function Form($action = '', $left = 0, $top = 0, $width = 600, $height = 600, $method = 'POST' )  
	{
		parent::Guardian($left, $top, $width, $height);
		$this->Method = $method;
		$this->Action = $action;
	}
	function GetAction()
	{
		return $this->Action;
	}
	function SetAction($action)
	{
		$this->Action = $action;
		NolohInternal::SetProperty('action', $action, $this);
	}
	function GetMethod()
	{
		return $this->Method;
	}
	function SetMethod($method)
	{
		$this->Method = $method;
		NolohInternal::SetProperty('method', $method, $this);
	}
	function GetEncType()
	{
		return $this->EncType;
	}
	function SetEncType($encType)
	{
		$this->EncType = $encType;
		NolohInternal::SetProperty('enctype', $encType, $this);
	}
	function Show()
	{
		NolohInternal::Show('FORM', parent::Show(), $this);
	}
}
?>