<?php
/**
 * Application class
 *
 * The Application class contains static methods that control the application from a very general perspective.
 * It is perhaps best known for the important Start method, though it serves a number of critical
 * internal purposes as well.
 *
 * @package System
 */
final class Application extends Base
{
	/**
	 * @ignore
	 */
	const Name = '@APPNAME';
	const DefaultAlgo = 'crc32';
	const Bin2Hex = 'bin2hex';
	/**
	 * @var WebPage
	 */
	private $WebPage;

	public static $RequestDetails;
	/**
	 * @ignore
	 */
	public static function AutoStart()
	{
		if (empty($GLOBALS['_NREST']))
		{
			$GLOBALS['_NAutoStart'] = true;
			if (isset($GLOBALS['_NApplication']) &&
				$GLOBALS['_NApplication'] instanceof Application
			)
			{
				$GLOBALS['_NApplication']->HandleFirstRun();
			}
			else
			{
				Application::Start();
			}
		}
	}
	/**
	 * Starts the Application, optionally with some Configuration parameters.
	 * @param mixed,... $dotDotDot
	 * @return Configuration
	 * @throws
	 */
	public static function &Start($dotDotDot=null)
	{
		if (empty($GLOBALS['_NApp']))
		{
			self::InitRequestDetails();
			if(!UserAgent::IsCLI() && getcwd() !== $GLOBALS['_NCWD'] && !chdir($GLOBALS['_NCWD']))
				exit('Error with working directory. This could be caused by two reasons: you do a chdir in main after including the kernel, or your server is not compatible with not allowing a Application::Start call.');
			if(isset($_REQUEST['_NApp']))
				ini_set('session.use_cookies', 0);
			else
				session_set_cookie_params(30);
			System::BeginBenchmarking('_N/Application::Start');


			$algo = getenv('COOKIE_NAME_ALGO') ?: self::DefaultAlgo;
			if ($algo === self::Bin2Hex)
			{
				$sessionName = '_NS' . bin2hex($_SERVER['PHP_SELF']);
			}
			else
			{
				$sessionName = '_NS' . hash($algo, $_SERVER['PHP_SELF']);	// Protection from different folders or files grabbing same session
			}

			$currentSessionName = session_name($sessionName);
			if ($currentSessionName === false)
			{
				$error = [
					'message' => 'Failed to set session name',
					'session_name' => $sessionName
				];
				self::LogSessionError('session_name.failed', $error);
			}

			Cookie::SetSessionParams();
			$cookieSessionId = null;
			if (isset($_COOKIE[$sessionName]))
			{
				$cookieSessionId = session_id($_COOKIE[$sessionName]);
				if ($cookieSessionId === false)
				{
					$error = [
						'message' => 'Failed to set cookie session ID',
						'session_name' => $sessionName,
						'current_session_name' => $currentSessionName
					];
					self::LogSessionError('cookie_session_id.failed', $error);
				}
			}

			// Capture pre-start state for diagnostics
			$preStartStatus = session_status();
			$preStartSessionId = session_id();

			$sessionStarted = session_start();
			if ($sessionStarted === false)
			{
				$error = [
					'message' => 'Session failed to start',
					'session_name' => $sessionName,
					'current_session_name' => $currentSessionName,
					'cookie_session_id' => $cookieSessionId,
					'pre_start_status' => self::GetSessionStatusName($preStartStatus),
					'pre_start_session_id' => $preStartSessionId ?: '(empty)',
					'session_module_name' => session_module_name(),
					'headers_sent' => headers_sent($headersFile, $headersLine),
					'headers_sent_location' => headers_sent() ? "$headersFile:$headersLine" : null,
					'last_error' => error_get_last(),
				];
				self::LogSessionError('session_start.failed', $error);
			}

			$GLOBALS['_NApp'] = session_id();
			if ($GLOBALS['_NApp'] === false)
			{
				$error = [
					'message' => 'Failed to set session ID',
					'session_name' => $sessionName,
					'current_session_name' => $currentSessionName,
					'session_started' => $sessionStarted ? 'Yes' : 'No'
				];
				self::LogSessionError('session_id.failed', $error);
			}


			self::$RequestDetails['total_session_io_time'] += System::Benchmark('_N/Application::Start');
			if (isset($_SESSION['_NConfiguration']))
			{
				$config = $_SESSION['_NConfiguration'];
			}
			else
			{
				$args = func_get_args();
				if (count($args) === 1 && $args[0] instanceof Configuration)
				{
					$config = $args[0];
				}
				else
				{
					$reflect = new ReflectionClass('Configuration');
					$config = $reflect->newInstanceArgs($args);
				}
				$_SESSION['_NConfiguration'] = &$config;
			}
			if ($config->StartClass)
			{
				new Application($config);
			}
			return $config;
		}
		else
		{
			return $_SESSION['_NConfiguration'];
		}
		return false;
	}

	private static function LogSessionError($message, array $error)
	{
		$error['diagnostics'] = self::GetSessionDiagnostics();
		$e = new Exception(json_encode($error));
		_NLogError($message, $e);
	}

