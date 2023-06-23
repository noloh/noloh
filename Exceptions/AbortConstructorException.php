<?php

class AbortConstructorException extends Exception
{
	protected $Key;

	function __construct($message = '', $key = '')
	{
		parent::__construct($message, 0);
		$this->Key = $key;
	}

	function GetKey()
	{
		return $this->Key;
	}
}