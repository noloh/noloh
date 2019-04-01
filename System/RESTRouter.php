<?php

ini_set('html_errors', 0);

abstract class RESTRouter extends Base
{
	const Post 		= 'POST';		// Create
	const Get 		= 'GET';		// Read
	const Put 		= 'PUT';		// Update
	const Delete 	= 'DELETE';		// Delete
	
	const Options	= 'OPTIONS';	// Preflighted Requests
	
	protected $Method;
	protected $ResourceName;
	protected $Resource;
	protected $InputData;
	
	function RESTRouter()
	{
		parent::Base();

		// CORS whitelist all origins
		header('Access-Control-Allow-Origin: *');
		
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
			case self::Options:
				break;
			
			default:
				Resource::MethodNotAllowed('Method not supported: ' . $this->Method, array(
					self::Post, self::Get, self::Put, self::Delete, self::Options
				));
		}
	}

	function GetMethod()
	{
		return $this->Method;
	}

	function GetResource()
	{
		return $this->ResourceName;
	}

	function GetInputData()
	{
		return $this->InputData;
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

		$this->ResourceName = $resourceName;
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
		$this->ProcessData();
		call_user_func(array($this->Resource, ucfirst($this->Method)), $this->InputData);
		$this->Resource->SendResponse();
	}

	protected function ProcessData()
	{
		switch ($this->Method)
		{
			case self::Post:
			case self::Put:
			case self::Delete:
				if (empty($_POST))
				{
					$raw = file_get_contents('php://input');
					if ($this->Resource->ReceivesJSON)
					{
						$json = json_decode($raw, true);
						if ($json === null)
						{
							parse_str($raw, $data);
							/* If invalid JSON, parse_str on the raw has an odd effect.
								This attempts to detect that effect and produce a good error message.
								I can't think of any reason anybody would pass in a 1-element array whose
								key starts with { or [ as keys should be alphanumeric anyway. */
							if (count($data) === 1)
							{
								$key = key($data);
								$firstChar = $key[0];
								if ($firstChar === '{' || $firstChar === '[')
								{
									Resource::BadRequest('Invalid JSON.');
								}
							}
						}
						else
						{
							$data = $json;
						}
					} else {
						$data = $raw;
					}
				}
				else
				{
					$data = $_POST;
				}
				break;

			case self::Get:
				// TODO: Possibly return Not Modified response, for cache
				$data = $_GET;
				break;

			default:
				$data = array();
		}
		$this->InputData = $data;
	}

	// Bootstrap
	
	public static function Bootstrap()
	{
		$config = new Configuration();
		$className = $config->StartClass;
		try
		{
			new $className;
		}
		catch (ResourceException $e)
		{
			die($e->getMessage());
		}
	}
}

$GLOBALS['_NREST'] = true;
register_shutdown_function(array('RESTRouter', 'Bootstrap'));

