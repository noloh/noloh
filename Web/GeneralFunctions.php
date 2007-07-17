<?php
/**
* Throws an Exception, and destroy the Session, similar to die()
* @param mixed|specifies Error Message to throw.
 */
function BloodyMurder($whatErrorMessage)
{
	session_destroy();
	session_unset(); 
	die($whatErrorMessage);
}
/**
* A String split that works like most splits. It <b>always</b> returns an array of parts
* @param string|What String to split.
* @param string|What Delimeter to split by.
* @param integer|Set a limit of times to split, default is null.
* @return array of parts
 */
function SaneSplit($whatString, $whatDelimeter, $whatLimit=null)
{
	if(strpos($whatString, $whatDelimeter)) 
		return $whatLimit == null ?
			split($whatDelimeter, $whatString) :
			split($whatDelimeter, $whatString, $whatLimit);
			/*
		if($whatLimit == null)
			return split($whatDelimeter, $whatString);
		else 
			return split($whatDelimeter, $whatString, $whatLimit);*/
	else
	{
		$arr = array();
		$arr[] = $whatString;
		return $arr;
	}
}
/**
* Gets the size of session in bytes.
* @return size of session in bytes
 */
function MemoryGetUsage()
{
	return strlen(serialize($GLOBALS['OmniscientBeing']));
}
/**
* Gets the Absolute path of a the $path parameter.
* @param string|path
* @return string|absolute path
 */
function GetAbsolutePath($path)
{
	if($path[0] == "\\" || $path[0] == "/")
		return realpath(NOLOHConfig::GetBaseDirectory().$path);
	elseif(strpos($path, "http://") >= 0)
		return $path;
	else
		return realpath($path);
}
/**
* Gets the global specified by the $whatName parameter.
* If Not found returns null.
* @param string|name of Global
* @return mixed|Global Variable
*/
function GetGlobal($whatName)
{
	if(isset($_SESSION["NOLOHGlobal" . $whatName]))
		return $_SESSION["NOLOHGlobal" . $whatName];
	else
		return null;
}
/**
* Sets the global specified by the $whatName, $whatValue parameter.
* Variable must be declared, or it will throw an exception.
* If Not found returns null.
* @param string|name of Global
* @param mixed|value of Global
*/
function SetGlobal($whatName, $whatValue)
{
	if(isset($_SESSION["NOLOHGlobal" . $whatName]))
		$_SESSION["NOLOHGlobal" . $whatName] = &$whatValue;
	else
		//die("Variable " . $whatName . " not declared.");
		DeclareGlobal($whatName, $whatValue);
}
/**
* Declares a global variable, and sets it by the $whatName, $whatValue parameter.
* @param string|name of Global
* @param mixed|value of Global
*/
function DeclareGlobal($whatName, $whatValue=null)
{
	if(isset($_SESSION["NOLOHGlobal" . $whatName])){}
		//die("Variable " . $whatName . " is already declared.");
	else
		$_SESSION["NOLOHGlobal" . $whatName] = &$whatValue;
}
/**
* Alert a string specified by the $msg variable.
* <br><code>Alert("Hi my name is Asher");</code>
* @param string|Message to be Alerted
*/
function Alert($msg)
{
	AddScript("alert('" . addslashes(str_replace("\n","",$msg)) . "');");
	//$_SESSION['NOLOHScript'] .= 'alert("' . str_replace('"','\"',str_replace("'","\'",str_replace("\n","",$msg))) . '");';
}
/**
* Adds Javascript code to be run immediately on client.
* <br> See AddScriptSrc($src) to add actual script files.
* @param string|code of script, not path to script
*/
function AddScript($script, $priority=Priority::Medium)
{
	//if(isset($_SESSION['UnlockNOLOHDebug']) && $_SESSION['UnlockNOLOHDebug'] == 'mddevmddev')
	//	$_SESSION['NOLOHScript'] .= $script . ";";
	//else
		/*$_SESSION['NOLOHScript'] .= str_replace('"','\"',str_replace("'","\'",str_replace("\n","",$script))) . ';';*/
		$_SESSION['NOLOHScript'][$priority] .= $script . ";";
}
/**
* Gets operating system that user is using.
* @return string|shorthand of operating system(e.g., nt, mac, ...)
*/
function GetOperatingSystem()
{
	return "win";
}
/**
* Gets Browser that user is using.
* @return string|shorthand of browser(e.g., ie, mozilla, ...)
*/
function GetBrowser()
{
	return $_SESSION["NOLOHBrowser"];
	//return GetGlobal("BrowserName");
}
/**
* Adds a Javascript script to be run immediately on client, or be written to client depending on case.
* <br> See AddScriptSrc($src) to add actual script files.
* @param string|path to script, not code of script
*/
function AddScriptSrc($src)
{
	if(!in_array($src, $_SESSION['NOLOHScriptSrcs']))
	{
		print(file_get_contents($src));
		/*if(isset($_SESSION['UnlockNOLOHDebug']) && $_SESSION['UnlockNOLOHDebug'] == 'mddevmddev')
			$_SESSION['NOLOHSrcScript'] .= $buffer;
		else
			$_SESSION['NOLOHSrcScript'] .= $buffer;*/
		$_SESSION['NOLOHScriptSrcs'][] = $src;
	}
}

