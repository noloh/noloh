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
	
	function __construct()
	{
		parent::__construct();

		// CORS whitelist all origins
		header('Access-Control-Allow-Origin: *');
		
		// TODO: Output buffering

		$this->ValidateSecurity();
		$this->InitMethod();
		$this->InitResources();
	}

	protected function ValidateIpWhitelisting($ip, $cidrs)
	{
		$valid = IP::ValidateIpCidrRanges($ip, $cidrs);
		if (is_string($valid))
		{
			Resource::BadRequest($valid);
		}
		elseif (!$valid)
		{
			Resource::Unauthorized("Unauthorized IP for {$this->ResourceName}.");
		}
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

		/*
		 * InputMiddleware allows data transformations to be performed on all request data BEFORE it hits the individual resource
		 */
		if (method_exists($this->Resource, 'InputMiddleware'))
		{
			$this->InputData = call_user_func(array($this->Resource, 'InputMiddleware'), $this->InputData);
		}

		call_user_func(array($this->Resource, ucfirst($this->Method)), $this->InputData);

		if (method_exists($this->Resource, 'OutputMiddleware'))
		{
			call_user_func(array($this->Resource, 'OutputMiddleware'));
		}

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
					$raw = trim(file_get_contents('php://input'));

					if (
						!$this->Resource->ReceivesJSON
						|| (
							isset($_SERVER['HTTP_CONTENT_TYPE'])
							&& $_SERVER['HTTP_CONTENT_TYPE'] === 'application/xml'
						)
					)
					{
						$xml = simplexml_load_string($raw);
						if (!$xml)
						{
							Resource::BadRequest('Invalid XML.');
						}
						$data = (array) $xml;
					}
					elseif ($this->Resource->ReceivesJSON)
					{
						$json = json_decode($raw, true);
						if ($json === null)
						{
							parse_str($raw, $json);
							/* If invalid JSON, parse_str on the raw has an odd effect.
								This attempts to detect that effect and produce a good error message.
								I can't think of any reason anybody would pass in a 1-element array whose
								key starts with { or [ as keys should be alphanumeric anyway. */
							if (count($json) === 1)
							{
								$key = key($json);
								$firstChar = $key[0];
								if ($firstChar === '{' || $firstChar === '[')
								{
									Resource::BadRequest('Invalid JSON.');
								}
							}
						}
						$data = $json;
					}
					else
					{
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

		Application::SetNolohSessionVars();

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
		$debugModeError = null;
		$debugType = null;

		$resourceException = ($exception instanceof ResourceException);
		if (!$resourceException)
		{
			header('HTTP/1.1 500 Internal Server Error');

			if (isset($GLOBALS['_NConfiguration']))
			{
				$debugModeError = $GLOBALS['_NConfiguration']->DebugModeError;
				$debugType = $GLOBALS['_NConfiguration']::Alert;
			}
		}

		if (static::$JSONErrors)
		{
			$error = array(
				//'code' => $exception->getCode(),
				'type' => $debugType ?: (($resourceException) ? $exception->GetErrorType() : get_class($exception)),
				'message' => $debugModeError ?: $exception->getMessage()
			);
			$error = json_encode($error);
		}
		else
		{
			$error = $debugModeError ?: $exception->getMessage();
		}

		echo $error;
	}
}

$GLOBALS['_NREST'] = true;
register_shutdown_function(array('RESTRouter', 'Bootstrap'));
