<?php
/**
 * ClientScript class
 * 
 * This class contains the various static functions and constants pertaining to executing client-side scripts, namely, JavaScript.
 * This is only necessary either for very advanced, custom functionality, or for extending existing JavaScript. Under normal
 * circumstances, coding JavaScript is strongly discouraged as NOLOH provides you with all the tools to work with one unified
 * language.
 * 
 * @package Statics
 */
final class ClientScript
{
	const JQuery = 'jquery', JQueryUI = 'jqueryui', Angular = 'angularjs', 
	MooTools = 'mootools', Dojo = 'dojo', Prototype = 'prototype';
	
	private function __construct() {}
	/**
	 * Adds Javascript code to be run immediately on the client.<br>
	 * {@see ClientScript::AddSource} to add script files as opposed to code, or {$see ClientScript::Queue} to run code when a specific object has shown.
	 * @param string $code Valid string of JavaScript code.
	 * @param Priority::High|Priority::Medium|Priority::Low $priority Determines the order in which scripts run. Can be: Priority::Low, Priority::Medium, or Priority::High 
	 */
	static function Add($code, $priority=Priority::Medium)
	{
		$_SESSION['_NScript'][$priority] .= $code;
	}
	/**
	 * Queues either a JavaScript function or a full JavaScript statement associated with a specific Component to be executed on the client. <br>
	 * The code will not be sent to the client, until the given Component has shown.<br>
	 * <pre>
	 * 	ClientScript::Queue($this, 'alert', array("This Component's Id is $this->Id"));
	 * </pre>
	 * @param Component $component One or more components that the scripts will be dependent on, i.e., they will not get sent to the client if the dependencies have not shown.
	 * @param string $codeOrFunction The name of the JavaScript function or valid JavaScript code ending with a semicolon
	 * @param mixed $paramsArray An array of parameters passed into the function
	 * @param boolean $replace
	 * @param mixed $priority Determines the order in which scripts run. Can be: Priority::Low, Priority::Medium, or Priority::High 
	 * @param boolean $formatParams Determines if the parameters will be formatted.
	 */
	static function Queue($component, $codeOrFunction, $paramsArray = array(), $replace = true, $priority = Priority::Medium, $formatParams = true)
	{
		$id = $component->Id;
		if (!isset($GLOBALS['_NQueueDisabled']) || $GLOBALS['_NQueueDisabled'] != $id)
		{
			if ($formatParams)
			{
				if (is_array($paramsArray))
				{
					$paramsArray = array_map(array('ClientScript', 'ClientFormat'), $paramsArray);
				}
				elseif ($paramsArray === null)
				{
					$paramsArray = array();
				}
				else
				{
					$paramsArray = array(ClientScript::ClientFormat($paramsArray));
				}
			}

			// Checking for code that doesn't accept parameters
			if (preg_match('/(?:;|})\s*?\z/', $codeOrFunction))
			{
				$paramsArray = null;
			}
			
			if (!isset($_SESSION['_NFunctionQueue'][$id]))
			{
				$_SESSION['_NFunctionQueue'][$id] = array();
			}
			if ($replace)
			{
				$_SESSION['_NFunctionQueue'][$id][$codeOrFunction] = array($paramsArray, $priority);
			}
			else
			{
				$_SESSION['_NFunctionQueue'][$id][] = array($codeOrFunction, $paramsArray, $priority);
			}
		}
	}
	private static function AddMTime($path)
	{
		return $path . '?mtime=' . filemtime(GetAbsolutePath($path));
	}
	/**
	 * Adds a Javascript script file to be run immediately on the client after satisfying a race condition. <br>
	 * The server will keep track of which files have been added so that the same file will not be sent to the client twice.<br>
	 * {@see ClientScript::Add} to add actual code as opposed to files.
	 * @param string $path A path to the javascript file.
	 */
	static function RaceAddSource($condition, $path)
	{
		if(!isset($_SESSION['_NScriptSrcs'][$path]))
		{
			self::AddNOLOHSource('AddExternal.js');
			$path2 = Configuration::That()->AddMTimeToExternals ? self::AddMTime($path) : $path;
			ClientScript::RaceQueue(WebPage::That(), $condition, '_NAddExtSource', array($path2));
			$_SESSION['_NScriptSrcs'][$path] = true;
		}
	}
	/**
	 * Queues either a JavaScript function or a full JavaScript statement associated with a specific Component to be executed on the client AFTER a race condition is met. <br>
	 * The code will not be sent to the client, until the given Component has shown.<br>
	 * The function parameters will NOT be formatted when queued.
	 * <pre>
	 * 	ClientScript::RaceQueue($this, 'someWidget.state == "ready"', alert', array("someWidget is ready for use"));
	 * </pre>
	 * @param Component $component One or more components that the scripts will be dependent on, i.e., they will not get sent to the client if the dependencies have not shown.
	 * @param mixed $condition A statement, condition, JavaScript object, JavaScript function, or ClientEvent.
	 * @param string $codeOrFunction The name of the JavaScript function or valid JavaScript code ending with a semicolon
	 * @param mixed $paramsArray An array of parameters passed into the function
	 * @param boolean $replace
	 * @param mixed $priority Determines the order in which scripts run. Can be: Priority::Low, Priority::Medium, or Priority::High 
	 */
	static function RaceQueue($component, $condition, $codeOrFunction, $paramsArray=null, $replace=true, $priority=Priority::Medium)
	{
		if ($condition instanceof  ClientEvent)
		{
			$condition = $condition->ExecuteFunction;
		}
		else
		{	
			if (preg_match('/^[a-z$_][\w$()\']+\.[\w$()\'.]+$/i', $condition))
			{
				$namespaces = explode('.', $condition);
			
				$condition = "function(){return (typeof({$namespaces[0]}) != 'undefined' && _NNS(" . $namespaces[0] . ',' . 
					ClientScript::ClientFormat(array_slice($namespaces, 1)) . 
					', true))}';
			}
			elseif (preg_match('/^([a-z$_][\w$()\']+\.[\w$()\'.]+)\s*?([!=<>]{1,3})\s*(.*)$/i', $condition, $parts))
			{
				$namespaces = explode('.', $parts[1]);
				$condition = "function() {return (typeof({$namespaces[0]}) != 'undefined' && _NNS(" . $namespaces[0] . ',' . 
						ClientScript::ClientFormat(array_slice($namespaces, 1)) . 
						", true) && ({$parts[1]} {$parts[2]} {$parts[3]}))}";
			}
			elseif (preg_match('/^(?:true|false|\d+)$/i', $condition) || !(preg_match('/^[a-z$_][\w$\']+$/i', $condition))) 
				$condition = "function(){return $condition;}";
			else
				$condition = "function(){return typeof($condition) != 'undefined';}";	
		}
		if ($codeOrFunction instanceof  ClientEvent)
		{
			$codeOrFunction = $codeOrFunction->ExecuteFunction;
		}
		else
		{
			if(preg_match('/(?:;|})\s*?\z/', $codeOrFunction))
			{
				$paramsArray = null;
				$codeOrFunction = 'function(){' . $codeOrFunction . '}';
			}
			else
			{
				if(is_array($paramsArray))
				{
					$paramsArray = array_map(array('ClientScript', 'ClientFormat'), $paramsArray);
				}
				elseif($paramsArray !== null)
				{
					$paramsArray = array(ClientScript::ClientFormat($paramsArray));
				}
				$paramsArray = implode(',', $paramsArray);
				$codeOrFunction = 'function(){' . $codeOrFunction . '('. $paramsArray .')}'; 	
			}
		}

		self::AddNOLOHSource('RaceCall.js');
		ClientScript::Queue($component, '_NChkCond', array($condition, $codeOrFunction), /*$replace*/false, $priority, false);
	}
	/**
	 * Adds a Javascript script file to be run immediately on the client <br>
	 * The server will keep track of which files have been added so that the same file will not be sent to the client twice.<br>
	 * {@see ClientScript::Add} to add actual code as opposed to files.
	 * @param string $path A path to the javascript file.
	 * @param bool $combine Whether you want the source file to be combined with other source files, or added separately.
	 * @param bool|null $addMTime Whether mtime is added to the request for caching purposes. Null defaults to Configuration value
	 */
	static function AddSource($path, $combine = true, $addMTime = null)
	{
		if(!isset($_SESSION['_NScriptSrcs'][$path]))
		{
			if ($combine)
			{
				$_SESSION['_NScriptSrc'] .= preg_replace('!//# sourceMappingURL.*?map\n|$!', '', file_get_contents($path));
			}
			else
			{
				self::AddNOLOHSource('AddExternal.js');
				if ($addMTime === null)
				{
					$addMTime = Configuration::That()->AddMTimeToExternals;
				}
				$path2 = $addMTime ? self::AddMTime($path) : $path;
				ClientScript::Add("_NAddExtSource('$path2');", Priority::High);
			}
			$_SESSION['_NScriptSrcs'][$path] = true;
		}
	}
	/**
	 * Adds a 3rd Party Javascript library and prevents multiple versions of the
	 * same library from being used. In the case that a name is provided that is
	 * a ClientScript Static, NOLOH will use the Google CDN hosted version of 
	 * the latest version of the framework
	 * 
	 * See https://developers.google.com/speed/libraries/devguide for a complete
	 * list of versions available
	 * 
	 * @param string $library, shorthand name of the library, or your own name.
	 * @param string $version The version of the library you wish to use.
	 * @param string $path If you would like to provide a path to the library
	 */
	static function AddLibrary($library, $version, $path=null)
	{
		static $libraries = array(
			self::JQuery => '/jquery/VERSION/jquery.min.js', 
			self::JQueryUI => '/jqueryui/VERSION/jquery-ui.min.js', 
			self::Angular => '/angularjs/VERSION/angular.min.js', 
			self::Dojo => '/dojo/VERSION/dojo/dojo.js', 
			self::MooTools => '/mootools/VERSION/mootools-yui-compressed.js', 
			self::Prototype => '/prototype/VERSION/prototype.js'
		);
		$library = strtolower($library);
		if(!isset($_SESSION['_NScriptSrcs'][$library]))
		{
			if(!$path && key_exists($library, $libraries))
			{
				$path = '//ajax.googleapis.com/ajax/libs';
				$path .= str_replace('VERSION', strtolower($version), $libraries[$library]);
			}
			// if($combine)
				// $_SESSION['_NScriptSrc'] .= file_get_contents($path);
			// else
			self::AddNOLOHSource('AddExternal.js');
			ClientScript::Add("_NAddExtSource('$path');", Priority::High);
			$_SESSION['_NScriptSrcs'][$library] = true;
		}
	}
	/**
	 * @ignore
	 */
	static function AddNOLOHSource($fileName, $browserSpecific = false)
	{
		if(!isset($_SESSION['_NScriptSrcs'][$fileName]))
		{
			$path = System::NOLOHPath() . '/JavaScript/';
			if($browserSpecific)
				$path .= $_SESSION['_NIsIE'] ? 'IE/' : 'Standard/';
			$path .= $fileName;
			$_SESSION['_NScriptSrc'] .= file_get_contents($path);
			$_SESSION['_NScriptSrcs'][$fileName] = true;
		}
	}
	/**
	 * Sets a client-side property of some Component to a particular value. By default, all properties will be namespaced with the name of your application (the name of the class that extended WebPage). If you want to use the global space of the object, pass in null. If you want to modify CSS properties through this function, you may use the "style" namespace, however, this is not the ideal way to set CSS properties (see the Controls document for more defails.)
	 * @param Component $component
	 * @param string $property
	 * @param mixed $value
	 * @param string,... $namespaces
	 */
	static function Set($component, $property, $value, $namespaces = Application::Name)
	{
		$id = is_object($component) ? $component->Id : $component;
		if($GLOBALS['_NQueueDisabled'] !== $id)
		{
			
			if($namespaces == null || $namespaces == 'style')
			{
				if($namespaces == 'style')
					$property = 'style.' . $property; 
				if(!isset($_SESSION['_NPropertyQueue'][$id]))
					$_SESSION['_NPropertyQueue'][$id] = array();
				$_SESSION['_NPropertyQueue'][$id][$property] = $value;
			}
			else
			{
				self::AddNOLOHSource('SafeSet.js');
				$args = func_get_args();
				$numArgs = func_num_args();
				$paramsArray = array($component->Id, $property, $value);
				$startClass = Configuration::That()->StartClass;
				if($numArgs === 3)
					array_push($paramsArray, $startClass);
				else
					for($i=3; $i<$numArgs; ++$i)
						array_push($paramsArray, $args[$i] === Application::Name ? $startClass : $args[$i]);
				if($value instanceof RaceClientEvent)
				{
//					$execute =  preg_replace('/^function\(\)\{\s*(?:return ?)?/', 'function(){return ', $value->ExecuteFunction);
					$execute =  preg_replace('/,function\(\)\{\s*(?:return ?)?/', ',function(){return ', $value->ExecuteFunction, 1);
//					What Execute Function Looks like
//					$this->ExecuteFunction = '_NChkCond(' . $condition . ',' . 'function(){' . $this->ExecuteFunction . '});';				
					$execute = substr($execute, 0, -2) . ', function(v){_NSfSet(' . ClientScript::ClientFormat($paramsArray[0]) 
					. ',' . ClientScript::ClientFormat($paramsArray[1]). ',v,';	
					$execute .= implode(',', array_map(array('ClientScript', 'ClientFormat'), array_slice($paramsArray, 3))) . ')});'; 
					self::Queue($component, $execute, null, false, Priority::High);
				}
				else
					self::Queue($component, '_NSfSet', $paramsArray, false, Priority::High);
			}
		}
	}
	/**
	* Returns a Raw object for use in various Client related functions. This
	* allows for better interopability with 3rd party JavaScript libararies.
	* 
	* @param mixed string|ClientEvent The string, or Client that you wish to raw
	*/
	static function Raw($value)
	{
		return (object)array('raw' => $value);
	}
	/**
	 * Observes a JavaScript property so that it is automatically updated on the server as well, under an optional alias.
	 * Note: This is slightly less efficient than manually calling the NOLOH built-in JavaScript function _NSet when
	 * the property is changed in order to inform the server since the latter happens only when necessary, while the property
	 * observers will result in executing some relatively light code with every server request. Clearly, the former method is
	 * preferred for its on-demand nature, while usage of ClientScript::Observe is often left to either less experienced
	 * NOLOH developers or in cases when modifying the JavaScript in any way is for some reason impossible, thus not lending to
	 * an opportune moment to call _NSet on-demand.
	 * @param Component|string $objOrId
	 * @param string $clientPropertyName
	 * @param string $serverPropertyAlias
	 */
	static function Observe($objOrId, $clientPropertyName, $serverPropertyAlias = null)
	{
		$obj = is_object($objOrId) ? $objOrId : Component::Get($objOrId);
		self::AddNOLOHSource('Observe.js');
		self::Queue($obj, '_NObserve', array($objOrId, $clientPropertyName, $serverPropertyAlias), false, Priority::Low);
	}
	/**
	* Formats parameter for sending to the client in conjuction with other
	* ClientScript functions. Only necessary in special cases, NOLOH 
	* automatically calls this function for you in most cases.
	* 
	* @param mixed $param
	* @return mixed
	*/
	static public function ClientFormat($param)
	{
		if(is_string($param))
			return '"'.str_replace(array('"', "\r\n", "\n"), array('\\"', ' ', ' '), $param).'"';
		elseif(is_int($param) || is_float($param))
			return $param;
		elseif(is_bool($param))
			return ($param?'true':'false');
		elseif($param === null)
			return 'null';
		elseif(is_array($param))
		{
			$isList = true;
			$count = count($param);
        	for ($i=0, reset($param); $i<$count; ++$i, next($param))
            	if (key($param) !== $i)
            	{
            		$isList = false; 
            		break; 
            	}
            if($isList)
            {
				$tmpArr = array();
				foreach($param as $val)
					$tmpArr[] = self::ClientFormat($val);
				return '[' . implode(',', $tmpArr) . ']';
			}
			else
			{
				$str = '{';
				foreach($param as $key => $val)
					$str .= self::ClientFormat($key) . ':' . self::ClientFormat($val) . ',';
				return rtrim($str, ',') . '}';
			}
		}
		elseif($param instanceof Component)
			return '"' . $param->Id . '"';
		elseif($param instanceof ClientEvent)
		{
			$func = $param->ExecuteFunction;
			if (preg_match('/^\s*?function\s*\(.*\)?\s*?\{.*\}\s*?$/si', $func))
				return $func;
			else
				return 'function(){' . $param->GetEventString(ClientEvent::Inline, null) .'}';
		}
		elseif($param instanceof stdClass)
		{
			$raw = $param->raw;
			return ($raw instanceof ClientEvent)?rtrim($raw->GetEventString(ClientEvent::Inline, null), ';'):$raw;
		}
		elseif(is_object($param))
			BloodyMurder('Objects can not be converted to the client');
	}
}
?>