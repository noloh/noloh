<?php
/**
 * Object class
 * 
 * An Object is the top-most parent of any NOLOH class. Its purpose is two-fold: First of all, for organizing classes, it is convenient and
 * logically elegant to have a base for all of your classes. Secondly, it comes equipped with the syntactic sugar that calls Get or Set 
 * functions for you for properties that are not accessible. Consider the following example:
 * 
 * <pre>
 * class Foo extends Object
 * {
 *   // A property not visible outside of this class
 *   private $Property;
 *   // An accessor method for the private property
 *   function GetProperty()
 *   {
 *     Alert('GetProperty has been called!');
 *     return $this->Property;
 *   }
 *   // A mutator method for the private property
 *   function SetProperty($value)
 *   {
 *     Alert('SetProperty has been called!');
 *     $this->Property = $value;
 *   }
 * }
 * 
 * // Instantiate a new Foo object
 * $foo = new Foo();
 * // Automatically calls the SetProperty method, triggering an Alert and setting the private variable to 'Hello'
 * $foo->Property = 'Hello';
 * // Automatically calls the GetProperty method, triggering an Alert and setting the local variable to 'Hello'
 * $property = $foo->Property;
 * </pre>
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
	* @ignore
	*/
	function __get($nm)
	{
		$func = 'Get' . $nm;
		if(method_exists($this, $func))
			return $this->$func();
		else
		{
			$func = 'get' . $nm;
			if(method_exists($this, $func))
				return $this->$func();
			else
				BloodyMurder('Could not get property ' . $nm . ' because it does not exist or is write-only in the class ' . get_class($this) . '.');
		}
	}
	/**
	* @ignore
	*/
	function __set($nm, $val)
	{
		$func = 'Set' . $nm;
		if(method_exists($this, $func))
		{
			$this->$func($val);
			return $val;
		}
		else 
		{
			$func = 'set' . $nm;
			if(method_exists($this, $func))
			{
				$this->$func($val);
				return $val;
			}
			else
				BloodyMurder('Could not set property ' . $nm . ' because it does not exist or is read-only in the class ' . get_class($this) . '.');
		}
	}
	/**
	 * @ignore
	 */
	function __call($nm, $args)
	{
		if(strpos($nm, 'Get') === 0)
		{
			$prop = substr($nm, 3);
			if(property_exists($this, $prop))
				return $this->$prop;
			else 
				BloodyMurder('The function ' . $nm . ' could not be called because it does not exist or is not in scope, nor does the property ' . $prop . ' exist in the class ' . get_class($this) . '.');
		}
		elseif(strpos($nm, 'Set') === 0)
		{
			$prop = substr($nm, 3);
			if(property_exists($this, $prop))
				return $this->$prop = $args[0];
			else 
				BloodyMurder('The function ' . $nm . ' could not be called because it does not exist or is not in scope, nor does the property ' . $prop . ' exist in the class ' . get_class($this) . '.');
		}
		else 
			BloodyMurder('The function ' . $nm . ' could not be called because it does not exist or is not in scope of the class ' . get_class($this) . '.');
	}
}

?>