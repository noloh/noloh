<?php

class JsonException extends Exception
{
	private $Json;
	private $Class;
	private $Type;

	public function __construct($json, $class, $type, $message = null, $code = 0, Exception $previous = null) {
		$this->Type = $type;
		$this->Json = $json;
		$this->Class = $class;
		parent::__construct($message, $code, $previous);
	}
	function getJson(){
		return $this->Json;
	}
	function getResource(){
		return $this->Class;
	}
	function getErrorType(){
		return $this->Type;
	}
}