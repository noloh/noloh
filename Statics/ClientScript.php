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
	private function ClientScript() {}
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
	 */
	static function Queue($component, $codeOrFunction, $paramsArray=array(), $replace=true, $priority=Priority::Medium)
	{
		$id = $component->Id;
		if($GLOBALS['_NQueueDisabled'] != $id)
		{
			if(preg_match('/(?:;|})\s*?\z/', $codeOrFunction))
				$paramsArray = null;
			elseif(is_array($paramsArray))
				$paramsArray = array_map(array('ClientEvent', 'ClientFormat'), $paramsArray);
			elseif($paramsArray === null)
				$paramsArray = array();
			else 
				$paramsArray = array(ClientEvent::ClientFormat($paramsArray));
			
			if(!isset($_SESSION['_NFunctionQueue'][$id]))
				$_SESSION['_NFunctionQueue'][$id] = array();
			if($replace)
				$_SESSION['_NFunctionQueue'][$id][$codeOrFunction] = array($paramsArray, $priority);
			else
				$_SESSION['_NFunctionQueue'][$id][] = array($codeOrFunction, $paramsArray, $priority);			
		}
	}
	/**
	 * @ignore 
	 */
	static function RaceAdd()
	{
		
	}
	/**
	 * Queues either a JavaScript function or a full JavaScript statement associated with a specific Component to be executed on the client AFTER a race condition is met. <br>
	 * The code will not be sent to the client, until the given Component has shown.<br>
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
		if(!$condition instanceof ClientEvent)
		{	
			if(preg_match('/^[a-z$_][\w$]*$/i', $condition))
				$condition = "function(){return typeof($condition) != 'undefined';}";
			else
				$condition = "function(){return $condition;}";
//			System::Log($condition);
			$condition = new ClientEvent($condition);	
		}
		if(!$codeOrFunction instanceof ClientEvent)
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
					$paramsArray = array_map(array('ClientEvent', 'ClientFormat'), $paramsArray);
					$paramsArray = implode(',', $paramsArray);
				}
				elseif($paramsArray !== null)
					$paramsArray = array(ClientEvent::ClientFormat($paramsArray));
				$codeOrFunction = 'function(){' . $codeOrFunction . '('. $paramsArray .')}'; 	
			}
//			System::Log($codeOrFunction);
			$codeOrFunction = new ClientEvent($codeOrFunction);
		}
		self::AddNOLOHSource('RaceCall.js');
		ClientScript::Queue($component, '_NChkCond', array($condition, $codeOrFunction), /*$replace*/false, $priority);
	}
	/**
	 * Adds a Javascript script file to be run immediately on the client <br>
	 * The server will keep track of which files have been added so that the same file will not be sent to the client twice.<br>
	 * {@see ClientScript::Add} to add actual code as opposed to files.
	 * @param string $path A path to the javascript file.
	 * @param bool $combine Whether you want the source file to be combined with other source files, or added separately.
	 */
	static function AddSource($path, $combine=true)
	{
		if(!isset($_SESSION['_NScriptSrcs'][$path]))
		{
			if($combine)
				$_SESSION['_NScriptSrc'] .= file_get_contents($path);
			else
			{
				self::AddNOLOHSource('AddExternal.js');
				$_SESSION['_NScriptSrc'] .= '_NAddExtSource(\'' . $path . '\');';
			}
			$_SESSION['_NScriptSrcs'][$path] = true;
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
				self::Queue($component, '_NSfSet', $paramsArray, false, Priority::High);
			}
		}
	}
	/**
	 * Observes a JavaScript property so that it is automatically updated on the server as well, under an optional alias.
	 * Note: This is slightly less efficient than manually calling the NOLOH built-in JavaScript function _NSetProperty when
	 * the property is changed in order to inform the server since the latter happens only when necessary, while the property
	 * observers will result in executing some relatively light code with every server request. Clearly, the former method is
	 * preferred for its on-demand nature, while usage of ClientScript::Observe is often left to either less experienced
	 * NOLOH developers or in cases when modifying the JavaScript in any way is for some reason impossible, thus not lending to
	 * an opportune moment to call _NSetProperty on-demand.
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
}
?>