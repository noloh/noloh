<?php

abstract class Resource extends Base
{
	protected $Response;
	protected $ReceivesJSON = true;
	
	function __construct() {}
	
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
		// Duplicated from Base because this cannot parent::__call
		// Backwards compatibility before PHP8
		if (class_exists($name, false) && is_a($this, $name))
		{
			$constructor = new ReflectionMethod($name, '__construct');
			return $constructor->invokeArgs($this, $args);
		}


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
		System::SendHttpResponse($data, 200);
	}

	private static function IsAResponseInterface($obj)
	{
		/* Checking for method existence is going to be a little more reliable than checking instanceof GuzzleHttp\Psr7\Response
			because at the time of this writing, we are already using 3 different versions of Guzzle, a moving target. */
		$methods = array(
			'getStatusCode',
			'getReasonPhrase',
			'getHeaders',
			'getBody'
		);
		foreach ($methods as $method)
		{
			if (!method_exists($obj, $method))
			{
				return false;
			}
		}
		return true;
	}

	private static function SendResponseFromInterface($obj)
	{
		$statusCode = $obj->getStatusCode();
		$reasonPhrase = $obj->getReasonPhrase();
		header("HTTP/1.1 {$statusCode} {$reasonPhrase}");

		foreach ($obj->getHeaders() as $key => $val)
		{
			if (is_array($val))
			{
				$val = reset($val);
			}
			header("{$key}: {$val}");
		}

		echo (string)$obj->getBody();
	}

	static function ParseClass(&$paths, &$resourceName = null)
	{
		if (is_string($paths))
		{
			$paths = explode('/', $paths);
		}
		// TODO: Might want to allow cascading resources, loop through, etc...
		if (count($paths) > 2)
		{
			Resource::BadRequest('API does not support resources more than 1 level deep');
		}

		$resourceName = array_shift($paths);
		$resourceName = ucwords(str_replace('-', ' ', $resourceName));
		$resourceName = str_replace(' ', '', $resourceName);

		// Allows .php files in URL when using resources
		if (strpos($resourceName, '.php') === false)
		{
			$className = $resourceName . 'Resource';
		}
		else
		{
			$resourceName = str_replace('.php', '', $resourceName);
			$className = $resourceName;
		}

		return $className;
	}

	static function Create($paths, &$resourceName = null)
	{
		$className = static::ParseClass($paths, $resourceName);

		if (is_subclass_of($className, 'Resource'))
		{
			$class = new ReflectionClass($className);
			return $class->newInstanceArgs($paths);
		}
		else
		{
			Resource::NotFound('Resource not found.');
		}
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