<?php

class JsonException extends Exception
{
	protected $Json;
	private $Class;
	private $Type;

	public function __construct($json, $class, $type, $message = null, $code = 0, Exception $previous = null) {
		$this->Type = $type;
		$this->Json = $json;
		$this->Class = $class;
		parent::__construct($message, $code, $previous);
	}
	function GetJson(){
		return $this->Json;
	}
	function GetResource(){
		return $this->Class;
	}
	function GetErrorType(){
		return $this->Type;
	}
}