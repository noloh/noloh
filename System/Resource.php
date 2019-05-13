<?php

abstract class Resource extends Base
{
	protected $Response;
	protected $ReceivesJSON = true;
	
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
				return self::MethodNotAllowed('Method not defined: ' . $name, $this->GetAllowedMethods());
				break;
				
			default:
				self::InternalError('Unsupported and undefined method: ' . $name);
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

	public static function BadRequest($text = '')
	{
		header('HTTP/1.1 400 Bad Request');

		$exception = new ResourceException('400 Bad Request', $text);
		throw $exception;
	}

	public static function Unauthorized($text = '')
	{
		header('HTTP/1.1 401 Unauthorized');

		$exception = new ResourceException('401 Unauthorized', $text);
		throw $exception;
	}

	public static function Forbidden($text = '')
	{
		header('HTTP/1.1 403 Forbidden');
		
		$exception = new ResourceException('403 Forbidden', $text);
		throw $exception;
	}
	
	public static function NotFound($text = '')
	{
		header('HTTP/1.1 404 Not Found');
		$exception = new ResourceException('404 Not Found', $text);
		throw $exception;
	}

	public static function MethodNotAllowed($text = '', $allowedList = array())
	{
		// Old usage - deprecated
		if (is_array($text))
		{
			$temp = $allowedList;
			$allowedList = $text;
			$text = $temp;
		}

		header('HTTP/1.1 405 Method Not Allowed');
		if (!empty($allowedList))
		{
			header('Allow: ' . implode(', ', $allowedList));
		}
		$exception = new ResourceException('405 Method Not Allowed', $text);
		throw $exception;
	}
	
	public static function InternalError($text = '')
	{
		header('HTTP/1.1 500 Internal Server Error');
		$exception = new ResourceException('500 Internal Server Error', $text);
		throw $exception;
	}
	
	public function GetReceivesJSON()
	{
		return $this->ReceivesJSON;
	}
}