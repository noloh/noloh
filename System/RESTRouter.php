<?php

ini_set('html_errors', 0);

abstract class RESTRouter extends Object
{
	const Post 		= 'POST';		// Create
	const Get 		= 'GET';		// Read
	const Put 		= 'PUT';		// Update
	const Delete 	= 'DELETE';		// Delete
	
	protected $Method;
	protected $Resource;
	
	function RESTRouter()
	{
		parent::Object();
		
		// TODO: Output buffering
		
		$this->InitMethod();
		$this->InitResources();
	}
	
	function InitMethod()
	{
		$this->Method = strtoupper($_SERVER['REQUEST_METHOD']);
		switch ($this->Method)
		{
			case self::Post:
			case self::Get:
			case self::Put:
			case self::Delete:
				break;
			default:
				Resource::MethodNotAllowed(array('GET', 'PUT', 'POST', 'DELETE'));
		}
	}
	
	function GetPaths()
	{
		if (isset($_SERVER['PATH_INFO']))
		{
			$pathInfo = trim($_SERVER['PATH_INFO'], '/');
		}
		else
		{
			$queryPos = strpos($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']) - 2;
			$scriptLength = strlen($_SERVER['SCRIPT_NAME']) + 1;

			$pathInfo = substr($_SERVER['REQUEST_URI'], $queryPos, $scriptLength);
		}

		$paths = explode('/', $pathInfo);
		
		if (empty($paths))
		{
			Resource::BadRequest();
		}
		
		return $paths;
	}
	
	function InitResources()
	{
		$paths = $this->GetPaths();
			
		// TODO: Might want to allow cascading resources, loop through, etc...
		if (count($paths) > 2)
		{
			Resource::BadRequest();
		}
		
		$resourceName = array_shift($paths);
		$resourceName = ucwords(str_replace('-', ' ', $resourceName));
		$resourceName = str_replace(' ', '', $resourceName);
		$className = $resourceName . 'Resource';
		if (is_subclass_of($className, 'Resource'))
		{
			$class = new ReflectionClass($className);
			$this->Resource = $class->newInstanceArgs($paths);
		}
		else
		{
			Resource::NotFound();
		}
	}
	
	public function Route()
	{
		$method = ucfirst($this->Method);
		switch ($this->Method)
		{
			case self::Post:
			case self::Put:
				$data = $_POST;
				break;
				
			case self::Get:
				// TODO: Possibly return Not Modified response, for cache
				$data = $_GET;
				break;
				
			case self::Delete:
			default:
				$data = array();
		}
		call_user_func(array($this->Resource, $method), $data);
	}
	
	
	// Bootstrap
	
	public static function Bootstrap()
	{
		$config = new Configuration();
		$className = $config->StartClass;		
		new $className;
	}
}

$GLOBALS['_NREST'] = true;
register_shutdown_function(array('RESTRouter', 'Bootstrap'));

