<?php

class SqlFriendlyException extends Exception
{
	protected $CallBack;

	function __construct($message, $callBack)
	{
		$this->CallBack = $callBack;

		$error = array();
		preg_match("/SQL_FRIENDLY_EXCEPTION: (.*)/", $message, $error);
		parent::__construct($error[1]);
	}
	function CallBackExec()
	{
		call_user_func_array($this->CallBack, array($this->getMessage()));
	}
}