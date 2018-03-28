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
final class Application extends Object
{
	/**
	 * @ignore
	 */
	const Name = '@APPNAME';
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
	 */
	public static function &Start($dotDotDot=null)
	{
		if(empty($GLOBALS['_NApp']))
		{
			self::InitRequestDetails();
            if(!UserAgent::IsCLI() && getcwd() !== $GLOBALS['_NCWD'] && !chdir($GLOBALS['_NCWD']))
            	exit('Error with working directory. This could be caused by two reasons: you do a chdir in main after including the kernel, or your server is not compatible with not allowing a Application::Start call.');
			if(isset($_REQUEST['_NApp']))
				ini_set('session.use_cookies', 0);
			else
				session_set_cookie_params(30);
			System::BeginBenchmarking();
			session_name('_NS');
			session_id(hash('md5', $GLOBALS['_NApp'] = (isset($_REQUEST['_NApp']) ? $_REQUEST['_NApp'] : (empty($_COOKIE['_NAppCookie']) ? rand(1, 99999999) : $_COOKIE['_NAppCookie']))));
			//session_name(hash('md5', $GLOBALS['_NApp'] = (isset($_REQUEST['_NApp']) ? $_REQUEST['_NApp'] : (empty($_COOKIE['_NAppCookie']) ? rand(1, 99999999) : $_COOKIE['_NAppCookie']))));
			session_start();
			self::$RequestDetails['total_session_io_time'] += System::Benchmark();
			if(isset($_SESSION['_NConfiguration']))
				$config = $_SESSION['_NConfiguration'];
			else 
			{
				$args = func_get_args();
				if(count($args) === 1 && $args[0] instanceof Configuration)
					$config = $args[0];
				else 
				{
					$reflect = new ReflectionClass('Configuration');
					$config = $reflect->newInstanceArgs($args);
				}
				$_SESSION['_NConfiguration'] = &$config;
			}
            if($config->StartClass)
			    new Application($config);
			return $config;
		}
		else
//			return Configuration::That();
			return $_SESSION['_NConfiguration'];
		return false;
	}
	/**
	 * Resets Application to original state
	 * @param boolean $clearURLTokens Whether the URL Tokens will be cleared out
	 * @param boolean $clearSessionVariables Whether the session will be cleared out
	 * @param boolean $alert If a string, it will be alerted to the user before resetting
	 */
	public static function Reset($clearURLTokens = true, $clearSessionVariables = true, $alert = false)
	{
		if(isset($GLOBALS['_NDebugMode']))
			ob_end_clean();
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
		if ($browser === 'ie' || $browser === 'ff' || $browser === 'ch')
		{
			if($clearURLTokens)
				echo 'window.location.replace(', $url, ');';
			else
				echo 'window.location.reload(true);';
		}
		else
			echo 'var frm=document.createElement("FORM");frm.action=', $url, ';frm.method="post";document.body.appendChild(frm);frm.submit();';
		exit();
	}
	/**
	 * Returns the full, URL path to the application
	 * @return string
	 */
	static function GetURL()	{return System::FullAppPath();}
	private function Application($config)
	{
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
			elseif (isset($_GET['_NImage']))
			{
				if (empty($_GET['_NWidth']))
				{
					Image::MagicGeneration($_GET['_NImage'], $_GET['_NClass'], $_GET['_NFunction'], $_GET['_NParams']);
				}
				else
				{
					Image::MagicGeneration($_GET['_NImage'], $_GET['_NClass'], $_GET['_NFunction'], $_GET['_NParams'], $_GET['_NWidth'], $_GET['_NHeight']);
				}
			}
			elseif (isset($_GET['_NFileUpload']))
			{
				FileUpload::ShowInside($_GET['_NFileUpload'], $_GET['_NWidth'], $_GET['_NHeight']);
			}
			elseif (isset($_GET['_NFileRequest']))
			{
				File::SendRequestedFile($_GET['_NFileRequest']);
			}
			elseif ((!isset($_SERVER['HTTP_ACCEPT']) || strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== 0) && (isset($_SESSION['_NVisit']) || isset($_POST['_NVisit'])) && (!($host = parse_url((isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null), PHP_URL_HOST)) || (UserAgent::IsPPCOpera()) || $host == (($pos = (strpos($_SERVER['HTTP_HOST'], ':'))) !== false ? substr($_SERVER['HTTP_HOST'], 0, $pos) : $_SERVER['HTTP_HOST'])))
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
					GetComponentById($key)->File = new File($val);
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
					GetComponentById($key)->File = null;
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
		catch (SqlFriendlyException $e)
		{
			$e->CallBackExec();
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
			$_SESSION['_NOrigUserAgent']
		);
	}
	private function HandleFirstRun($trulyFirst=true)
	{
		if(isset($_COOKIE['_NPHPInfo']))
		{
			setcookie('_NPHPInfo', false);
			unset($_COOKIE['_NPHPInfo'], $_REQUEST['_NPHPInfo']);
			ob_start('_NPHPInfo');
			phpinfo();
			exit();
		}
		$home = null;
		$_SESSION['_NVisit'] = -1;
		$_SESSION['_NNumberOfComponents'] = 0;
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
		$_SESSION['_NURL'] = rtrim($_SERVER['QUERY_STRING'] ? rtrim($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']) : $_SERVER['REQUEST_URI'], '?');
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
		if($home)
			$_SESSION['_NUserDir'] = true;
		UserAgent::LoadInformation();
		$config = Configuration::That();
		if($config->ShowURLFilename !== 'Auto')
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
		if($config->MobileAppURL && System::FullAppPath()!=$config->MobileAppURL && UserAgent::GetDevice()===UserAgent::Mobile)
		{
			header('Location: ' . $config->MobileAppURL);
			exit();
		}
		if($trulyFirst)
			if(UserAgent::IsSpider() || UserAgent::GetBrowser() === UserAgent::Links)
				$this->SearchEngineRun();
			else 
			{
				$config = Configuration::That();
				$className = $config->StartClass;
				try
				{
					$webPage = new $className();
				}
				catch(Exception $e)
				{
					if($e->getCode() == $GLOBALS['_NApp'])
					{
						if(empty($_GET))
							setcookie('_NAppCookie', $GLOBALS['_NApp']);
						WebPage::SkeletalShow($GLOBALS['_NTitle'], $config->UnsupportedURL, $GLOBALS['_NFavIcon'], $GLOBALS['_NMobileApp']);
						return;
					}
					else 
					{
						$message = $e->getMessage();
					}
				}
				echo 'Critical error: Could not construct WebPage.<br>', $message ? $message : 'Please make sure the WebPage constructor is properly called from the ' . $className . ' constructor.';
				session_destroy();
			}
	}
	private function HandleForcedReset()
	{
		if(!isset($_SESSION['_NVisit']) || 
			(isset($_POST['_NVisit']) && $_SESSION['_NVisit'] != $_POST['_NVisit']) ||
			((!isset($_POST['_NVisit']) || !isset($_SERVER['HTTP_REMOTE_SCRIPTING'])) && $_SESSION['_NVisit']>=0 && !isset($_GET['_NVisit']) && !isset($_POST['_NListener'])))
		{
			if(UserAgent::IsPPCOpera() || !isset($_POST['_NEvents']) || $_POST['_NEvents'] !== ('Unload@'.$_SESSION['_NStartUpPageId']))
			{
				if(isset($_SERVER['HTTP_REMOTE_SCRIPTING']) || isset($_POST['_NEvents']) || !isset($_SESSION['_NVisit']) || isset($_GET['_NWidth']))
					self::Reset(false, false);
				$this->TheComingOfTheOmniscientBeing();
				$webPage = WebPage::That();
				if($webPage !== null && !$webPage->GetUnload()->Blank())
					$webPage->Unload->Exec();
				self::UnsetNolohSessionVars();
				self::Start(Configuration::That());
			}
			return true;//!isset($_COOKIE['_NApp']);
		}
		if($_SESSION['_NVisit']===0 && (isset($_GET['_NVisit']) && $_GET['_NVisit']==0) && count($_POST)===0)	//FireBug bug
			return true;
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
	private function HandleDebugMode()
	{
		$debugMode = Configuration::That()->DebugMode;
		if($debugMode !== 'Unhandled')
		{
			$GLOBALS['_NDebugMode'] = $debugMode;
			ini_set('html_errors', false);
			set_error_handler('_NErrorHandler', error_reporting() | E_USER_NOTICE);
			set_exception_handler('_NExceptionHandler');
			ob_start('_NOBErrorHandler');
			if($debugMode === System::Full)
				ClientScript::AddNOLOHSource('DebugFull.js');
		}
	}
	private function TheComingOfTheOmniscientBeing()
	{
		global $OmniscientBeing;
		System::BeginBenchmarking();
		$OmniscientBeing = unserialize(defined('FORCE_GZIP') ? gzuncompress($_SESSION['_NOmniscientBeing']) : $_SESSION['_NOmniscientBeing']);
		self::$RequestDetails['total_session_io_time'] += System::Benchmark();
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
				$params = session_get_cookie_params();
			    setcookie(session_name(), '', time() - 42000,
			        $params["path"], $params["domain"],
			        $params["secure"], $params["httponly"]);
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
		unset($_GET['_NVisit'], $_GET['_NApp'], $_GET['_NWidth'], $_GET['_NHeight']);
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
			global $_NShowStrategy, $_NWidth, $_NHeight;
			if(isset($_COOKIE['_NAppCookie']))
				setcookie('_NAppCookie', $_COOKIE['_NAppCookie'], 1);
			$_NWidth = isset($_GET['_NWidth']) ? $_GET['_NWidth'] : 1024;
			$_NHeight = isset($_GET['_NHeight']) ? $_GET['_NHeight'] : 768;
			$this->HandleTokens();
			$_NShowStrategy = (empty($_COOKIE['_NAppCookie']) || (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != System::FullAppPath()));
			$className = Configuration::That()->StartClass;
			$this->WebPage = new $className();
			if($_NShowStrategy)
				$this->WebPage->Show();
			else
				return $this->WebPage->NoScriptShow();
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
		ob_end_clean();
		$gzip = defined('FORCE_GZIP');
		if($gzip)
			ob_start('ob_gzhandler');
		echo $_SESSION['_NScriptSrc'], '/*_N*/', $_SESSION['_NScript'][0], $_SESSION['_NScript'][1], $_SESSION['_NScript'][2];
		$_SESSION['_NScriptSrc'] = '';
		$_SESSION['_NScript'] = array('', '', '');
		
		System::BeginBenchmarking();
		$serializedSession = serialize($OmniscientBeing);
		$_SESSION['_NOmniscientBeing'] = $gzip ? gzcompress($serializedSession, 1) : $serializedSession;
		$benchmark = System::Benchmark();

		$requestDetails = &self::UpdateRequestDetails();
		$requestDetails['total_session_io_time'] += $benchmark;
		$requestDetails['session_strlen'] = strlen($serializedSession);
		$requestDetails['tokens'] = $tokenString;
		if ($this->WebPage && method_exists($this->WebPage, 'ProcessRequestDetails') && !empty($requestDetails))
		{
			$this->WebPage->ProcessRequestDetails($requestDetails);
		}
		if ($gzip)
		{
			ob_end_flush();
		}
		flush();
		
		if (isset($_SESSION['_NDataLinks']))
		{
			foreach ($_SESSION['_NDataLinks'] as $connection)
			{
				$connection->ForceCommit();
				$connection->Close();
			}
		}
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
			$freeMemory = $matches[1];
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
		$this->WebPage->SearchEngineShow($canonicalURL, '<UL>'.$tokenLinks.'</UL>');
		ob_flush();
		if(isset($_SESSION['_NDataLinks']))
			foreach($_SESSION['_NDataLinks'] as $connection)
				$connection->Close();
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