function QueueClientFunction(Component $whatObj, $functionName, $paramsArray, $replace=true, $priority=Priority::Medium)
{
	if(!isset($GLOBALS["PropertyQueueDisabled"]))
	{
		$objId = $whatObj->DistinctId;
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
function UnlockNOLOHDebug($password)
{
	$_SESSION['UnlockNOLOHDebug'] = $password;
}
/**
* Gets a Component by it's DistinctId
* @param string|DistinctId
* @return Component|Component with DistinctId specified by $whatId
*/
function &GetComponentById($whatId)
{
	global $OmniscientBeing;
	//if(!isset($OmniscientBeing[$whatId]))
	//	$OmniscientBeing[$whatId] = unserialize($_SESSION["NOLOH".$whatId]);
	return $OmniscientBeing[$whatId];
}
/**
* @ignore
*/
function ArrayRestoreValues(&$ar)
{
	$ItemCount = count($ar);
	for($i=0; $i<$ItemCount; $i++)
		if(is_object($ar[$i]) && get_class($ar[$i]) == "Pointer")
			$ar[$i] = $ar[$i]->Dereference();
		elseif(isArray($ar[$i]))
			ArrayRestoreValues($ar[$i]);
}
/**
* @ignore
*/
function RefCount(&$thing)
{
	ob_start();
	debug_zval_dump(&$thing);
	$str = ob_get_contents();
	$str = str_replace(")", "", $str);
	$refcount = substr($str, strpos($str, "refcount(")+9);
	ob_end_clean();
	return $refcount-3;
}
/**
* Returns the Associative Name of the index of an array specified by the $whatArray, and $whatPosition paramaters.
* @param array|
* @param  integer|position in array
* @return mixed|Associative Key
*/
function KeyName($whatArray, $whatPosition) 
{
	if (($whatPosition < 0) || ($whatPosition >= count($whatArray) ))
		return "NULL";
	reset($whatArray);
	for($i = 0;$i < $whatPosition; $i++) next($whatArray);
		return key($whatArray);
}
/**
* Determines whether something is an array
* @param mixed|variable to be tested
* @return bool|whether $x is an array
*/
function isArray($x)
{
   return (bool)($x instanceof ArrayAccess || is_array($x));
}
/**
* Determines the width and height of a string
* @param string|String to be tested
* @param mixed|Specified width
* @param mixed|Specified height
* @param integer|Font size
* @return array|Array whose 0th index is the computed width, and 1st index is the computed height
*/
function AutoWidthHeight($str, $width=System::Auto, $height=System::Auto, $fontSize=12)
{
	$retArray = array($width, $height);
	if($width == System::Auto || $width == System::AutoHtmlTrim)
	{
		$bbox = imagettfbbox($fontSize, 0, NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Fonts/times.ttf", 
			$str);
		$retArray[0] = $bbox[4]-$bbox[6];
		if($height == System::Auto || $height == System::AutoHtmlTrim)
			$retArray[1] = $bbox[1]-$bbox[7] + 4;
	}
	elseif($height == System::Auto || $height == System::AutoHtmlTrim)
	{
		$str = str_replace("\r", "", $str);
		$lines = explode("\n", $str);
		$ntext = "";
		foreach($lines as $line)
		{
			$words = explode(" ", $line);
			$nline = "";
			foreach($words as $word)
			{
				$bbox = imagettfbbox($fontSize, 0, NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Fonts/times.ttf", 
					$nline.$word);
				if($bbox[4]-$bbox[6] > $width)
				{
					$ntext .= $nline . "\n";
					$nline = $word;
				}
				else 
					$nline .= $word." ";
			}
		}
		$bbox = imagettfbbox($fontSize, 0, NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Fonts/times.ttf", 
			$ntext==null?$str:$ntext);
		$retArray[1] = $bbox[1]-$bbox[7] + 6;
	}
	return $retArray;
}
/**
* Returns true if the object has this class as one of its parents or is itself of that class
* @param object|
* @param string|
* @return bool|

function IsSubclassWeaklyOf($Object, $ClassAsString)
{
	return is_subclass_of($Object, $ClassAsString) || get_class($Object) == $ClassAsString;
}*/
/**
* @ignore
*


function CreatePointer($obj)
{
	return "NOLOH" . $obj->DistinctId;
}
/**
* @ignore
*
function DereferencePointer($ptr)
{
	$splitStr = split("&NOLOH", $ptr, 2);
	//echo "Derefing " . $splitStr[1] . " ";
	return GetComponentById($splitStr[1]);
}
/**
* @ignore

function IsPointer($var)
{
	if(is_string($var))
	{
		echo strpos($var, "NOLOH");
		if(strpos($var, "NOLOH") === 0)
			return true;
	}
	return false;
}

function &getObjectById($whatId)
{
	$Ancestors = &$_SESSION['Ancestors' . $whatId];
	$RetStr = '$_SESSION["StartUpPage"]';
	
	//echo get_class($Ancestors) . " hi";
	
	$size = $Ancestors->Count();
	for($i = 0; $i < $size; $i++)
	{
		$RunThisString = '$ind = Ancestors->Item[$i];';
		eval($RunThisString);
		$RetStr = $RetStr . '->Controls[' . $ind . ']';
	}
	$RunThisString = 'return ' . $RetStr . ';';
	echo $RetStr;
	//eval($RunThisString);
	//return $Obj;
}

function &getParentOf($whatObject)
{
	
}*/

?>