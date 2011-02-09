<?php
/**
 * Object class
 * 
 * An Object is the top-most parent of any NOLOH class. Its purpose is two-fold: First of all, for organizing classes, it is both convenient and
 * semantically elegant to have a single base for all of your classes. Secondly, it comes equipped with the most basic and most universal
 * syntactic sugars of NOLOH, thus allowing for even your most basic classes (e.g., classes used primarily for storing properties) to take
 * advantage of these great features. These sugars include calling Get or Set methods for you for properties that are not accessible, as well as cascading
 * method and Set method calls, illustrated in the following example, as well as many others not listed here.
 * 
 * <pre>
 * class Foo extends Object
 * {
 *   // A property not visible outside of this class
 *   private $Property;
 *   // An accessor method for the private property
 *   function GetProperty()
 *   {
 *     System::Alert('GetProperty has been called!');
 *     return $this->Property;
 *   }
 *   // A mutator method for the private property
 *   function SetProperty($value)
 *   {
 *     System::Alert('SetProperty has been called!');
 *     $this->Property = $value;
 *   }
 * 	 // A generic method with a return
 * 	 function DoSomething()
 *   {
 * 		System::Alert('Doing something!');
 * 		return 17;
 *   }
 * }
 * 
 * // Instantiate a new Foo object
 * $foo = new Foo();
 * // Automatically calls the SetProperty method, triggering an Alert and setting the private variable to 'Hello'
 * $foo->Property = 'Hello';
 * // Automatically calls the GetProperty method, triggering an Alert and setting the local variable to 'Hello'
 * $property = $foo->Property;
 * // Automatically calls methods and overrides their returns to return the object back for more actions.
 * // A total of 3 Alerts will trigger, starting with 'Doing something!' since the method calls get resolved from left to right.
 * $foo->CasDoSomething()->CasSetProperty('Ummm...')->CasProperty('Goodbye');
 * </pre>
 * 
 * For a more detailed discussion about the rich subject of NOLOH's syntactic sugars, please explore http://dev.noloh.com/#/articles/
 * 
 * @package System
 */
abstract class Object
{
	/**
	 * @ignore
	 */
	function Object()	{}
	/**
	 * Returns whether or not the object has a specified property, in the sense of either variables or a Get method.
	 * @param string $property
	 * @return boolean
	 */
	function HasProperty($property)
	{
		return property_exists($this, $property) || method_exists($this, 'Get'.$property);
	}
	/**
	 * Returns whether or not the object has a specified method. Identical to PHP's native method_exists function.
	 * @param string $method
	 * @return boolean
	 */
	function HasMethod($method)
	{
		return method_exists($this, $method);
	}
	/**
	 * @ignore
	 */
	function &__get($nm)
	{
		if(method_exists($this, $func = 'Get' . $nm))
		{
			$ret = $this->$func();
			return $ret;
		}
		elseif(method_exists($this, $nm))
			$ret = new ServerEvent($this, $nm);
		elseif(property_exists($class = get_class($this), $var = '_In' . $nm))
		{
			$class = new ReflectionClass($class);
			$ret = new InnerSugar($this, array(), $nm, $class->getStaticPropertyValue($var));
		}
		elseif(method_exists($this, $func = 'get' . $nm))
			$ret = $this->$func();
		elseif($innerSugar = $this->ParentInnerSugar())
			$ret = $innerSugar->$nm;
		else
			BloodyMurder('Could not get property ' . $nm . ' because it does not exist or is write-only in the class ' . get_class($this) . '.');
		return $ret;
	}
	/**
	 * @ignore
	 */
	function __set($nm, $val)
	{
		if(method_exists($this, $func = 'Set' . $nm))
		{
			$this->$func($val);
			return $val;
		}
		elseif(strpos($nm, 'All') === 0 && $this instanceof Iterator)
		{
			$prop = substr($nm, 3);
			$method = 'Set' . $prop;
			foreach($this as $obj)
				if(is_object($obj) && (property_exists($obj, $prop) || ($obj instanceof Object && method_exists($obj, $method)) || ($obj instanceof Control && strpos($prop, 'CSS')===0)))
					$obj->$prop = $val;
			return $val;
		}
		elseif(method_exists($this, $func = 'set' . $nm))
		{
			$this->$func($val);
			return $val;
		}
		elseif($innerSugar = $this->ParentInnerSugar())
			return $innerSugar->$nm = $val;
		elseif(preg_match('/\.(\w+)$/i', $nm, $matches))
			return self::__set($matches[1], $args);
		else
			BloodyMurder('Could not set property ' . $nm . ' because it does not exist or is read-only in the class ' . get_class($this) . '.');
	}
	/**
	 * @ignore
	 */
	function __call($nm, $args)
	{
		if(strpos($nm, 'Cas') === 0)
		{
			$prop = substr($nm, 3);
			if(method_exists($this, $prop))
			{
				call_user_func_array(array(&$this, $prop), $args);
				return $this;
			}
			elseif(method_exists($this, $setProp = 'Set'.$prop))
			{
				call_user_func_array(array(&$this, $setProp), $args);
				return $this;
			}
			elseif(property_exists($this, $prop))
				$this->$prop = $args[0];
			else 
				BloodyMurder('The function ' . $nm . ' could not be called because it does not exist or is not in scope, nor does the method or property ' . $prop . ' exist in the class ' . get_class($this) . '.');
		}
		elseif(strpos($nm, 'Get') === 0)
		{
			$prop = substr($nm, 3);
			if(property_exists($this, $prop))
				return $this->$prop;
			if(method_exists($this, $prop))
			{
				$class = new ReflectionClass('ServerEvent');
				array_unshift($args, $this, $prop);
				return $class->newInstanceArgs($args);
			}
			else 
				BloodyMurder('The function ' . $nm . ' could not be called because it does not exist or is not in scope, nor does the property ' . $prop . ' exist in the class ' . get_class($this) . '.');
		}
		elseif(strpos($nm, 'Set') === 0)
		{
			$prop = substr($nm, 3);
			if(property_exists($this, $prop))
				return $this->$prop = $args[0];
			elseif(preg_match('/\.(\w+)$/i', $nm, $matches))
				return call_user_func_array(array(&$this, 'Set' . $matches[1]), $args);
			elseif(isset($GLOBALS['_NQueueDisabled']) && $GLOBALS['_NQueueDisabled']===$this->Id)
				return;
			else
				BloodyMurder('The function ' . $nm . ' could not be called because it does not exist or is not in scope, nor does the property ' . $prop . ' exist in the class ' . get_class($this) . '.');
		}
		elseif(strpos($nm, 'All') === 0 && $this instanceof Iterator)
		{
			$method = substr($nm, 3);
			foreach($this as $obj)
				if(is_object($obj) && method_exists($obj, $method))
					call_user_func_array(array(&$obj, $method), $args);
		}
		elseif($innerSugar = $this->ParentInnerSugar())
			call_user_func_array(array(&$innerSugar, $nm), $args);
		else 
			BloodyMurder('The function ' . $nm . ' could not be called because it does not exist in, or is not in scope of, the class ' . get_class($this) . '.');
	}
	private function ParentInnerSugar()
	{
		if($this->HasProperty('ParentId') && ($parent = Component::Get($this->ParentId)))
		{
			$class = new ReflectionClass(get_class($parent)); 
        	$staticProperties = $class->getStaticProperties(); 
        	foreach ($staticProperties as $name => $value) 
        		if(preg_match('/^_In(.+)$/i', $name, $result)) 
        			if($parent->{$result[1]} === $this)
						return new InnerSugar($parent, array(), $result[1], $value);
		}
		return false;
	}
}

?>