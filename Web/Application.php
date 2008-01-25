<?php
/**
 * @package Web
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
	if(ereg('([^:]+): (.+) in (.+) on line ([0-9]+)', $buffer, $matches))
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
		//$number = $splitStr[2];
		$string = $splitStr[3];
		$file = $splitStr[4];
		$line = $splitStr[5];
	}
	if(defined('FORCE_GZIP') && !in_array('ob_gzhandler', ob_list_handlers()))
	{
		ob_start('ob_gzhandler');
		++$_SESSION['NOLOHVisit'];
	}
	print('/*~NScript~*/alert("' . ($GLOBALS['_NDebugMode'] ? ("A server error has occurred:\\n\\n".str_replace('"','\"',$string)."\\nin $file\\non line $line") : 'An application error has occurred.') . '");');
	global $OmniscientBeing;
	$_SESSION['NOLOHScript'] = array('', '', '');
	$_SESSION['_NScriptSrc'] = '';
	$_SESSION['NOLOHOmniscientBeing'] = defined('FORCE_GZIP') ? gzcompress(serialize($OmniscientBeing),1) : serialize($OmniscientBeing);
    ob_end_flush();
    exit();
}

/**
* @ignore
*/
final class Application
{
	private $WebPage;

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
		if(isset(Event::$MouseX))
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
			print('location.replace('.$url.');');
		else
			print('var frm=document.createElement("FORM");frm.action='.$url.';frm.method="post";document.body.appendChild(frm);frm.submit();');
		exit();
	}

	public function Application($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode)
	{
		session_name(hash('md5', $_SERVER['PHP_SELF']));
		session_start();
		$GLOBALS['NOLOHURLTokenMode'] = $urlTokenMode;
		$GLOBALS['NOLOHTokenTrailsExpiration'] = $tokenTrailsExpiration;
		if(isset($_GET['NOLOHImage']))
			Image::MagicGeneration($_GET['NOLOHImage'], $_GET['Class'], $_GET['Function'], $_GET['Params']);
		elseif(isset($_GET['NOLOHFileUpload']))
			FileUpload::ShowInside($_GET['NOLOHFileUpload'], $_GET['Width'], $_GET['Height']);
		elseif(isset($_GET['NOLOHFileRequest']))
			File::SendRequestedFile($_GET['NOLOHFileRequest']);
		elseif(isset($_SESSION['NOLOHVisit']) || isset($_POST['NOLOHVisit']))
		{
			if(!isset($_SESSION['NOLOHVisit']) || (isset($_POST['NOLOHVisit']) && $_SESSION['NOLOHVisit'] != $_POST['NOLOHVisit']) ||
			  ((!isset($_POST['NOLOHVisit']) || !isset($_SERVER['HTTP_REMOTE_SCRIPTING'])) && $_SESSION['NOLOHVisit']>=0 && !isset($_GET['NOLOHVisit'])))
					if($this->HandleForcedReset($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode))
						return;
			if(isset($_POST['NoSkeleton']) && GetBrowser()=='ie')
				$this->HandleIENavigation($className, $unsupportedURL);
			$this->HandleDebugMode($debugMode);
			if(isset($_SESSION['NOLOHOmniscientBeing']))
				$this->TheComingOfTheOmniscientBeing();
			if(!empty($_POST['NOLOHClientChanges']))
				$this->HandleClientChanges();
			if(!empty($_POST['NOLOHFileUploadId']))
				GetComponentById($_POST['NOLOHFileUploadId'])->File = &$_FILES['NOLOHFileUpload'];
			foreach($_SESSION['NOLOHFiles'] as $key => $val)
				GetComponentById($key)->File = new File($val);
			if(!empty($_POST['NOLOHServerEvent']))
				$this->HandleServerEvent();
			foreach($_SESSION['NOLOHFiles'] as $key => $val)
			{
				unlink($_SESSION['NOLOHFiles'][$key]['tmp_name']);
				GetComponentById($key)->File = null;
				unset($_SESSION['NOLOHFiles'][$key]);
			}
			$this->Run();
		}
		else
			$this->HandleFirstRun($className, $unsupportedURL);
	}
	
	static function UnsetNolohSessionVars()
	{
		unset($_SESSION['NOLOHVisit'],
			$_SESSION['NOLOHNumberOfComponents'],
			$_SESSION['NOLOHOmniscientBeing'],
			$_SESSION['NOLOHControlQueue'],
			$_SESSION['NOLOHControlInserts'],
			$_SESSION['NOLOHFunctionQueue'],
			$_SESSION['NOLOHPropertyQueue'],
			$_SESSION['NOLOHScript'],
			$_SESSION['_NScriptSrc'],
			$_SESSION['NOLOHScriptSrcs'],
			$_SESSION['NOLOHGlobals'],
			$_SESSION['NOLOHFiles'],
			$_SESSION['NOLOHFileSend'],
			$_SESSION['NOLOHGarbage'],
			$_SESSION['NOLOHStartUpPageClass'],
			$_SESSION['NOLOHURL'],
			$_SESSION['NOLOHTokens'],
			$_SESSION['HighestZIndex'],
			$_SESSION['LowestZIndex']);
	}
	
	private function HandleFirstRun($className, $unsupportedURL, $trulyFirst=true)
	{
		$_SESSION['NOLOHVisit'] = -1;
		$_SESSION['NOLOHNumberOfComponents'] = 0;
		$_SESSION['NOLOHControlQueue'] = array();
		$_SESSION['NOLOHControlInserts'] = array();
		$_SESSION['NOLOHFunctionQueue'] = array();
		$_SESSION['NOLOHPropertyQueue'] = array();
		$_SESSION['NOLOHScript'] = array('', '', '');
		$_SESSION['_NScriptSrc'] = '';
		$_SESSION['NOLOHScriptSrcs'] = array();
		$_SESSION['NOLOHGlobals'] = array();
		$_SESSION['NOLOHFiles'] = array();
		$_SESSION['NOLOHFileSend'] = array();
		$_SESSION['NOLOHGarbage'] = array();
		$_SESSION['NOLOHStartUpPageClass'] = $className;
		$_SESSION['NOLOHURL'] = $_SERVER['PHP_SELF'];
		$_SESSION['NOLOHTokens'] = array();
		$_SESSION['HighestZIndex'] = 0;
		$_SESSION['LowestZIndex'] = 0;
		UserAgentDetect::LoadInformation();
		if($trulyFirst)
			if($_SESSION['NOLOHBrowser'] == 'other' && $_SESSION['NOLOHOS'] == 'other')
			//if(true)
				$this->SearchEngineRun();
			else 
				WebPage::SkeletalShow($unsupportedURL);
	}
	
	private function HandleForcedReset($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode)
	{
		if(isset($_SESSION['_NReset']))
		{
			unset($_SESSION['_NReset']);
			$_SESSION['NOLOHVisit'] = $_POST['NOLOHVisit'];
		}
		elseif(!isset($_POST['NOLOHServerEvent']) || $_POST['NOLOHServerEvent'] != 'Unload@N1')
		{
			if(isset($_SERVER['HTTP_REMOTE_SCRIPTING']) || isset($_POST['NOLOHServerEvent']) || !isset($_SESSION['NOLOHVisit']) || isset($_GET['NWidth']))
				self::Reset(false, false);
			$webPage = GetComponentById('N1');
			if($webPage != null && !$webPage->GetUnload()->Blank())
				$webPage->Unload->Exec();
			self::UnsetNolohSessionVars();
			self::SetStartUpPage($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode);
			return true;
		}
		else
			$_SESSION['_NReset'] = true;
		return false;
	}
	
	private function HandleIENavigation($className, $unsupportedURL)
	{
		$srcs = $_SESSION['NOLOHScriptSrcs'];
		self::UnsetNolohSessionVars();
		$this->HandleFirstRun($className, $unsupportedURL, false);
		$_SESSION['NOLOHScriptSrcs'] = $srcs;
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
			if($_SESSION['NOLOHVisit']==-1)
				AddScript('_NDebugMode='.($debugMode?'true;':'false;'));
		}
	}
	
	private function TheComingOfTheOmniscientBeing()
	{
		global $OmniscientBeing;
		$OmniscientBeing = unserialize(defined('FORCE_GZIP') ? gzuncompress($_SESSION['NOLOHOmniscientBeing']) : $_SESSION['NOLOHOmniscientBeing']);
		unset($_SESSION['NOLOHOmniscientBeing']);
		foreach($_SESSION['NOLOHGarbage'] as $id => $nothing)
		{
			$control = &$GLOBALS['OmniscientBeing'][$id];
			if(!isset($_SESSION['NOLOHGarbage'][$control->GetParentId()]) && $control->GetShowStatus()!==0 && $control instanceof Control)
				AddScript("_NAsc('$id')", Priority::Low);
			unset($OmniscientBeing[$id]);
		}
		$_SESSION['NOLOHGarbage'] = array();
		$this->WebPage = GetComponentById($_SESSION['NOLOHStartUpPageId']);
	}

	private function HandleClientChanges()
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
		$GLOBALS['_NQueueDisabled'] = null;
	}
	
	private function HandleServerEvent()
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
		Event::$MouseX = $_POST['NOLOHMouseX'];
		Event::$MouseY = $_POST['NOLOHMouseY'];
		$splitEvent = explode('@', $_POST['NOLOHServerEvent']);
		$obj = GetComponentById($splitEvent[1]);
		if($obj != null)
        {
            $execClientEvents = false;
			return $obj->{$splitEvent[0]}->Exec($execClientEvents);
        }
		else 
		{
			$splitStr = explode('i', $splitEvent[1], 2);
			return GetComponentById($splitStr[0])->ExecEvent($splitEvent[0], $splitEvent[1]);
		}
	}

	private function HandleTokens()
	{
		if($GLOBALS['NOLOHURLTokenMode'] == 0)
			return;
		unset($_GET['NOLOHVisit'], $_GET['NWidth'], $_GET['NHeight']);
		if($GLOBALS['NOLOHURLTokenMode'] == 1)
			$_SESSION['NOLOHTokens'] = $_GET;
		if($GLOBALS['NOLOHURLTokenMode'] == 2)
		{
			$keys = array_keys($_GET);
			$ubound = count($keys) - 1;
			for($i=0; $i<$ubound; ++$i)
				$_SESSION['NOLOHTokens'][$keys[$i]] = $_GET[$keys[$i]];
			if($_GET[$keys[$ubound]] != '')
				$_SESSION['NOLOHTokens'][$keys[$ubound]] = $_GET[$keys[$ubound]];
			else
			{
				$split = explode('&', base64_decode($keys[$ubound]));
				$count = count($split);
				for($i=0; $i<$count; ++$i)
				{
					$split2 = explode('=', $split[$i].'=');
					$_SESSION['NOLOHTokens'][$split2[0]] = $split2[1];
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
				$_SESSION['NOLOHTokens'][$split2[0]] = $split2[1];
			}
			$split2 = explode('=', $split[$ubound]);
			if($GLOBALS['NOLOHURLTokenMode'] == 1 || $split2[1] != '')
				$_SESSION['NOLOHTokens'][$split2[0]] = $split2[1];
			else 
			{
				$split = explode('&', base64_decode($split2[0]));
				$count = count($split);
				for($i=0; $i<$count; ++$i)
				{
					$split2 = explode('=', $split[$i].'=');
					$_SESSION['NOLOHTokens'][$split2[0]] = $split2[1];
				}
			}
		}
	}

	private function Run()
	{
		global $OmniscientBeing;
		if(++$_SESSION['NOLOHVisit']==0)
		{
			$this->HandleTokens();
			$className = $_SESSION['NOLOHStartUpPageClass'];
			$this->WebPage = new $className();
			$_SESSION['NOLOHStartUpPageId'] = $this->WebPage->Id;
			$this->WebPage->Show();
		}
		if(isset($GLOBALS['NOLOHTokenUpdate']) && (!isset($_POST['NoSkeleton']) || GetBrowser()!='ie'))
			URL::UpdateTokens();
		NolohInternal::ShowQueue();
		NolohInternal::FunctionQueue();
		NolohInternal::SetPropertyQueue();
		ob_end_clean();
		if(defined('FORCE_GZIP'))
			ob_start('ob_gzhandler');
		print($_SESSION['_NScriptSrc'] . '/*~NScript~*/' . $_SESSION['NOLOHScript'][0] . $_SESSION['NOLOHScript'][1] . $_SESSION['NOLOHScript'][2]);
		$_SESSION['_NScriptSrc'] = '';
		$_SESSION['NOLOHScript'] = array('', '', '');
		$_SESSION['NOLOHOmniscientBeing'] = defined('FORCE_GZIP') ? gzcompress(serialize($OmniscientBeing),1) : serialize($OmniscientBeing);
		$GLOBALS['NOLOHGarbage'] = true;
		unset($OmniscientBeing, $GLOBALS['OmniscientBeing']);
		unset($GLOBALS['NOLOHGarbage']);
	}
	
	private function SearchEngineRun()
	{
		$this->HandleTokens();
		$tokenLinks = '';
		$file = getcwd().'/NOLOHSearchTrails.dat';
		if(file_exists($file))
		{
			$tokenString = URL::TokenString($_SESSION['NOLOHTokens']);
			$trails = unserialize(base64_decode(file_get_contents($file)));
			if($trails !== false && isset($trails[$tokenString]))
				foreach($trails[$tokenString] as $key => $nothing)
					$tokenLinks .= '<A href="' . $_SERVER['PHP_SELF'] . '?' . $key . '">' . $key . '</a> ';
		}
		$className = $_SESSION['NOLOHStartUpPageClass'];
		$this->WebPage = new $className();
		$_SESSION['NOLOHStartUpPageId'] = $this->WebPage->Id;
		$this->WebPage->SearchEngineShow($tokenLinks);
		session_destroy();
		session_unset();
	}
	
	private function ExplodeDragCatch($objectsString)
	{
		$objs = array();
		$objectsIdArray = explode(',', $objectsString);
		$objectsCount = count($objectsIdArray);
		for($i=0; $i<$objectsCount; $i++)
			$objs[] = GetComponentById($objectsIdArray[$i]);
		return $objs;
	}
	/*
	private function ExplodeItems($optionsString)
	{
		$items = new ArrayList();
		$optionsArray = explode('~d3~', $optionsString);
		$optionsCount = count($optionsArray);
		for($i=0; $i<$optionsCount; $i++)
		{
			$option = explode('~d2~', $optionsArray[$i]);
			$items->Add(new Item($option[0], $option[1]));
		}
		return $items;
	}
	
	private function ExplodeSelectedIndices($indicesString)
	{
		return explode('~d2~', $indicesString);
	}
	*/
}

?>