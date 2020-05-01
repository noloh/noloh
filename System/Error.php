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
	{
		trigger_error('~_NINFO~');
	}
	elseif(preg_match('/(.*): (.*) in (.*) on line ([0-9]+)/s', $buffer, $matches))
	{
		if ($GLOBALS['_NDebugMode'] === 'Kernel')
		{
			trigger_error('~OB~' . $matches[1] . '~OB~' . $matches[2] . '~OB~' . $matches[3] . '~OB~' . $matches[4]);
		}
		else
		{
			$trace = _NFirstNonNOLOHBacktrace();
			if (PHP_VERSION_ID < 50300)
			{
				$obStr = '~OB~' . $matches[1] . '~OB~' . $matches[2] . '~OB~' . $trace['file'] . '~OB~' . $trace['line'];
				trigger_error($obStr);
			}
			else
			{
				$processRequestDetails = true;
				// For some bizarre reason, calling _NErrorHandler (with modifications) doesn't work. So code repetition appears necessary.
				setcookie('_NAppCookie', false);
				if (!in_array('Cache-Control: no-cache', headers_list(), true))
				{
					++$_SESSION['_NVisit'];
				}

				$message = (str_replace(array("\n", "\r", '"'), array('\n', '\r', '\"'), $matches[2]) . ($trace['file'] ? "\\nin " . str_replace("\\", "\\\\", $trace['file']) . "\\non line " . $trace['line'] : ''));

				if (strpos($message, 'Class \'Object\' not found') !== false)
				{
					$message = 'This project requires PHP 5.x but is currently running on ' . PHP_VERSION;
					$processRequestDetails = false;
				}

				$alert = '/*_N*/alert("' . ($GLOBALS['_NDebugMode'] ? "A server error has occurred:\\n\\n$message" : 'An application error has occurred.') . '");';

				NolohInternal::ResetSecureValuesQueue();
				NolohInternal::ResetSession();

				$requestDetails = &Application::UpdateRequestDetails();
				$requestDetails['error_message'] = $message;
				unset($requestDetails['total_session_io_time']);

				$webPage = WebPage::That();
				if ($webPage && $processRequestDetails)
				{
					if(strpos($message, 'syntax error') !== false)
					{
						echo $message;
					}
					else
					{
						$webPage->ProcessRequestDetails($requestDetails);
					}
				}

				return $alert;
			}
		}
	}
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
	if ($number & error_reporting())
	{
		if ($ob)
		{
			$string = $matches[2];
			$file = $matches[3];
			$line = $matches[4];
		}
		elseif ($string === '~_NINFO~')
		{
			setcookie('_NPHPInfo', true);
			Application::Reset(true, false);
		}
		elseif ($GLOBALS['_NDebugMode'] !== 'Kernel')
		{
			$trace = _NFirstNonNOLOHBacktrace();
			$file = $trace['file'];
			$line = $trace['line'];
		}
		
		$message = $string . ($file ? "\\nin " . str_replace("\\", "\\\\", $file) . "\\non line $line" : '');
		DisplayError($message);
	}
	else
		return false;
}
function _NExceptionHandler($exception)
{
	global $_NPath;
	
	$traces = $exception->getTrace();
	$message = $exception->getMessage();
	foreach ($traces as $trace)
	{
		if (strpos($trace['file'], $_NPath) === false
			&& isset($trace['file'])
			&& isset($trace['line']))
		{
			$message .= PHP_EOL . 'in ' . str_replace('\\', '\\\\', $trace['file']) . "\\non line " . $trace['line'];
		}
	}
	
	DisplayError($message);
}
function DisplayError($message)
{
	$level = ob_get_level();
	for ($i = 0; $i < $level; ++$i)
	{
		ob_end_clean();
	}
	setcookie('_NAppCookie', false);

	$gzip = defined('FORCE_GZIP');
	if ($gzip && !in_array('ob_gzhandler', ob_list_handlers(), true))
	{
		ob_start('ob_gzhandler');
	}
	if (!in_array('Cache-Control: no-cache', headers_list(), true))
	{
		++$_SESSION['_NVisit'];
	}
	$message = str_replace(array("\n", "\r", '"'), array('\n', '\r', '\"'), $message);
	@error_log($message);
	echo '/*_N*/alert("', $GLOBALS['_NDebugMode'] ? "A server error has occurred:\\n\\n{$message}" : 'An application error has occurred.', '");';
	if ($gzip)
	{
		ob_end_flush();
	}
	flush();

	NolohInternal::ResetSecureValuesQueue();
	NolohInternal::ResetSession();

	$requestDetails = &Application::UpdateRequestDetails();
	$requestDetails['error_message'] = $message;
	unset($requestDetails['total_session_io_time']);
	$webPage = WebPage::That();
	if ($webPage)
	{
		$webPage->ProcessRequestDetails($requestDetails);
	}
	
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
	if ($GLOBALS['_NDebugMode'] === 'Kernel')
	{
		return $backtrace[0];
	}
	else
	{
		for ($i = 0; $i < $backtraceCount; ++$i)
		{
			if (isset($backtrace[$i]['file']) && strpos($backtrace[$i]['file'], $_NPath) === false)
			{
				return $backtrace[$i];
			}
		}
	}

	if (function_exists('error_get_last') && ($errorLast = error_get_last()) && isset($errorLast['file']) && strpos($errorLast['file'], $_NPath) === false)
	{
		return $errorLast;
	}
	return array('file' => null, 'line' => null);
}
/**
 * Terminates the application {@see PHP_Manual#die}
 * @param string $message Specifies an error message.
 */
function BloodyMurder($message)
{
	if(UserAgent::IsCLI() || System::IsRESTful())
	{
		trigger_error($message, E_USER_ERROR);
	}
	
	if(!isset($GLOBALS['_NDebugMode']))
	{
		trigger_error($message);
	}
	elseif($_SESSION['_NVisit'] === -1)
	{
		echo $message;
		session_destroy();
		exit();
	}
	else
	{
		$trace = debug_backtrace();
		if ($GLOBALS['_NDebugMode'] === 'Kernel')
		{
			$trace = $trace[0];

			_NErrorHandler(1, $message, $trace['file'], $trace['line']);
		}
		else
		{
			global $_NPath;

			$traceCount = 0;
			foreach ($trace as $error)
			{
				if (isset($error['file']) && strpos($error['file'], $_NPath) === false)
				{
					$message .= PHP_EOL . ($error['file'] ? "\\nin " . str_replace("\\", "\\\\", $error['file']) . "\\non line {$error['line']}" : '');
					$traceCount++;
				}

				if ($traceCount == 2)
				{
					break;
				}
			}

			DisplayError($message);
		}
	}
}
?>