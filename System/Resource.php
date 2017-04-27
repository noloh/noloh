<?php

abstract class Resource extends Object
{
	protected $Response;
	
	function Resource() {}
	
	function Options()
	{
		$allowedList = $this->GetAllowedMethods();
		header('Access-Control-Allow-Methods: ' . implode(', ', $allowedList));
		die();
	}
	
	protected function GetAllowedMethods()
	{
		$allowedMethods = array();
		$methods = array(
			RESTRouter::Post,
			RESTRouter::Get,
			RESTRouter::Put,
			RESTRouter::Delete,
			RESTRouter::Options
		);
		foreach ($methods as $method)
		{
			$func = ucfirst(strtoLower($method));
			if (method_exists($this, $func))
			{
				$allowedMethods[] = $method;
			}
		}
		return $allowedMethods;
	}
	
	function __call($name, $args)
	{
		$name = strtoupper($name);
		switch ($name)
		{
			case RESTRouter::Post:
			case RESTRouter::Get:
			case RESTRouter::Put:
			case RESTRouter::Delete:
				return self::MethodNotAllowed($this->GetAllowedMethods());
				break;
				
			default:
				self::InternalError();
		}
	}
	
	function Respond($data)
	{
		$this->Response = $data;
	}
	
	function SendResponse()
	{
		$data = $this->Response;
		if (is_object($data))
		{
			if (method_exists($data, 'ToArray'))
			{
				// Custom things, namely, Models.
				$data = $data->ToArray();
			}
			elseif ($data instanceof DataCommand)
			{
				$data = $data->Execute()->Data;
			}
			elseif ($data instanceof DataReader)
			{
				$data = $data->Data;
			}
			else
			{
				$data = get_object_vars($data);
			}
		}
		// TODO: End buffering
		// TODO: gzip
		// TODO: Possibly send cache headers on GET requests
		header('HTTP/1.1 200 OK');
		header('Content-Type: application/json');
		echo json_encode($data);
	}


	// Errors

	public static function BadRequest($text = '', $json = null, $class = null)
	{
		header('HTTP/1.1 400 Bad Request');

		die($text);
		$exception = new JsonException(json_encode($json), $class, '400 Bad Request', $text);
		throw $exception;
	}

	public static function Unauthorized($json = null, $class = 'Login')
	{
		header('HTTP/1.1 401 Unauthorized');

		die();
		$exception = new JsonException($json, $class, '401 Unauthorized');
		throw $exception;
	}

	public static function NotFound()
	{
		header('HTTP/1.1 404 Not Found');
		die();
	}

	public static function MethodNotAllowed($allowedList = array())
	{
		header('HTTP/1.1 405 Method Not Allowed');
		if (!empty($allowedList))
		{
			header('Allow: ' . implode(', ', $allowedList));
		}
		die();
	}
	
	public static function InternalError()
	{
		header('HTTP/1.1 500 Internal Server Error');
		die();
	}
}