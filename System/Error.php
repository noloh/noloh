<?php
/**
 * @ignore
 */
// TODO: Move into a class at some point?
/**
 * @ignore
 */
function _NPHPInfo($info)
{
	$info = str_replace(array("\n", "\r", "'"), array('','',"\\'"), $info);
	$loc = strpos($info, '</table>') + 8;
	$text = substr($info, 0, $loc) .
		'<br><table border="0" cellpadding="3" width="600"><tr class="h"><td><a href="http://www.noloh.com"><img border="0" src="' . ((NOLOHConfig::NOLOHURL)?NOLOHConfig::NOLOHURL:GetRelativePath(dirname($_SERVER['SCRIPT_FILENAME']), ComputeNOLOHPath())) . '/Images/nolohLogo.png" alt="NOLOH Logo" /></a><h1 class="p">NOLOH Version '.GetNOLOHVersion().'</h1></td></tr></table><div id="N2"></div><div id="N3"></div>' .
		substr($info, $loc);
	session_destroy();
	return $text;
}
/**
 * @ignore
 */
function _NOBErrorHandler($buffer)
{
	if(strpos($buffer, '<title>phpinfo()</title>') !== false)
		trigger_error('~_NINFO~');
	elseif(preg_match('/(.*): (.*) in (.*) on line ([0-9]+)/s', $buffer, $matches))
		if($GLOBALS['_NDebugMode'] === 'Kernel')
			trigger_error('~OB~'.$matches[1].'~OB~'.$matches[2].'~OB~'.$matches[3].'~OB~'.$matches[4]);
		else
		{
			$trace = _NFirstNonNOLOHBacktrace();
			trigger_error('~OB~'.$matches[1].'~OB~'.$matches[2].'~OB~'.$trace['file'].'~OB~'.$trace['line']);
			//return false;
			//trigger_error(serialize(error_get_last()));
			//trigger_error(serialize(error_get_last()));
			//trigger_error('~OB~'.$matches[1].'~OB~'.$matches[2].'~OB~?~OB~?');
			//return new Exception('lol', 0);
			//trigger_error('HAH');
			//trigger_error('~OB~'.$matches[1].'~'.$matches[2].'~'.$matches[3].'~'.$matches[4]);
			//_NErrorHandler($matches[2], $matches[3], $matches[4]);
		}
	/*elseif(ereg('([^:]+): (.+) in (.+) on line ([0-9]+)', $buffer, $matches))
		trigger_error('~OB~'.$matches[1].'~'.$matches[2].'~'.$matches[3].'~'.$matches[4]);*/
	//else 
	//	trigger_error('~OB~'.$buffer.'~OB~'.$buffer.'~OB~'.$buffer.'~OB~'.$buffer);
}
/**
 * @ignore
 */
function _NErrorHandler($number, $string, $file, $line)
{
	if($ob = (strpos($string, '~OB~') === 0))
	{
		$matches = explode('~OB~', $string);
		$error = substr(strtoupper(trim(strip_tags($matches[1]))), 0, 5);
		if($error === 'PARSE')
			$number = 4;
		else
			$number = 1;
	}
	if($number & error_reporting())
	{
		ob_end_clean();
		setcookie('_NAppCookie', false);
		if($ob)
		{
			$string = $matches[2];
			$file = $matches[3];
			$line = $matches[4];
		}
		elseif($string === '~_NINFO~')
		{
			setcookie('_NPHPInfo', true);
			Application::Reset(true, false);
		}
		elseif($GLOBALS['_NDebugMode'] !== 'Kernel')
		{
			$trace = _NFirstNonNOLOHBacktrace();
			$file = $trace['file'];
			$line = $trace['line'];
		}
		$gzip = defined('FORCE_GZIP');
		if($gzip && !in_array('ob_gzhandler', ob_list_handlers(), true))
			ob_start('ob_gzhandler');
		if(!in_array('Cache-Control: no-cache', headers_list(), true))
			++$_SESSION['_NVisit'];
		error_log($message = (str_replace(array("\n","\r",'"'),array('\n','\r','\"'),$string).($file?"\\nin ".str_replace("\\","\\\\",$file)."\\non line $line":'')));
		echo '/*_N*/alert("', $GLOBALS['_NDebugMode'] ? "A server error has occurred:\\n\\n$message" : 'An application error has occurred.', '");';
		if($gzip)
			ob_end_flush();
		flush();
		NolohInternal::ResetSecureValuesQueue();
		global $OmniscientBeing;
		$_SESSION['_NScript'] = array('', '', '');
		$_SESSION['_NScriptSrc'] = '';
		$_SESSION['_NOmniscientBeing'] = $gzip ? gzcompress(serialize($OmniscientBeing),1) : serialize($OmniscientBeing);
	    exit();
	}
	else
		return false;
}
/**
 * Finds the first array of trace information before a NOLOH file
 * @return array|false
 */
function _NFirstNonNOLOHBacktrace()
{
	global $_NPath;
	$backtrace = debug_backtrace();
	$backtraceCount = count($backtrace);
	for($i=0; $i<$backtraceCount; ++$i)
		if(isset($backtrace[$i]['file']) && strpos($backtrace[$i]['file'], $_NPath) === false)
			return $backtrace[$i];
	if(function_exists('error_get_last') && ($errorLast = error_get_last()) && isset($errorLast['file']) && strpos($errorLast['file'], $_NPath) === false)
		return $errorLast;
	return array('file' => null, 'line' => null);
}
/**
 * Terminates the application {@see PHP_Manual#die}
 * @param string $message Specifies an error message.
 */
function BloodyMurder($message)
{
	if(!isset($GLOBALS['_NDebugMode']))
		trigger_error($message);
	elseif($_SESSION['_NVisit'] === -1)
	{
		echo $message;
		session_destroy();
		exit();
	}
	else
	{
		if($GLOBALS['_NDebugMode'] === 'Kernel')
		{
			$trace = debug_backtrace();
			$trace = $trace[0];
		}
		else 
			$trace = _NFirstNonNOLOHBacktrace();
		_NErrorHandler(1, $message, $trace['file'], $trace['line']);
	}
}
?>