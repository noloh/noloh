<?php
/**
 * @package Controls/Core
 */	
/**
 * Form class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 */
class Form extends Control 
{
	const Post = 'POST', Get='GET';
	
	public $Controls;
	private $Action;
	private $Method;
	private $EncType;
	
	function Form($action = '', $left = 0, $top = 0, $width = 600, $height = 600, $method = 'POST')  
	{
		parent::Control($left, $top, $width, $height);
		$this->Controls = new ArrayList();
		$this->Controls->ParentId = $this->Id;
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