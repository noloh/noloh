<?php
/**
* InnerSugar class
* 
* 
* 
* @package System
*/
class InnerSugar extends Base
{
	const Get = 'Get';
	const Set = 'Set';
	const Call = 'Call';
	/**
	 * 
	 * @var InnerSugar::Get|InnerSugar::Set|InnerSugar::Call
	 */
	static $Invocation;
	/**
	 * 
	 * @var array
	 */
	static $Chain;
	/**
	 * 
	 * @var mixed
	 */
	static $Tail;
	
	private $Obj;
	private $CurrentChain;
	private $VarPointer;
	/**
	 * @ignore
	 */
	function InnerSugar($obj, $chain, $tail, $var)
	{
		$this->Obj = $obj;
		$this->CurrentChain = $chain;
		$this->CurrentChain[] = $tail;
		$this->VarPointer = $var;
	}
	/**
	 * @ignore
	 */
	static function Invoke($invocation, $object, $chain, $tail, $handle, $args)
	{
		$oldInvocation = self::$Invocation;
		$oldChain = self::$Chain;
		$oldTail = self::$Tail;
		
		self::$Invocation = $invocation;
		self::$Chain = $chain;
		self::$Tail = $tail;
		
		if(method_exists($object, $handle))
		{
			try
			{
				$val = call_user_func_array(array(&$object, $handle), $args);
			}
			catch(SugarException $e)
			{
				self::InvokeError($invocation, $object, $chain, $tail);
			}
		}
		else
		{
			$method = $invocation === 'Call' ? ($handle . $tail) : ($invocation . $handle . $tail);
			if(method_exists($object, $method))
				$val = call_user_func_array(array(&$object, $method), $args);
			else
				self::InvokeError($invocation, $object, $chain, $tail);
		}
		
		
		self::$Invocation = $oldInvocation;
		self::$Chain = $oldChain;
		self::$Tail = $oldTail;
		
		return $val;
	}
	/**
	 * @ignore
	 */
	function InvokeError($invocation, $object, $chain, $tail)
	{
		$nm = implode('->', $chain) . '->' . $tail;
		$class = get_class($object);
		if($invocation == 'Get')
			BloodyMurder('Could not get property ' . $nm . ' because it does not exist or is write-only in the class ' . $class . '.');
		elseif($invocation == 'Set')
			BloodyMurder('Could not set property ' . $nm . ' because it does not exist or is read-only in the class ' . $class . '.');
		elseif($invocation === 'Call')
			BloodyMurder('The function ' . $nm . ' could not be called because it does not exist in, or is not in the scope of, the class ' . $class . '.');
	}
	/**
	 * @ignore
	 */
	function Magic($invocation, $tail, $args = array())
	{
		if(is_string($this->VarPointer))
			return self::Invoke($invocation, $this->Obj, $this->CurrentChain, $tail, $this->VarPointer, $args);
		elseif(is_array($this->VarPointer))
			if(isset($this->VarPointer[$tail]))
				return new InnerSugar($this->Obj, $this->CurrentChain, $tail, $this->VarPointer[$tail]);
			elseif(isset($this->VarPointer[0]))
				return self::Invoke($invocation, $this->Obj, $this->CurrentChain, $tail, $this->VarPointer[0], $args);
	}
	/**
	 * @ignore
	 */
	function __get($nm)					{return $this->Magic('Get', $nm);}
	/**
	 * @ignore
	 */
	function __set($nm, $val)			{return $this->Magic('Set', $nm, array($val));}
	/**
	 * @ignore
	 */
	function __call($nm, $args)			{return $this->Magic('Call', $nm, $args);}
}
?>