<?php

ini_set('html_errors', 0);

abstract class RESTRouter extends Base
{
	const Post 		= 'POST';		// Create
	const Get 		= 'GET';		// Read
	const Put 		= 'PUT';		// Update
	const Delete 	= 'DELETE';		// Delete
	
	const Options	= 'OPTIONS';	// Preflighted Requests

	// It is strongly recommended that these be changed from default
	protected static $JSONErrors = false;
	protected $APIAccessKey = null;
	protected $RequireHTTPS = false;

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

		$this->ValidateSecurity();
		$this->InitMethod();
		$this->InitResources();
	}

	protected function ValidateSecurity()
	{
		if (!empty($this->APIAccessKey) && ($this->APIAccessKey !== System::GetHTTPHeader('API-Access-Key')))
		{
			Resource::Unauthorized('Invalid API Access Key');
		}
		if ($this->RequireHTTPS && (URL::GetProtocol() !== 'https'))
		{
			Resource::Forbidden('HTTPS is required');
		}
	}
	
	protected function InitMethod()
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
	
	protected function InitResources()
	{
		$paths = $this->GetPaths();
		$this->Resource = Resource::Create($paths, $this->ResourceName);
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

		if (System::IsRESTful() && getcwd() !== $GLOBALS['_NCWD'] && !chdir($GLOBALS['_NCWD']))
		{
			exit('Error: Incorrect Working directory');
		}

		try
		{
			static::$JSONErrors = $className::$JSONErrors;
			new $className;
		}
		catch (Exception $e)
		{
			static::ErrorHandling($e);
		}
	}

	protected static function ErrorHandling(Exception $exception)
	{
		$resourceException = ($exception instanceof ResourceException);
		if (!$resourceException)
		{
			header('HTTP/1.1 500 Internal Server Error');
		}

		if (static::$JSONErrors)
		{
			$error = array(
				//'code' => $exception->getCode(),
				'type' => $resourceException ? $exception->GetErrorType() : get_class($exception),
				'message' => $exception->getMessage()
			);
			$error = json_encode($error);
		}
		else
		{
			$error = $exception->getMessage();
		}

		echo $error;
	}
}

$GLOBALS['_NREST'] = true;
register_shutdown_function(array('RESTRouter', 'Bootstrap'));
