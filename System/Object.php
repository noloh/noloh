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
				BloodyMurder('Could not get property ' . $nm . ' because it does not exist or is write-only.');
			/*elseif(strpos($nm, "CSS") == 0 && $nm != "CSSFile")
				$ret = $this->CSSPropertyArray[str_replace("_", "-", str_replace("CSS", "", $nm))];*/
		}
		//else 
			//if(array_key_exists($nm, $this->PublicProperties))
				//return $this->PublicProperties[$nm];
			//else 
			//	return null;
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
				BloodyMurder('The property ' . $nm . ' could not be set because it does not exist or is read-only.');
			/*elseif(strpos($nm, "CSS") === 0 && $nm != "CSSFile")
			{
				if($this->CSSPropertyArray == null)
					$this->CSSPropertyArray = array();
				$this->CSSPropertyArray[str_replace("_", "-", str_replace("CSS", "", $nm))] = $val;
			}*/
			
		}
		
	}
}

?>