	private static function GetSessionDiagnostics(): array
	{
		$diagnostics = [
			// PHP Session Configuration
			'session_config' => [
				'save_handler' => ini_get('session.save_handler'),
				'save_path' => ini_get('session.save_path'),
				'use_cookies' => ini_get('session.use_cookies'),
				'use_only_cookies' => ini_get('session.use_only_cookies'),
				'use_strict_mode' => ini_get('session.use_strict_mode'),
				'cookie_lifetime' => ini_get('session.cookie_lifetime'),
				'cookie_secure' => ini_get('session.cookie_secure'),
				'cookie_httponly' => ini_get('session.cookie_httponly'),
				'cookie_samesite' => ini_get('session.cookie_samesite'),
				'gc_maxlifetime' => ini_get('session.gc_maxlifetime'),
				'gc_probability' => ini_get('session.gc_probability'),
				'gc_divisor' => ini_get('session.gc_divisor'),
			],
			// Session State
			'session_state' => [
				'session_status' => self::GetSessionStatusName(session_status()),
				'session_id' => session_id() ?: '(empty)',
				'session_name' => session_name(),
				'session_module_name' => session_module_name(),
			],
			// Redis Environment (if applicable)
			'redis_env' => [
				'REDIS_HOST' => getenv('REDIS_HOST') ?: '(not set)',
				'REDIS_PORT' => getenv('REDIS_PORT') ?: '(not set)',
				'REDIS_USE_TLS' => getenv('REDIS_USE_TLS') ?: '(not set)',
			],
			// Cookie State
			'cookies' => [
				'cookie_count' => count($_COOKIE),
				'session_cookie_exists' => isset($_COOKIE[session_name()]),
				'session_cookie_length' => isset($_COOKIE[session_name()]) ? strlen($_COOKIE[session_name()]) : 0,
			],
			// Request Info
			'request' => [
				'request_method' => $_SERVER['REQUEST_METHOD'] ?? '(unknown)',
				'php_self' => $_SERVER['PHP_SELF'] ?? '(unknown)',
				'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 100),
			],
			// System Info
			'system' => [
				'php_version' => PHP_VERSION,
				'php_sapi' => PHP_SAPI,
				'memory_usage' => memory_get_usage(true),
				'memory_peak' => memory_get_peak_usage(true),
			],
		];

		// Check if Redis extension is loaded
		if (extension_loaded('redis')) {
			$diagnostics['redis_env']['extension_version'] = phpversion('redis');
			$diagnostics['redis_env']['extension_loaded'] = true;
		} else {
			$diagnostics['redis_env']['extension_loaded'] = false;
		}

		return $diagnostics;
	}

	private static function GetSessionStatusName(int $status): string
	{
		switch ($status) {
			case PHP_SESSION_DISABLED:
				return 'PHP_SESSION_DISABLED';
			case PHP_SESSION_NONE:
				return 'PHP_SESSION_NONE';
			case PHP_SESSION_ACTIVE:
				return 'PHP_SESSION_ACTIVE';
			default:
				return "UNKNOWN($status)";
		}
	}

