<?php
/**
 * SugarException class
 * 
 * SugarException is an Exception that can be used to indicate an error in NOLOH's system of syntactic sugars. For example,
 * a SugarException can be thrown from a function handling the InnerSugar invocation to indicate that a property or method
 * is not available, as follows:
 * 
 * class Example extends Object
 * {
 * 	static $_InBundle = 'Handler';
 * 	function Handler()
 * 	{
 * 		switch(InnerSugar::$Tail)
 * 		{
 * 			case 'Allowed': 
 * 				// ...
 * 				break;
 * 			case 'ThisToo': 
 * 				// ...
 * 				break;
 * 			default:
 * 				throw new SugarException();
 * 		}
 * 	}
 * 	function Example()
 * 	{
 * 		$this->Bundle->Allowed = 200;	// Fine
 * 		$this->Bundle->ThisToo();		// Fine
 * 		$this->Bundle->NotACase();		// Gives a detailed error message indicating that this is impossible.
 * 	}
 * }
 * 
 * For a more detailed discussion about the rich subject of NOLOH's syntactic sugars, please explore http://dev.noloh.com/#/articles/
 * 
 * @package 
 */
class SugarException extends Exception {}
?>