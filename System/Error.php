<?php
/**
 * @ignore
 */
// TODO: Move into a class at some point?
/**
 * @ignore
 */
function _NOBErrorHandler($buffer)
{
	if(strpos($buffer, '<title>phpinfo()</title>') !== false)
		trigger_error('~_NINFO~');
	elseif(preg_match('/(.*): (.*) in (.*) on line ([0-9]+)/s', $buffer, $matches))
		//if($GLOBALS['_NDebugMode'] === 'Kernel')
			trigger_error('~OB~'.$matches[1].'~OB~'.$matches[2].'~OB~'.$matches[3].'~OB~'.$matches[4]);
		//else
		//{
			//if($trace = _NFirstNonNOLOHBacktrace())
			//	trigger_error('~OB~'.$matches[1].'~OB~'.$matches[2].'~OB~'.$trace['file'].'~OB~'.$trace['file']);
			
			//return false;
			//trigger_error(serialize(error_get_last()));
			//trigger_error(serialize(error_get_last()));
			//trigger_error('~OB~'.$matches[1].'~OB~'.$matches[2].'~OB~?~OB~?');
			//return new Exception('lol', 0);
			//trigger_error('HAH');
			//trigger_error('~OB~'.$matches[1].'~'.$matches[2].'~'.$matches[3].'~'.$matches[4]);
		//}
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
	ob_end_clean();
	setcookie('_NAppCookie', false, 0, '/');
	if(strpos($string, '~OB~') === 0)
	{
		$matches = explode('~OB~', $string);
		$string = $matches[2];
		$file = $matches[3];
		$line = $matches[4];
	}
	elseif($string === '~_NINFO~')
	{
		setcookie('_NPHPInfo', true);
		Application::Reset(true, false);
	}
	if($GLOBALS['_NDebugMode'] !== 'Kernel')
	{
		if($trace = _NFirstNonNOLOHBacktrace())
		{
			$file = $trace['file'];
			$line = $trace['line'];
		}
		else 
			$file = $line = null;
	}
	$gzip = defined('FORCE_GZIP');
	if($gzip && !in_array('ob_gzhandler', ob_list_handlers(), true))
		ob_start('ob_gzhandler');
	if(!in_array('Cache-Control: no-cache', headers_list(), true))
		++$_SESSION['_NVisit'];
	error_log($message = (str_replace(array("\n","\r",'"'),array('\n','\r','\"'),$string).($file?"\\nin $file\\non line $line":'')));
	echo '/*_N*/alert("', $GLOBALS['_NDebugMode'] ? "A server error has occurred:\\n\\n$message" : 'An application error has occurred.', '");';
	if($gzip)
		ob_end_flush();
	flush();
	global $OmniscientBeing;
	$_SESSION['_NScript'] = array('', '', '');
	$_SESSION['_NScriptSrc'] = '';
	$_SESSION['_NOmniscientBeing'] = $gzip ? gzcompress(serialize($OmniscientBeing),1) : serialize($OmniscientBeing);
    exit();
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
	return false;
}
/**
 * Terminates the application {@see PHP_Manual#die}
 * @param string $message Specifies an error message.
 */
function BloodyMurder($message)
{
	if($_SESSION['_NVisit'] === -1)
	{
		echo $message;
		session_destroy();
		exit();
	}
	else
		_NErrorHandler(0, $message, 0, 0);
}
?>