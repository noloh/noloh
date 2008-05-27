<?php
/**
* @package Web
* @subpackage GeneralFunctions 
*/

/**
* Terminates the application {@see PHP_Manual#die} but also destroys the Session
* @param string $message Specifies an error message.
 */
function BloodyMurder($message)
{
	$backtrace = debug_backtrace();
	$backtraceCount = count($backtrace);
	$nolohPath = realpath(dirname(__FILE__).'/../');
	for($i=0; $i<$backtraceCount; ++$i)
		if(strpos($backtrace[$i]['file'], $nolohPath) === false)
			_NErrorHandler(0, $message, $backtrace[$i]['file'], $backtrace[$i]['line']);
	_NErrorHandler(0, $message, '?', '?');
}
/**
* Gets the size of the application on the server's hard disk in bytes
* @return integer
 */
function GetHardMemoryUsage()
{
	return strlen(serialize($GLOBALS['OmniscientBeing'])) + strlen(serialize($_SESSION));
}
/**
* Gets the absolute path of a localized path
* @param string $path
* @return string
 */
function GetAbsolutePath($path)
{
	if($path[0] == '\\' || $path[0] == '/')
		return realpath(NOLOHConfig::GetBaseDirectory().$path);
	elseif(strpos($path, 'http://') >= 0)
		return $path;
	else
		return realpath($path);
}
/**
* Gets a global that lasts as long as the application does, as opposed to PHP's globals that are forgotten as soon as the server is left.
* If not found returns null.
* @param string $name
* @return mixed
*/
function GetGlobal($name)
{
	return isset($_SESSION['_NGlobals'][$name]) ? $_SESSION['_NGlobals'][$name] : null;
}
/**
* Gets a global that lasts as long as the application does, as opposed to PHP's globals that are forgotten as soon as the server is left.
* @param string $name
* @param mixed $value
*/
function SetGlobal($name, $value)
{
	$_SESSION['_NGlobals'][$name] = &$value;
}
/**
* Alert a string specified by the $msg variable.
* <br><code>Alert("Hi, my name is Asher!");</code>
* @param string $msg Message to be Alerted
*/
function Alert($msg)
{
	AddScript('alert("' . str_replace(array("\n","\r",'"'),array('\n','\r','\"'),$msg) . '")');
}
/**
* Adds Javascript code to be run immediately on the client.<br>
* {@see AddScriptSrc($src)} to add script files as opposed to code.
* @param string $script Actual code of the script
* @param mixed $priority Determines the order in which scripts run. Can be: Priority::Low, Priority::Medium, or Priority::High 
*/
function AddScript($script, $priority=Priority::Medium)
{
	//if(isset($_SESSION['UnlockNOLOHDebug']) && $_SESSION['UnlockNOLOHDebug'] == 'mddevmddev')
	//	$_SESSION['_NScript'] .= $script . ";";
	//else
		/*$_SESSION['_NScript'] .= str_replace('"','\"',str_replace("'","\'",str_replace("\n","",$script))) . ';';*/
		$_SESSION['_NScript'][$priority] .= $script . ';';
}
/**
* Adds a Javascript script file to be run immediately on the client <br>
* The server will keep track of which files have been added so that the same file will not be sent to the client twice.<br>
* {@see AddScript($src)} to add actual code as opposed to files.
* @param string $path A path to the javascript file.
*/
function AddScriptSrc($path)
{
	if(!isset($_SESSION['_NScriptSrcs'][$path]))
	{
		$_SESSION['_NScriptSrc'] .= (file_get_contents($path));
		$_SESSION['_NScriptSrcs'][$path] = true;
	}
}
/**
* @ignore
*/
function AddNolohScriptSrc($src, $browserSpecific = false)
{
	if(!isset($_SESSION['_NScriptSrcs'][$src]))
	{
		$path = NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath().'Javascripts/';
		if($browserSpecific)
			$path .= $_SESSION['_NIsIE'] ? 'IE/' : 'Standard/';
		$path .= $src;
		$_SESSION['_NScriptSrc'] .= file_get_contents($path);
		$_SESSION['_NScriptSrcs'][$src] = true;
	}
}
/**
 * Queues a JavaScript function associated with a specific Component to be executed on the client. <br>
 * The code will not be sent to the client, until the given Component has shown.<br>
 * Be sure to include an extra set of quotes for your string parameters.<br>
 * <code>
 * 	QueueClientFunction($this, 'alert', "'This Component's Id is $this->Id'");
 * </code>
 * @param Component $component
 * @param string $functionName The name of the JavaScript function
 * @param array $paramsArray An array of parameters passed into the function
 * @param boolean $replace If true, 
 * @param mixed $priority Determines the order in which scripts run. Can be: Priority::Low, Priority::Medium, or Priority::High 
 */
