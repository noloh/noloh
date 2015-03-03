<?php
/**
 * Singleton interface
 * 
 * If you have a class of which you know for sure that there is only one, unique instance, and you need a reference
 * to this instance from outside of the class, your class should implement the Singleton interface.
 * After it is instantiated, you can get a reference to it by using the That() static method, a play on the "this" concept.<BR>
 * The traditional wisdom is that Singletons are a design pattern (on which there is plenty of available literature) and not 
 * just something you can implement or extend, but some patterns are so uniform that they really can be abstracted and automated. 
 * However, because of the lack of certain functionality within PHP 5.2 or less, it cannot be fully automated, and we require that 
 * you simply copy and paste one simple line of code, documented below. In NOLOH for PHP 5.3, however, the process is completely 
 * automated and the line is not required (though not harmful for backwards compatibility).
 *
 * Note: Singletons will only work for subclasses of Component. Calling That() before an instance is created will return null, and attempts at creating two instances will result in an error.
 * 
 * @package Interfaces
 */
interface Singleton
{
	/**
	 * Returns the unique instance of the class that implements this interface. In PHP before 5.2 or less,
	 * your That() method should be defined as follows. You can also, however, extend it with your own code:
	 * <pre>
	 * static function That()		{return parent::That(__CLASS__);}
	 * </pre>
	 * @return Component
	 */
	static function That();
}

?>