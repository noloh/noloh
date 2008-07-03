<?php
/**
 * @package System
 */

global $OmniscientBeing;

// DEPRECATED! Use Application::SetStartUpPage instead.
function SetStartUpPage($className, $unsupportedURL='', $urlTokenMode=URL::Display, $tokenTrailsExpiration=604800, $debugMode=true)
{
	new Application($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode);
}

/**
* @ignore
*/
function _NOBErrorHandler($buffer)
{
	if(strpos($buffer, '<title>phpinfo()</title>') !== false)
		trigger_error('~_NINFO~');
	elseif(ereg('([^:]+): (.+) in (.+) on line ([0-9]+)', $buffer, $matches))
		trigger_error('~OB~'.$matches[1].'~'.$matches[2].'~'.$matches[3].'~'.$matches[4]);
}

/**
 * @ignore
 */
function _NErrorHandler($number, $string, $file, $line)
{
	ob_end_clean();
	if(strpos($string, '~OB~')===0)
	{
		$splitStr = explode('~', $string);
		$string = $splitStr[3];
		$file = $splitStr[4];
		$line = $splitStr[5];
	}
	elseif($string == '~_NINFO~')
	{
		$_SESSION['_NPHPInfo'] = true;
		Application::Reset(true, false);
	}
	if(defined('FORCE_GZIP') && !in_array('ob_gzhandler', ob_list_handlers(), true))
	{
		ob_start('ob_gzhandler');
		++$_SESSION['_NVisit'];
	}
	error_log($message = (str_replace(array("\n","\r",'"'),array('\n','\r','\"'),$string)."\\nin $file\\non line $line"));
	print('/*~NScript~*/alert("' . ($GLOBALS['_NDebugMode'] ? "A server error has occurred:\\n\\n$message" : 'An application error has occurred.') . '");');
	global $OmniscientBeing;
	$_SESSION['_NScript'] = array('', '', '');
	$_SESSION['_NScriptSrc'] = '';
	$_SESSION['_NOmniscientBeing'] = defined('FORCE_GZIP') ? gzcompress(serialize($OmniscientBeing),1) : serialize($OmniscientBeing);
    ob_end_flush();
    exit();
}
/**
 * @ignore
 */
function _NPHPInfo($info)
{
	$info = str_replace(array("\n", "\r", "'"), array('','',"\\'"), $info);
	$loc = strpos($info, '</table>') + 8;
	$text = substr($info, 0, $loc) .
		'<br><table border="0" cellpadding="3" width="600"><tr class="h"><td><a href="http://www.noloh.com"><img border="0" src="' . NOLOHConfig::GetNOLOHPath() . 'Images/nolohLogo.png" alt="NOLOH Logo" /></a><h1 class="p">NOLOH Version '.GetNOLOHVersion().'</h1></td></tr></table><div id="N2"></div><div id="N3"></div>' .
		substr($info, $loc);
	session_destroy();
	session_unset();
	return $text;
}
/**
* @package System
*/
final class Application extends Object
{
	private $WebPage;

	/**
	 * Specifies which WebPage class will serve as the initial start-up point of your application
	 * @param string $className The name of the class that extends WebPage, as a string
	 * @param string $unsupportedURL If a user's browser is not supported, or he does not have JavaScript enabled, this will be the URL of the error page to which he is navigated
	 * @param mixed $urlTokenMode Specifies how URL tokens are displayed. Possible values are URL::Display, URL::Encrypt, or URL::Disable
	 * @param integer $tokenTrailsExpiration Specifies the number of seconds until token search trails file expires. Please see Search Engine Friendly documentation for more information
	 * @param mixed $debugMode Specifies the level of error-handling: true gives specific errors for developers, false gives generic errors for users, and System::Unhandled does not fail gracefully but crashes
	 */
	public static function SetStartUpPage($className, $unsupportedURL='', $urlTokenMode=URL::Display, $tokenTrailsExpiration=604800, $debugMode=true)
	{
		new Application($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode);
	}
	
