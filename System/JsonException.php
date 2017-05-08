<?php

class JsonException extends Exception
{
	protected $Json;
	private $Type;

	public function __construct($json, $type, $message = null, $code = 0, Exception $previous = null) {
		$this->Type = $type;
		$this->Json = $json;
		parent::__construct($message, $code, $previous);
	}
	function GetJson(){
		return $this->Json;
	}
	function GetErrorType(){
		return $this->Type;
	}
}