function QueueClientFunction(Component $component, $functionName, $paramsArray, $replace=true, $priority=Priority::Medium)
{
    $objId = $component->Id;
	if($GLOBALS['_NQueueDisabled'] != $objId)
	{
		if(!isset($_SESSION['_NFunctionQueue'][$objId]))
			$_SESSION['_NFunctionQueue'][$objId] = array();
		if($replace)
			$_SESSION['_NFunctionQueue'][$objId][$functionName] = array($paramsArray, $priority);
		else
			$_SESSION['_NFunctionQueue'][$objId][] = array($functionName, $paramsArray, $priority);			
	}
}
/**
* @ignore
*/
function GetBrowser()
{
	return $_SESSION['_NBrowser'];
}
/**
* @ignore
*/
function UnlockNOLOHDebug($password)
{
	$_SESSION['UnlockNOLOHDebug'] = $password;
}
/**
* Gets a Component by its Id
* @param string $id
* @return Component
*/
function &GetComponentById($id)
{
	global $OmniscientBeing;
	//if(!isset($OmniscientBeing[$id]))
	//	$OmniscientBeing[$id] = unserialize($_SESSION["NOLOH".$id]);
	return $OmniscientBeing[$id];
}
/**
* Determines whether a variable holds an array.<br>
* This is a little more general than {@see PHP_Manual#is_array} because it evaluates true for things like ArrayList.
* @param mixed $x The variable to be tested
* @return bool
*/
function isArray($x)
{
	return $x instanceof ArrayList || is_array($x) || $x instanceof ArrayObject;
}
/**
* Determines the width and height in pixels of a string
* @param string $str String to be tested
* @param mixed $width Specified width
* @param mixed $height Specified height
* @param integer $fontSize Font size
* @return array Array whose 0th index is the computed width, and 1st index is the computed height
*/
function AutoWidthHeight($str, $width=System::Auto, $height=System::Auto, $fontSize=12)
{
	$retArray = array($width, $height);
	$fontPath = NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath().'Fonts/times.ttf';
	if($width == System::Auto || $width == System::AutoHtmlTrim)
	{
		$bbox = imagettfbbox($fontSize, 0, $fontPath, $str);
		$retArray[0] = $bbox[4]-$bbox[6] + 1;
		if($height == System::Auto || $height == System::AutoHtmlTrim)
			$retArray[1] = $bbox[1]-$bbox[7] + 5;
	}
	elseif($height == System::Auto || $height == System::AutoHtmlTrim)
	{
		$lines = explode("\n", str_replace("\r", '', $str));
		$ntext = '';
		$lineCount = count($lines);
		for($i=0; $i<$lineCount; ++$i)
		{
			$nline = '';
			$words = explode(' ', $lines[$i]);
			$wordCount = count($words);
			for($j=0; $j<$wordCount; ++$j)
			{
				$bbox = imagettfbbox($fontSize, 0, $fontPath, $nline.$words[$j]);
				if($bbox[4]-$bbox[6]+1 > $width)
					if($nline == '')
						$ntext .= $words[$j] . "\n";
					else 
					{
						$ntext .= $nline . "\n";
						$nline = $words[$j];
					}
				else 
					$nline .= $words[$j].' ';
			}
			$ntext .= $nline . "\n";
		}
		$bbox = imagettfbbox($fontSize, 0, $fontPath, $ntext);
		$retArray[1] = $bbox[1]-$bbox[7] + 5;
	}
	return $retArray;
}

?>