	/**
	 * Resets Application to original state
	 * @param boolean $clearURLTokens Whether the URL Tokens will be cleared out
	 * @param boolean $clearSessionVariables Whether the session will be cleared out
	 */
	public static function Reset($clearURLTokens = true, $clearSessionVariables = true)
	{
		if(isset($GLOBALS['_NDebugMode']))
			ob_end_clean();
        print('/*~NScript~*/');
        $webPage = GetComponentById('N1');
        if($webPage != null && !$webPage->GetUnload()->Blank())
        {
            print('window.onunload=null;');
            $webPage->Unload->Exec();
        }
		if($clearSessionVariables)
		{
			session_destroy();
			session_unset();
		}
		else
			self::UnsetNolohSessionVars();
		$url = $clearURLTokens ? ('"'.$_SERVER['PHP_SELF'].'"') : 'location.href';
		$browser = GetBrowser();
		if($browser=='ie' || $browser=='ff')
			if($clearURLTokens)
				print('window.location.replace('.$url.');');
			else
				print('window.location.reload(true);');
		else
			print('var frm=document.createElement("FORM");frm.action='.$url.';frm.method="post";document.body.appendChild(frm);frm.submit();');
		exit();
	}
	/**
	 * @ignore
	 */
	public function Application($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode)
	{
		//ini_set('session.gc_maxlifetime', '0');
		session_name(hash('md5', $_SERVER['PHP_SELF']));
		session_start();
		$GLOBALS['_NURLTokenMode'] = $urlTokenMode;
		$GLOBALS['_NTokenTrailsExpiration'] = $tokenTrailsExpiration;
		if(isset($_GET['NOLOHImage']))
			if(empty($_GET['Width']))
				Image::MagicGeneration($_GET['NOLOHImage'], $_GET['Class'], $_GET['Function'], $_GET['Params']);
			else
				Image::MagicGeneration($_GET['NOLOHImage'], $_GET['Class'], $_GET['Function'], $_GET['Params'], $_GET['Width'], $_GET['Height']);
		elseif(isset($_GET['NOLOHFileUpload']))
			FileUpload::ShowInside($_GET['NOLOHFileUpload'], $_GET['Width'], $_GET['Height']);
		elseif(isset($_GET['NOLOHFileRequest']))
			File::SendRequestedFile($_GET['NOLOHFileRequest']);
		elseif(isset($_SESSION['_NVisit']) || isset($_POST['NOLOHVisit']))
		{
			if(isset($_POST['NoSkeleton']) && GetBrowser()=='ie')
				$this->HandleIENavigation($className, $unsupportedURL);
			elseif($this->HandleForcedReset($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode))
				return;
			$this->HandleDebugMode($debugMode);
			if(isset($_SESSION['_NOmniscientBeing']))
				$this->TheComingOfTheOmniscientBeing();
			$this->HandleClientChanges();
			if(!empty($_POST['NOLOHFileUploadId']))
				GetComponentById($_POST['NOLOHFileUploadId'])->File = &$_FILES['NOLOHFileUpload'];
			foreach($_SESSION['_NFiles'] as $key => $val)
				GetComponentById($key)->File = new File($val);
			if(!empty($_POST['NOLOHServerEvent']))
				$this->HandleServerEvent();
			foreach($_SESSION['_NFiles'] as $key => $val)
			{
				unlink($_SESSION['_NFiles'][$key]['tmp_name']);
				GetComponentById($key)->File = null;
				unset($_SESSION['_NFiles'][$key]);
			}
			$this->Run();
		}
		else
			$this->HandleFirstRun($className, $unsupportedURL);
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
			$_SESSION['_NFiles'],
			$_SESSION['_NFileSend'],
			$_SESSION['_NGarbage'],
			$_SESSION['_NStartUpPageClass'],
			$_SESSION['_NURL'],
			$_SESSION['_NTokens'],
			$_SESSION['HighestZIndex'],
			$_SESSION['LowestZIndex']);
	}
	
	private function HandleFirstRun($className, $unsupportedURL, $trulyFirst=true)
	{
		if(isset($_SESSION['_NPHPInfo']))
		{
			ob_start('_NPHPInfo');
			phpinfo();
			exit();
		}
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
		$_SESSION['_NFiles'] = array();
		$_SESSION['_NFileSend'] = array();
		$_SESSION['_NGarbage'] = array();
		$_SESSION['_NStartUpPageClass'] = $className;
		$_SESSION['_NURL'] = $_SERVER['PHP_SELF'];
		$_SESSION['_NTokens'] = array();
		$_SESSION['HighestZIndex'] = 0;
		$_SESSION['LowestZIndex'] = 0;
		UserAgent::LoadInformation();
		if($trulyFirst)
			if($_SESSION['_NBrowser'] == 'other' && $_SESSION['_NOS'] == 'other')
				$this->SearchEngineRun();
			else 
				WebPage::SkeletalShow($unsupportedURL);
	}
	
	private function HandleForcedReset($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode)
	{
		if(!isset($_SESSION['_NVisit']) || 
			(isset($_POST['NOLOHVisit']) && $_SESSION['_NVisit'] != $_POST['NOLOHVisit']) ||
			((!isset($_POST['NOLOHVisit']) || !isset($_SERVER['HTTP_REMOTE_SCRIPTING'])) && $_SESSION['_NVisit']>=0 && !isset($_GET['NOLOHVisit'])))
		{
			if(!isset($_POST['NOLOHServerEvent']) || $_POST['NOLOHServerEvent'] != ('Unload@'.$_SESSION['_NStartUpPageId']))
			{
				if(isset($_SERVER['HTTP_REMOTE_SCRIPTING']) || isset($_POST['NOLOHServerEvent']) || !isset($_SESSION['_NVisit']) || isset($_GET['NWidth']))
					self::Reset(false, false);
				$this->TheComingOfTheOmniscientBeing();
				$webPage = WebPage::That();
				if($webPage != null && !$webPage->GetUnload()->Blank())
					$webPage->Unload->Exec();
				self::UnsetNolohSessionVars();
				self::SetStartUpPage($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode);
			}
			return true;
		}
		return false;
	}
	
	private function HandleIENavigation($className, $unsupportedURL)
	{
		$srcs = $_SESSION['_NScriptSrcs'];
		self::UnsetNolohSessionVars();
		$this->HandleFirstRun($className, $unsupportedURL, false);
		$_SESSION['_NScriptSrcs'] = $srcs;
		AddScript('NOLOHVisit=-1', Priority::High);
	}
	
	private function HandleDebugMode($debugMode)
	{
		if($debugMode !== 'Unhandled')
		{
			$GLOBALS['_NDebugMode'] = $debugMode;
			ini_set('html_errors', false);
			set_error_handler('_NErrorHandler', error_reporting());
			ob_start('_NOBErrorHandler');
			if($_SESSION['_NVisit']==-1)
				AddScript('_NDebugMode='.($debugMode?'true':'false'));
		}
	}
	
	private function TheComingOfTheOmniscientBeing()
	{
		global $OmniscientBeing;
		$OmniscientBeing = unserialize(defined('FORCE_GZIP') ? gzuncompress($_SESSION['_NOmniscientBeing']) : $_SESSION['_NOmniscientBeing']);
		unset($_SESSION['_NOmniscientBeing']);
		$idArrayStr = '';
		foreach($_SESSION['_NGarbage'] as $id => $nothing)
		{
			//$control = &$GLOBALS['OmniscientBeing'][$id];
			$control = &$OmniscientBeing[$id];
			//if(!isset($_SESSION['_NGarbage'][$control->GetParentId()]) && $control->GetShowStatus()!==0 && $control instanceof Control)
			if($control instanceof Control && !isset($_SESSION['_NGarbage'][$control->GetParentId()]) && $control->GetShowStatus()!==0)
				$idArrayStr .= '\'' . $id . '\',';
			unset($OmniscientBeing[$id]);
		}
		if($idArrayStr != '')
			AddScript('_NGCAsc([' . rtrim($idArrayStr, ',') . '])', Priority::Low);
//			AddScript('_NGCAsc(Array(' . rtrim($idArrayStr, ',') . '))', Priority::Low);
		$_SESSION['_NGarbage'] = array();
		$this->WebPage = GetComponentById($_SESSION['_NStartUpPageId']);
	}

	private function HandleClientChanges()
	{
		if(isset($_POST['NOLOHKey']))
			Event::$Key = $_POST['NOLOHKey'];
		if(isset($_POST['NOLOHCaught']))
			Event::$Caught = $this->ExplodeDragCatch($_POST['NOLOHCaught']);
        if(isset($_POST['NOLOHFocus']))
        {
			Event::$FocusedComponent = $_POST['NOLOHFocus'];
            Event::$SelectedText = $_POST['NOLOHSelectedText'];
        }
		if(isset($_POST['NOLOHContextMenuSource']))
			ContextMenu::$Source = GetComponentById($_POST['NOLOHContextMenuSource']);
		if(isset($_POST['NOLOHMouseX']))
		{
			Event::$MouseX = $_POST['NOLOHMouseX'];
			Event::$MouseY = $_POST['NOLOHMouseY'];
		}
		if(isset($_POST['NOLOHFlashArgs']))
			Event::$FlashArgs = explode('~d3~', $_POST['NOLOHFlashArgs']);
		if(!empty($_POST['NOLOHClientChanges']))
		{
			$componentChanges = explode('~d0~', stripslashes($_POST['NOLOHClientChanges']));
			$numComponents = count($componentChanges);
			for($i = 0; $i < $numComponents; ++$i)
			{
				$changes = explode('~d1~', $componentChanges[$i]);
				$GLOBALS['_NQueueDisabled'] = $changes[0];
				$component = &GetComponentById($changes[0]);
				$changeCount = count($changes);
				$j = 0;
				while(++$j < $changeCount)
					$component->{$changes[$j]} = $changes[++$j];
			}
		}
		$GLOBALS['_NQueueDisabled'] = null;
	}
	
	private function HandleServerEvent()
	{
		$splitEvent = explode('@', $_POST['NOLOHServerEvent']);
		$obj = GetComponentById($splitEvent[1]);
		if($obj != null)
        {
            $execClientEvents = false;
			return $obj->{$splitEvent[0]}->Exec($execClientEvents);
        }
		else 
			return GetComponentById(substr($splitEvent[1], 0, strpos($splitEvent[1], 'i')))->ExecEvent($splitEvent[0], $splitEvent[1]);
	}

	private function HandleTokens()
	{
		if($GLOBALS['_NURLTokenMode'] == 0)
			return;
		unset($_GET['NOLOHVisit'], $_GET['NWidth'], $_GET['NHeight']);
		if($GLOBALS['_NURLTokenMode'] == 1)
			$_SESSION['_NTokens'] = $_GET;
		if($GLOBALS['_NURLTokenMode'] == 2)
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

	private function Run()
	{
		global $OmniscientBeing;
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		//header('Cache-Control: no-store');
		if(++$_SESSION['_NVisit']==0)
		{
			header('Content-Type: text/javascript');
			$GLOBALS['_NWidth'] = $_GET['NWidth'];
			$GLOBALS['_NHeight'] = $_GET['NHeight'];
			$this->HandleTokens();
			$className = $_SESSION['_NStartUpPageClass'];
			$this->WebPage = new $className();
			$_SESSION['_NStartUpPageId'] = $this->WebPage->Id;
			$this->WebPage->Show();
		}
		if(isset($GLOBALS['_NTokenUpdate']) && (!isset($_POST['NoSkeleton']) || GetBrowser()!='ie'))
			URL::UpdateTokens();
		NolohInternal::ControlQueue();
		NolohInternal::SetPropertyQueue();
		NolohInternal::FunctionQueue();
		//NolohInternal::SetPropertyQueue();
		ob_end_clean();
		if(defined('FORCE_GZIP'))
			ob_start('ob_gzhandler');
		print($_SESSION['_NScriptSrc'] . '/*~NScript~*/' . $_SESSION['_NScript'][0] . $_SESSION['_NScript'][1] . $_SESSION['_NScript'][2]);
		$_SESSION['_NScriptSrc'] = '';
		$_SESSION['_NScript'] = array('', '', '');
		$_SESSION['_NOmniscientBeing'] = defined('FORCE_GZIP') ? gzcompress(serialize($OmniscientBeing),1) : serialize($OmniscientBeing);
		$GLOBALS['_NGarbage'] = true;
		unset($OmniscientBeing, $GLOBALS['OmniscientBeing']);
		unset($GLOBALS['_NGarbage']);
	}

	private function SearchEngineRun()
	{
		$this->HandleTokens();
		$className = $_SESSION['_NStartUpPageClass'];
		$this->WebPage = new $className();
		$_SESSION['_NStartUpPageId'] = $this->WebPage->Id;
		$tokenLinks = '';
		$file = getcwd().'/NOLOHSearchTrails.dat';
		if(file_exists($file))
		{
			$tokenString = URL::TokenString($_SESSION['_NTokens']);
			$trails = unserialize(base64_decode(file_get_contents($file)));
			if($trails !== false && isset($trails[$tokenString]))
			{
				//file_put_contents('/tmp/PhillData.txt', 'YES!');
				foreach($trails[$tokenString] as $key => $nothing)
					$tokenLinks .= '<A href="' . $_SERVER['PHP_SELF'] . '?' . $key . '">' . $key . '</a> ';
			}
			//else
				//file_put_contents('/tmp/PhillData.txt', $tokenString);
		}
		/*$className = $_SESSION['_NStartUpPageClass'];
		$this->WebPage = new $className();
		$_SESSION['_NStartUpPageId'] = $this->WebPage->Id;*/
		$this->WebPage->SearchEngineShow($tokenLinks);
		session_destroy();
		session_unset();
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
}

?>