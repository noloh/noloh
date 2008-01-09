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
	session_destroy();
	session_unset(); 
	die($message);
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
	return isset($_SESSION['NOLOHGlobals'][$name]) ? $_SESSION['NOLOHGlobals'][$name] : null;
}
/**
* Gets a global that lasts as long as the application does, as opposed to PHP's globals that are forgotten as soon as the server is left.
* @param string $name
* @param mixed $value
*/
function SetGlobal($name, $value)
{
	$_SESSION['NOLOHGlobals'][$name] = &$value;
}
/**
* Alert a string specified by the $msg variable.
* <br><code>Alert("Hi, my name is Asher!");</code>
* @param string $msg Message to be Alerted
*/
function Alert($msg)
{
	AddScript('alert(\'' . str_replace(array("\n","\r"),array('\n','\r'),$msg) . '\')');
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
	//	$_SESSION['NOLOHScript'] .= $script . ";";
	//else
		/*$_SESSION['NOLOHScript'] .= str_replace('"','\"',str_replace("'","\'",str_replace("\n","",$script))) . ';';*/
		$_SESSION['NOLOHScript'][$priority] .= $script . ';';
}
/**
* Adds a Javascript script file to be run immediately on the client <br>
* The server will keep track of which files have been added so that the same file will not be sent to the client twice.<br>
* {@see AddScript($src)} to add actual code as opposed to files.
* @param string $path A path to the javascript file.
*/
function AddScriptSrc($path)
{
	if(!isset($_SESSION['NOLOHScriptSrcs'][$path]))
	{
		$_SESSION['_NScriptSrc'] .= (file_get_contents($path));
		$_SESSION['NOLOHScriptSrcs'][$path] = true;
	}
}
/**
* @ignore
*/
function AddNolohScriptSrc($src, $browserSpecific = false)
{
	if(!isset($_SESSION['NOLOHScriptSrcs'][$src]))
	{
		$path = NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath().'Javascripts/';
		if($browserSpecific)
			$path .= $_SESSION['NOLOHIsIE'] ? 'IE/' : 'Standard/';
		$path .= $src;
		$_SESSION['_NScriptSrc'] .= file_get_contents($path);
		$_SESSION['NOLOHScriptSrcs'][$src] = true;
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
		if(!isset($_SESSION['NOLOHFunctionQueue'][$objId]))
			$_SESSION['NOLOHFunctionQueue'][$objId] = array();
		if($replace)
			$_SESSION['NOLOHFunctionQueue'][$objId][$functionName] = array($paramsArray, $priority);
		else
			$_SESSION['NOLOHFunctionQueue'][$objId][] = array($functionName, $paramsArray, $priority);			
	}
}
/**
* @ignore
*/
function GetBrowser()
{
	return $_SESSION['NOLOHBrowser'];
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
	//if(!isset($OmniscientBeing[$whatId]))
	//	$OmniscientBeing[$whatId] = unserialize($_SESSION["NOLOH".$whatId]);
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
   return (bool)($x instanceof ArrayList || is_array($x) || $x instanceof ArrayObject);
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
	if($width == System::Auto || $width == System::AutoHtmlTrim)
	{
		$bbox = imagettfbbox($fontSize, 0, NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath().'Fonts/times.ttf', 
			$str);
		$retArray[0] = $bbox[4]-$bbox[6];
		if($height == System::Auto || $height == System::AutoHtmlTrim)
			$retArray[1] = $bbox[1]-$bbox[7] + 7;
	}
	elseif($height == System::Auto || $height == System::AutoHtmlTrim)
	{
		$str = str_replace("\r", '', $str);
		$lines = explode("\n", $str);
		$ntext = '';
		foreach($lines as $line)
		{
			$words = explode(' ', $line);
			$nline = '';
			foreach($words as $word)
			{
				$bbox = imagettfbbox($fontSize, 0, NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath().'Fonts/times.ttf', 
					$nline.$word);
				if($bbox[4]-$bbox[6] > $width)
				{
					$ntext .= $nline . "\n";
					$nline = $word;
				}
				else 
					$nline .= $word.' ';
			}
		}
		$bbox = imagettfbbox($fontSize, 0, NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath().'Fonts/times.ttf', 
			$ntext==null?$str:$ntext);
		$retArray[1] = $bbox[1]-$bbox[7] + 7;
	}
	return $retArray;
}

?>