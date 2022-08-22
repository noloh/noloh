<?php

class ResourceException extends Exception
{
	private $Type;

	public function __construct($type, $message = null, $code = 0, Exception $previous = null) {
		$this->Type = $type;
		parent::__construct($message, $code, $previous);
	}
	function GetErrorType(){
		return $this->Type;
	}
}