	/**
	 * Resets Application to original state
	 * @param boolean $clearURLTokens Whether the URL Tokens will be cleared out
	 * @param boolean $clearSessionVariables Whether the session will be cleared out
	 * @param boolean $alert If a string, it will be alerted to the user before resetting
	 */
	public static function Reset($clearURLTokens = true, $clearSessionVariables = true, $alert = false)
	{
		Application::ObEndAll();
		echo '/*_N*/';
		if ($alert)
			echo 'alert("', str_replace(array('\\',"\n","\r",'"'),array('\\\\','\n','\r','\"'),$alert), '");';
		$webPage = WebPage::That();
		if($webPage != null && !$webPage->GetUnload()->Blank())
		{
			echo 'window.onunload=null;';
			$webPage->Unload->Exec();
		}
		if($clearSessionVariables)
			session_destroy();
		else
			self::UnsetNolohSessionVars();
		if ($clearURLTokens)
		{
			// Used to be PHP_SELF, without parsing. That failed for webserver proxy.
			$uri = $_SERVER['DOCUMENT_URI'];
			$url = '"' . substr($uri, 0, strpos($uri, '?')) . '"';
		}
		else
		{
			$url = 'location.href';
		}
		$browser = GetBrowser();
		if ($browser === 'ie' || $browser === 'ff' || $browser === 'ch' || $browser === 'ed')
		{
			if ($clearURLTokens)
			{
				echo 'window.location.replace(', $url, ');';
			}
			else
			{
				echo 'window.location.reload(true);';
			}
		}
		else
		{
			echo 'var frm=document.createElement("FORM");frm.action=', $url, ';frm.method="post";document.body.appendChild(frm);frm.submit();';
		}
		exit();
	}
	/**
	 * Returns the full, URL path to the application
	 * @return string
	 */
	static function GetURL()	{return System::FullAppPath();}
	function __construct($config)
	{
		NolohInternal::SaveSessionState();

		if (strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false)
		{
			$_POST = array_map(
				function ($value)
				{
					return urldecode($value);
				},
				$_POST
			);
		}

		try
		{
			$GLOBALS['_NURLTokenMode'] = $config->URLTokenMode;
			$GLOBALS['_NTokenTrailsExpiration'] = $config->TokenTrailsExpiration;
			if (isset($_REQUEST['_NError']))
			{
				return print self::CreateError($_REQUEST['_NError']);
			}
			elseif (isset($_REQUEST['_NTimeout']))
			{
				return $this->HandleTimeout($_REQUEST['_NTimeout']);
			}
			elseif (isset($_GET['_NImageId']))
			{
				Image::MagicGeneration($_GET['_NImageId']);
			}
			elseif (isset($_GET['_NFileUpload']))
			{
				FileUpload::ShowInside($_GET['_NFileUpload'], $_GET['_NWidth'], $_GET['_NHeight']);
			}
			elseif (isset($_GET['_NFileRequest']))
			{
				File::SendRequestedFile($_GET['_NFileRequest']);
			}
			else
			{
				$requestingStrictlyHtml = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === 0);
				$hasRequestingVisit = (isset($_SESSION['_NVisit']) || isset($_POST['_NVisit']));
				$refererHost = parse_url(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null, PHP_URL_HOST);
				// Remove potential port off the end
				$realHost = preg_replace('/:\d*$/', '', $_SERVER['HTTP_HOST']);
				$refererHostMatchesRealHost = !$refererHost || ($refererHost === $realHost);
				$subsequentRun = !$requestingStrictlyHtml && $hasRequestingVisit && ($refererHostMatchesRealHost || UserAgent::IsPPCOpera());

				if ($subsequentRun)
				{
					// Proxy Error, originally done for AKPartners
					if ($_SESSION['_NOrigUserAgent'] != $_SERVER['HTTP_USER_AGENT'])
					{
						die();
					}
					$run = true;
					if (isset($_POST['_NSkeletonless']) && UserAgent::IsIE())
					{
						$this->HandleIENavigation();
					}
					elseif ($this->HandleForcedReset())
					{
						return;
					}
					$this->HandleDebugMode();
					if (isset($_SESSION['_NOmniscientBeing']))
					{
						$this->TheComingOfTheOmniscientBeing();
					}
					if (!empty($_POST['_NEventVars']))
					{
						$this->HandleEventVars();
					}
					$this->HandleClientChanges();
					if (!empty($_POST['_NFileUploadId']))
					{
						GetComponentById($_POST['_NFileUploadId'])->File = &$_FILES['_NFileUpload'];
					}
					foreach ($_SESSION['_NFiles'] as $key => $val)
					{
						if ($file = GetComponentById($key))
						{
							$file->File = new File($val);
						}
					}
					if (isset($_POST['_NTokenLink']))
					{
						$this->HandleLinkToTokens();
					}
					if (!empty($_POST['_NEvents']))
					{
						$this->HandleServerEvents();
					}
					foreach ($_SESSION['_NFiles'] as $key => $val)
					{
						unlink($_SESSION['_NFiles'][$key]['tmp_name']);
						if ($file = GetComponentById($key))
						{
							$file->File = null;
						}
						unset($_SESSION['_NFiles'][$key]);
					}
					if (isset($_POST['_NListener']))
					{
						Listener::Process($_POST['_NListener']);
					}
				}
				else
				{
					self::UnsetNolohSessionVars();
					$this->HandleFirstRun();
				}
			}
		}
		catch (SqlFriendlyException $e)
		{
			$e->CallBackExec();
		}

		if (!empty($config->MobileSsoValidationCallBack))
		{
			$class = $config->MobileSsoValidationCallBack[0];
			$function = $config->MobileSsoValidationCallBack[1];

			$functionExists = method_exists($class, $function);

			$continue = $functionExists
				? call_user_func(array($class, $function))
				: true;

			$run = (isset($run) && $run && $continue);
		}

		if (isset($run) && $run === true)
		{
			$this->Run();
		}
	}
	private static function InitRequestDetails()
	{
		self::$RequestDetails = array(
			'server_events'			=> '',
			'total_database_time'	=> 0,
			'total_session_io_time'	=> 0,
			'timestamp'				=> microtime(true)
		);
	}
	private static function CreateError($type)
	{
		if(!System::NOLOHPath())
			$_SESSION['_NPath'] = ComputeNOLOHPath();
		if(file_exists($path = (System::NOLOHPath() . '/Errors/' . $type . '.html')))
			$error = file_get_contents($path);
		/*elseif(file_exists($path = (System::NOLOHPath() . '/Errors/ErrorType.html')))
			$error = file_get_contents($path);*/
		else
			return 'Error: An undetermined problem arose displaying a custom NOLOH error.';
		$replace = array('{APP_PATH}' => System::FullAppPath());
		if($type == 'NoJavaScript')
		{
			$query = urlencode('Enable Javascript in ' . UserAgent::GetBrowser());
			$replace['{ENABLE_JS}'] = 'http://google.com/search?btnI=I%27m+Feeling+Lucky&q=' . $query . '&sourceid=navclient';
		}
		return str_replace(array_keys($replace), array_values($replace), $error);
	}
	/**
	 * @ignore
	 */
	static function UnsetNolohSessionVars()
	{
		unset($_SESSION['_NVisit'],
			$_SESSION['_NNumberOfComponents'],
			$_SESSION['_NShowStrategy'],
			$_SESSION['_NOmniscientBeing'],
			$_SESSION['_NControlQueueRoot'],
			$_SESSION['_NControlQueueDeep'],
			$_SESSION['_NControlInserts'],
			$_SESSION['_NFunctionQueue'],
			$_SESSION['_NPropertyQueue'],
			$_SESSION['_NScript'],
			$_SESSION['_NScriptSrc'],
			$_SESSION['_NScriptSrcs'],
			$_SESSION['_NGlobals'],
			$_SESSION['_NSingletons'],
			$_SESSION['_NFiles'],
			$_SESSION['_NFileSend'],
			$_SESSION['_NGarbage'],
			$_SESSION['_NURL'],
			$_SESSION['_NTokens'],
			$_SESSION['_NTokenChain'],
			$_SESSION['_NHighestZ'],
			$_SESSION['_NLowestZ'],
			$_SESSION['_NOrigUserAgent'],
			$_SESSION['_NMaxTouchPoints'],
			$_SESSION['_NBrowserPlatform']
		);
	}
	private function HandleFirstRun($trulyFirst=true)
	{
		if(isset($_COOKIE['_NPHPInfo']))
		{
			Cookie::Delete('_NPHPInfo');
			unset($_REQUEST['_NPHPInfo']);
			ob_start('_NPHPInfo');
			phpinfo();
			exit();
		}
		$home = null;

		static::SetNolohSessionVars();

		if ($home)
		{
			$_SESSION['_NUserDir'] = true;
		}
		UserAgent::LoadInformation();
		$config = Configuration::That();
		if ($config->ShowURLFilename !== 'Auto')
		{
			$fileName = basename($_SERVER['SCRIPT_FILENAME']);
			$appears = preg_match('/'.$fileName.'$/i', $fullPath = System::FullAppPath());
			if($appears != $config->ShowURLFilename)
			{
				header('HTTP/1.1 301 Moved Permanently');
				if($appears)
					header('Location: ' . rtrim($fullPath, $fileName));
				else
					header('Location: ' . rtrim($fullPath, '/') . '/' . $fileName);
				exit();
			}
		}
		if ($config->MobileAppURL && System::FullAppPath() != $config->MobileAppURL && UserAgent::GetDevice() === UserAgent::Mobile)
		{
			header('Location: ' . $config->MobileAppURL);
			exit();
		}
		if ($trulyFirst)
		{
			if (UserAgent::IsSpider() || UserAgent::GetBrowser() === UserAgent::Links)
			{
				$this->SearchEngineRun();
			}
			else
			{
				$config = Configuration::That();
				$className = $config->StartClass;
				try
				{
					$reflect = new ReflectionClass($className);
					if ($reflect->isInstantiable())
					{
						$webPage = new $className();
					}
				}
				catch (AbortConstructorException $e)
				{
					if ($e->getKey() == $GLOBALS['_NApp'])
					{
						WebPage::SkeletalShow($GLOBALS['_NTitle'], $config->UnsupportedURL, $GLOBALS['_NFavIcon'], $GLOBALS['_NMobileApp']);
						return;
					}
					else
					{
						$message = $e->getMessage();
					}
				}
				catch (Exception $e)
				{
					$message = $e->getMessage();
				}
				echo 'Critical error: Could not construct WebPage.<br>', $message ? $message : 'Please make sure the WebPage constructor is properly called from the ' . $className . ' constructor.';
				session_destroy();
			}
		}
	}
	private function HandleForcedReset()
	{
		$sessionVisit = isset($_SESSION['_NVisit']);
		$visitMismatch = (isset($_POST['_NVisit']) && $_SESSION['_NVisit'] != $_POST['_NVisit']);
		$paramsPassedUp = ((isset($_POST['_NVisit']) && isset($_SERVER['HTTP_REMOTE_SCRIPTING'])) || $_SESSION['_NVisit'] < 0 || isset($_GET['_NVisit']) || isset($_POST['_NListener']));

		if (!$sessionVisit || $visitMismatch || !$paramsPassedUp)
		{
			if(UserAgent::IsPPCOpera() || !isset($_POST['_NEvents']) || $_POST['_NEvents'] !== ('Unload@'.$_SESSION['_NStartUpPageId']))
			{
				if(isset($_SERVER['HTTP_REMOTE_SCRIPTING']) || isset($_POST['_NEvents']) || !isset($_SESSION['_NVisit']) || isset($_GET['_NWidth']))
				{
					self::Reset(false, false);
				}
				$this->TheComingOfTheOmniscientBeing();
				$webPage = WebPage::That();
				if($webPage !== null && !$webPage->GetUnload()->Blank())
					$webPage->Unload->Exec();
				self::UnsetNolohSessionVars();
				self::Start(Configuration::That());
			}
			return true;//!isset($_COOKIE['_NApp']);
		}
		if ($_SESSION['_NVisit'] === 0 && (isset($_GET['_NVisit']) && $_GET['_NVisit'] == 0) && count($_POST) === 0)	//FireBug bug
		{
			return true;
		}
		return false;
	}
	private function HandleIENavigation()
	{
		$srcs = $_SESSION['_NScriptSrcs'];
		self::UnsetNolohSessionVars();
		$this->HandleFirstRun(false);
		$_SESSION['_NScriptSrcs'] = $srcs;
		AddScript('_N.Visit=-1', Priority::High);
	}
	public static function EnableErrorHandlers()
	{
		$debugMode = Configuration::That()->DebugMode;
		$debugModeError = Configuration::That()->DebugModeError;

		if ($debugMode !== 'Unhandled')
		{
			$GLOBALS['_NDebugMode'] = $debugMode;
			$GLOBALS['_NDebugModeError'] = $debugModeError;

			ini_set('html_errors', false);
			set_error_handler('_NErrorHandler', error_reporting() | E_USER_NOTICE);
			set_exception_handler('_NExceptionHandler');
			ob_start('_NOBErrorHandler');
			if ($debugMode === System::Full)
			{
				ClientScript::AddNOLOHSource('DebugFull.js');
			}
		}
	}
	private function HandleDebugMode()
	{
		static::EnableErrorHandlers();
	}
	private function TheComingOfTheOmniscientBeing()
	{
		global $OmniscientBeing;
		System::BeginBenchmarking('_N/Application::TheComingOfTheOmniscientBeing');
		$OmniscientBeing = unserialize(defined('FORCE_GZIP') ? gzuncompress($_SESSION['_NOmniscientBeing']) : $_SESSION['_NOmniscientBeing']);
		self::$RequestDetails['total_session_io_time'] += System::Benchmark('_N/Application::TheComingOfTheOmniscientBeing');
		unset($_SESSION['_NOmniscientBeing']);
		$idArrayStr = '';
		$idShftWithArr = array();
		foreach($_SESSION['_NGarbage'] as $id => $nothing)
		{
			$control = &$OmniscientBeing[$id];
			if($control instanceof Control && !isset($_SESSION['_NGarbage'][$control->GetParentId()]) && $control->GetShowStatus()!==0)
			{
				$idArrayStr .= '\'' . $id . '\',';
				if($shifts = $control->_NGetShifts())
					foreach($shifts as $shift)
						if($shift[1] === 7 && !isset($_SESSION['_NGarbage'][$shftId = $shift[0]]) && !isset($idShftWithArr[$shftId]))
							$idShftWithArr[$shftId] = '\'' . $shftId . '\'';
			}
			unset($OmniscientBeing[$id]);
		}
		if($idArrayStr !== '')
		{
			AddScript('_NGCAsc([' . rtrim($idArrayStr, ',') . '])', Priority::Low);
			if($idShftWithArr)
				AddScript('_NShftGC(' . implode(',', $idShftWithArr) . ')', Priority::Low);
		}
		$_SESSION['_NGarbage'] = array();
		$this->WebPage = GetComponentById($_SESSION['_NStartUpPageId']);
		if(isset($_SESSION['_NTokenChain']))
			URL::$TokenChain = unserialize($_SESSION['_NTokenChain']);
	}
	private function HandleEventVars()
	{
		$varInfo = explode('~d0~', $_POST['_NEventVars']);
		$numInfo = count($varInfo);
		$i = -1;
		while(++$i < $numInfo)
		{
			switch($name = $varInfo[$i])
			{
				case 'Caught':
					Event::$Caught = $this->ExplodeDragCatch($varInfo[++$i]);
					break;
				case 'ContextMenuSource':
					ContextMenu::$Source = GetComponentById($varInfo[++$i]);
					break;
				case 'FlashArgs':
					Event::$FlashArgs = explode('~d3~', $varInfo[++$i]);
					break;
				case 'FocusedComponent':
					Event::$FocusedComponent = GetComponentById($varInfo[++$i]);
					break;
				default:
					Event::$$name = $varInfo[++$i];
			}
		}
	}
	private function HandleClientChanges()
	{
		if(!empty($_POST['_NChanges']))
		{
			$lookUp = array(
				'left' => 'SetLeft',
				'top' => 'SetTop',
				'width' => 'SetWidth',
				'height' => 'SetHeight',
				'zIndex' => 'SetZIndex',
				'background' => 'SetBackColor',
				'color' => 'SetColor',
				'value' => 'Set_NText',
				'innerHTML' => 'Set_NText',
				'selectedIndex' => 'SetSelectedIndex',
				'className' => 'SetCSSClass',
				'name' => 'SetHTMLName',
				'src' => 'SetSrc',
				'scrollLeft' => 'SetScrollLeft',
				'scrollTop' => 'SetScrollTop'
			);
			$componentChanges = explode('~d0~', stripslashes($_POST['_NChanges']));
			$numComponents = count($componentChanges);
			for($i = 0; $i < $numComponents; ++$i)
			{
				$changes = explode('~d1~', $componentChanges[$i]);
				if($component = &GetComponentById($changes[0]))
				{
					$GLOBALS['_NQueueDisabled'] = $changes[0];
					$changeCount = count($changes);
					$j = 0;
					if($component->GetSecure())
					{
						$oldValue = $component->GetValue();
						while(++$j < $changeCount)
							$component->{isset($lookUp[$prop = $changes[$j]]) ? $lookUp[$prop] : ('Set'.$prop)}($changes[++$j]);
						if($oldValue != $component->GetValue())
							if(isset($GLOBALS['_NResetSecureValues']))
								$GLOBALS['_NResetSecureValues'][$component->Id] = $oldValue;
							else
								$GLOBALS['_NResetSecureValues'] = array($component->Id => $oldValue);
					}
					else
						while(++$j < $changeCount)
							$component->{isset($lookUp[$prop = $changes[$j]]) ? $lookUp[$prop] : ('Set'.$prop)}($changes[++$j]);
				}
			}
		}
		$GLOBALS['_NQueueDisabled'] = null;
	}
	private function HandleServerEvents()
	{
		$events = explode(',', $_POST['_NEvents']);
		$eventCount = count($events);
		$GLOBALS['_NSEFromClient'] = true;
		for($i=0; $i<$eventCount; ++$i)
		{
			$eventInfo = explode('@', $events[$i]);
			if($eventInfo[1] === $_SESSION['_NStartUpPageId'] && $eventInfo[0] === 'Unload')
			{
				Cookie::Delete(session_name());
				session_destroy();
				exit();
			}
			if($obj = &GetComponentById($eventInfo[1]))
			{
				$execClientEvents = false;
				$obj->GetEvent($eventInfo[0])->Exec($execClientEvents, false, true);
			}
			elseif(($pos = strpos($eventInfo[1], 'i')) !== false)
				GetComponentById(substr($eventInfo[1], 0, $pos))->ExecEvent($eventInfo[0], $eventInfo[1]);
		}
		unset($GLOBALS['_NSEFromClient']);
	}
	/**
	 * @ignore
	 */
	public function HandleTokens()
	{
		if($GLOBALS['_NURLTokenMode'] == 0)
			return;
		unset($_GET['_NVisit'], $_GET['_NApp'], $_GET['_NWidth'], $_GET['_NHeight'], $_GET['_NTimeZone'], $_GET['_NMaxTouchPoints'], $_GET['_NBrowserPlatform']);
		if(isset($_GET['_escaped_fragment_']))
			parse_str(urldecode($_GET['_escaped_fragment_']), $_GET);
		if($GLOBALS['_NURLTokenMode'] == 1)
		{
			URL::$TokenChain = $tokenChain = new ImplicitArrayList('URL', 'AddChainToken', 'RemoveChainTokenAt', 'ClearChainTokens');
			if(reset($_GET) === '')
			{
				$tokenChain->Elements = explode('/',
					trim((($ampPos = strpos($_SERVER['QUERY_STRING'], '&')) === false)
						? $_SERVER['QUERY_STRING']
						: substr($_SERVER['QUERY_STRING'], 0, strpos($_SERVER['QUERY_STRING'],
							'&')), '/'));
				unset($_GET[key($_GET)]);
			}
			$_SESSION['_NTokenChain'] = serialize($tokenChain);
			$_SESSION['_NTokens'] = $_GET;
		}
		elseif($GLOBALS['_NURLTokenMode'] == 2)
		{
			$keys = array_keys($_GET);
			$ubound = count($keys) - 1;
			for($i=0; $i<$ubound; ++$i)
				$_SESSION['_NTokens'][$keys[$i]] = $_GET[$keys[$i]];
			if($_GET[$keys[$ubound]] != '')
				$_SESSION['_NTokens'][$keys[$ubound]] = $_GET[$keys[$ubound]];
			else
			{
				$split = explode('&', base64_decode($keys[$ubound]));
				$count = count($split);
				for($i=0; $i<$count; ++$i)
				{
					$split2 = explode('=', $split[$i].'=');
					$_SESSION['_NTokens'][$split2[0]] = $split2[1];
				}
			}
		}
		$query = explode('?', $_SERVER['REQUEST_URI']);
		if(isset($query[1]) && $query[1]!=$_SERVER['QUERY_STRING'])
		{
			$query = $query[1];
			$split = explode('&', $query);
			$ubound = count($split) - 4;
			for($i=0; $i<$ubound; ++$i)
			{
				$split2 = explode('=', $split[$i]);
				$_SESSION['_NTokens'][$split2[0]] = $split2[1];
			}
			$split2 = explode('=', $split[$ubound]);
			if($GLOBALS['_NURLTokenMode'] == 1 || $split2[1] != '')
				$_SESSION['_NTokens'][$split2[0]] = $split2[1];
			else
			{
				$split = explode('&', base64_decode($split2[0]));
				$count = count($split);
				for($i=0; $i<$count; ++$i)
				{
					$split2 = explode('=', $split[$i].'=');
					$_SESSION['_NTokens'][$split2[0]] = $split2[1];
				}
			}
		}
	}
	private function HandleLinkToTokens()
	{
		URL::QueueUpdateTokens();
		$this->HandleTokens();
	}
	private function HandleTimeout($action)
	{
		//global $OmniscientBeing;
		//$gzip = defined('FORCE_GZIP');
		//$_SESSION['_NOmniscientBeing'] = $gzip ? gzcompress(serialize($OmniscientBeing),1) : serialize($OmniscientBeing);
		if($action === 'Ping')
			echo 'Pong';
		elseif($action === 'Die')
		{
			echo 'Applicated timed out.';
			session_destroy();
		}
	}
	private function Run()
	{
		global $OmniscientBeing;
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		//header('Cache-Control: no-store');
		if (++$_SESSION['_NVisit'] === 0)
		{
			global $_NShowStrategy, $_NWidth, $_NHeight, $_NTimeZone;
			$_NWidth = isset($_GET['_NWidth']) ? $_GET['_NWidth'] : 1024;
			$_NHeight = isset($_GET['_NHeight']) ? $_GET['_NHeight'] : 768;
			$_NTimeZone = isset($_GET['_NTimeZone']) ? $_GET['_NTimeZone'] : date_default_timezone_get();
			$_SESSION['_NMaxTouchPoints'] = isset($_GET['_NMaxTouchPoints']) ? intval($_GET['_NMaxTouchPoints']) : 0;
			$_SESSION['_NBrowserPlatform'] = isset($_GET['_NBrowserPlatform']) ? $_GET['_NBrowserPlatform'] : '';
			$this->HandleTokens();
			$_NShowStrategy = (
				!empty($_SESSION['_NShowStrategy']) ||
				(isset($_GET['_NStrategy']) && $_GET['_NStrategy'] === 'Show') ||
				(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != System::FullAppPath())
			);
			$className = Configuration::That()->StartClass;
			$this->WebPage = new $className();
			if ($_NShowStrategy)
			{
				$_SESSION['_NShowStrategy'] = true;
				$this->WebPage->Show();
			}
			else
			{
				return $this->WebPage->NoScriptShow('');
			}
			AddScript('_N.Request=null;', Priority::Low);
		}
		header('Content-Type: text/javascript; charset=UTF-8');
		if (isset($GLOBALS['_NTokenUpdate']) && (!isset($_POST['_NSkeletonless']) || !UserAgent::IsIE()))
		{
			$tokenString = URL::UpdateTokens();
		}
		else
		{
			$tokenString = $_SERVER['QUERY_STRING'];
		}
		NolohInternal::Queues();
		// TODO: I think this should be Application::ObEndAll() but I am not bold enough to make the change yet
		ob_end_clean();
		$gzip = defined('FORCE_GZIP');
		if ($gzip)
		{
			ob_start('ob_gzhandler');
		}
		echo $_SESSION['_NScriptSrc'], '/*_N*/', $_SESSION['_NScript'][0], $_SESSION['_NScript'][1], $_SESSION['_NScript'][2];
		$_SESSION['_NScriptSrc'] = '';
		$_SESSION['_NScript'] = array('', '', '');

		System::BeginBenchmarking('_N/Application::Run');
		$serializedSession = serialize($OmniscientBeing);
		$_SESSION['_NOmniscientBeing'] = $gzip ? gzcompress($serializedSession, 1) : $serializedSession;
		$benchmark = System::Benchmark('_N/Application::Run');

		$requestDetails = &self::UpdateRequestDetails();
		$requestDetails['total_session_io_time'] += $benchmark;
		$requestDetails['session_strlen'] = strlen($serializedSession);
		$requestDetails['tokens'] = $tokenString;
		if ($this->WebPage && method_exists($this->WebPage, 'ProcessRequestDetails') && !empty($requestDetails) && $requestDetails['visit'] > 0)
		{
			$this->WebPage->ProcessRequestDetails($requestDetails);
		}
		if ($gzip)
		{
			ob_end_flush();
		}
		flush();

		DataConnection::CloseAll(true);
		$GLOBALS['_NGarbage'] = true;
		unset($OmniscientBeing, $GLOBALS['OmniscientBeing']);
		unset($GLOBALS['_NGarbage']);
	}
	public static function &UpdateRequestDetails()
	{
		global $OmniscientBeing;
		$requestDetails = &self::$RequestDetails;

		$requestDetails['visit'] = $_SESSION['_NVisit'];
		$requestDetails['components'] = count($OmniscientBeing);
		$requestDetails['session_id'] = session_id();
		$requestDetails['memory_peak_usage'] = memory_get_peak_usage(true) / 1048576; // 1024^2

		if (substr(strtoupper(PHP_OS), 0, 3) === 'WIN')
		{
			exec('wmic OS get FreePhysicalMemory /Value', $output);
			$memoryInfo = implode($output);
			$freeMemory = (int)(substr($memoryInfo, strpos($memoryInfo, '=') + 1));
		}
		else
		{
			$memoryInfo = exec('free | grep buffers/cache');
			preg_match('/(?:-\/\+ buffers\/cache:\s*\w+\s*)(\d*)/', $memoryInfo, $matches);
			$freeMemory = isset($matches[1]) ? $matches[1] : null;
		}
		$requestDetails['free_memory'] = $freeMemory / 1000;

		$requestDetails['total_server_time'] = (int)(1000 * (microtime(true) - $requestDetails['timestamp']));
		unset($requestDetails['timestamp']);

		return $requestDetails;
	}
	private function SearchEngineRun()
	{
		$GLOBALS['_NShowStrategy'] = false;
		if(!isset($GLOBALS['_NAutoStart']))
		{
			$GLOBALS['_NApplication'] = $this;
			return;
		}
		$this->HandleTokens();
		$config = Configuration::That();
		if($config->SpiderSSL !== 'Auto' && ((URL::GetProtocol() === 'https') != $config->SpiderSSL))
		{
			header('HTTP/1.1 301 Moved Permanently');
			if($config->SpiderSSL)
				header('Location: ' . str_replace('http', 'https', System::FullAppPath()));
			else
				header('Location: ' . str_replace('https', 'http', System::FullAppPath()));
			exit();
		}
		global $_NSETokenChain, $_NSETokens;
		$_NSETokenChain = array();
		$_NSETokens = array();
		++$_SESSION['_NVisit'];
		$className = $config->StartClass;
		$this->WebPage = new $className();
		$_SESSION['_NStartUpPageId'] = $this->WebPage->Id;
		$tokenLinks = '';
		$file = getcwd().'/NOLOHSearchTrails.dat';
		if(file_exists($file))
		{
			$expiration = $GLOBALS['_NTokenTrailsExpiration'] * 86400;
			$tokenString = URL::TokenString(URL::$TokenChain, $_SESSION['_NTokens']);
			$trails = unserialize(base64_decode(file_get_contents($file)));
			if($trails !== false && isset($trails[$tokenString]))
				foreach($trails[$tokenString] as $key => $info)
					if(time()-$info[1] < $expiration)
					{
						$href = $key[0]=='?'?(System::FullAppPath().$key):$key;
						$pos = strpos($href, '?');
						if($pos !== false)
							$href = substr($href, 0, $pos) . (UserAgent::GetName()===UserAgent::Googlebot?'#!/':'?') . htmlspecialchars(substr($href, $pos+1));
						$tokenLinks .= '<LI><A href="' . $href . '">' . $info[0] . '</A></LI> ';
					}
		}
		NolohInternal::NonstandardShowQueues();
		$canonicalURL = '';
		$chainCount = count($_NSETokenChain);
		$tokenCount = count($_NSETokens);
		if($chainCount || $tokenCount)
		{
			for($i=$chainCount-1; $i && (!isset($_NSETokenChain[$i]) || $_NSETokenChain[$i]!==false); --$i);
			for(; $i<$chainCount && (!isset($_NSETokenChain[$i]) || $_NSETokenChain[$i]!==true); ++$i);
			$count = count(URL::$TokenChain);
			for(; $i<$count; ++$i)
				unset(URL::$TokenChain[$i]);
			foreach($_NSETokens as $key => $val)
				unset($_SESSION['_NTokens']);
			$tokenString = URL::TokenString(URL::$TokenChain, $_SESSION['_NTokens']);
			$canonicalURL = System::FullAppPath() . ($tokenString ? (UserAgent::GetName()===UserAgent::Googlebot?'#!/':'?') . $tokenString : '');
		}
		$this->WebPage->CanonicalUrl = $canonicalURL;
		$this->WebPage->SearchEngineTokenLinks = $tokenLinks;
		$this->WebPage->SearchEngineShow();
		ob_flush();
		DataConnection::CloseAll(false);
		session_destroy();
	}
	private function ExplodeDragCatch($objectsString)
	{
		$objs = array();
		$objectsIdArray = explode(',', $objectsString);
		$objectsCount = count($objectsIdArray);
		for($i=0; $i<$objectsCount; ++$i)
			$objs[] = GetComponentById($objectsIdArray[$i]);
		return $objs;
	}
	/*
	function __destruct()
	{
		if(isset($GLOBALS['_NDEATH']))
		{
			ob_end_flush();
			//echo "eh";
		}
	}
	*/
	static function SetNolohSessionVars()
	{
		$_SESSION['_NVisit'] = -1;
		$_SESSION['_NNumberOfComponents'] = 0;
		$_SESSION['_NShowStrategy'] = 0;
		$_SESSION['_NControlQueueRoot'] = array();
		$_SESSION['_NControlQueueDeep'] = array();
		$_SESSION['_NControlInserts'] = array();
		$_SESSION['_NFunctionQueue'] = array();
		$_SESSION['_NPropertyQueue'] = array();
		$_SESSION['_NScript'] = array('', '', '');
		$_SESSION['_NScriptSrc'] = '';
		$_SESSION['_NScriptSrcs'] = array();
		$_SESSION['_NGlobals'] = array();
		$_SESSION['_NSingletons'] = array();
		$_SESSION['_NFiles'] = array();
		$_SESSION['_NFileSend'] = array();
		$_SESSION['_NGarbage'] = array();
		$_SESSION['_NTokens'] = array();
		$_SESSION['_NHighestZ'] = 0;
		$_SESSION['_NLowestZ'] = 0;
		$_SESSION['_NOrigUserAgent'] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION['_NURL'] = System::RequestUri();
		$_SESSION['_NPath'] = ComputeNOLOHPath();
		$_SESSION['_NRPath'] = NOLOHConfig::NOLOHURL ? NOLOHConfig::NOLOHURL : System::GetRelativePath(dirname($_SERVER['SCRIPT_FILENAME']), $_SESSION['_NPath'], '/');
		$_SESSION['_NRAPath'] = rtrim(
			NOLOHConfig::NOLOHURL ? NOLOHConfig::NOLOHURL :
				/*(str_repeat('../', substr_count($_SESSION['_NURL'], '/', strlen(dirname($_SESSION['_NURL']))+1))
					. (($home = (strpos(getcwd(), $selfDir = dirname($_SERVER['PHP_SELF']))===false)) ?
					GetRelativePath($selfDir, '/') . GetRelativePath($_SERVER['DOCUMENT_ROOT'], $_SESSION['_NPath']) :
					$_SESSION['_NRPath']));*/
				(($home = (strpos(getcwd(), $selfDir = dirname($_SERVER['PHP_SELF']))===false))
					? System::GetRelativePath($selfDir, '/', '/') . System::GetRelativePath($_SERVER['DOCUMENT_ROOT'], $_SESSION['_NPath'], '/')
					: $_SESSION['_NRPath']), '/');
	}

	/**
	 * @ignore
	 */
	public static function ObEndAll()
	{
		while (ob_get_level())
		{
			ob_end_clean();
		}
	}
}

// DEPRECATED! Use Application::SetStartUpPage instead.
/**
 * @ignore
 */
function SetStartUpPage($className, $unsupportedURL=null, $urlTokenMode=URL::Display, $tokenTrailsExpiration=14, $debugMode=true)
{
	Application::Start(new Configuration($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode));
}

register_shutdown_function(array('Application', 'AutoStart'));

?>
