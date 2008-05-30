<?php
/**
 * @package System
 */
/**
 * System class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 */
abstract class Multiple extends Component implements ArrayAccess, Countable, Iterator
{
	private $SubObjects;
	private $CastClass;
	
	function Multiple($extendsClassesAsDotDotDot)
	{
		parent::Component();
		$args = func_get_args();
		$numArgs = count($args);
		if($numArgs == 0)
			BloodyMurder('Multiple inheritence object must extend at least one class.');
		$this->SubObjects = array();
		if(is_string($args[0]))
			$this->SubObjects[$this->CastClass = $args[0]] = '';
		elseif(is_array($args[0]))
		{
			$this->SubObjects[$this->CastClass = $args[0][0]] = '';
			array_shift($args[0]);
			$this->ConstructViaArray($this->CastClass, $args[0]);
		}
		for($i=1; $i<$numArgs; ++$i)
			if(is_string($args[$i]))
				$this->SubObjects[$args[$i]] = '';
			elseif(is_array($args[$i]))
			{
				$this->SubObjects[$tmpClassName = $args[$i][0]] = '';
				array_shift($args[1]);
				$this->ConstructViaArray($tmpClassName, $args[$i]);
			}
	}
	
	function Construct($className, $paramsAsDotDotDot=null)
	{
		if(isset($this->SubObjects[$className]))
			if(isset($GLOBALS['_NMultipleConstruct' . $className]))
				BloodyMurder('Circular extensions detected in Multiple object, particularly extending the ' . $className . ' class');
			else
			{
				$args = func_get_args();
				$loopBound = count($args)-2;
				$paramsString = '';
				for($i=1; $i<$loopBound; ++$i)
					$paramsString .= '$args['.$i.'],';
				$paramsString .= '$args['.$i.']';
				$GLOBALS['_NMultipleConstruct' . $className] = true;
				eval('$this->SubObjects[$className] = new $className('.$paramsString.');');
				unset($GLOBALS['_NMultipleConstruct' . $className]);
			}
		else 
			BloodyMurder('Multiple inheritence object does not extend ' . $className . '.');
		return $this;
	}
	
	function ConstructViaArray($className, $paramsArray=array())
	{
		if(isset($this->SubObjects[$className]))
			if(isset($GLOBALS['_NMultipleConstruct' . $className]))
				BloodyMurder('Circular extensions detected in Multiple object, particularly extending the ' . $className . ' class');
			else
			{
				$loopBound = count($paramsArray)-1;
				$paramsString = '';
				for($i=0; $i<$loopBound; ++$i)
					$paramsString .= '$paramsArray['.$i.'],';
				$paramsString .= '$paramsArray['.$i.']';
				$GLOBALS['_NMultipleConstruct' . $className] = true;
				eval('$this->SubObjects[$className] = new $className('.$paramsString.');');
				unset($GLOBALS['_NMultipleConstruct' . $className]);
			}
		else 
			BloodyMurder('Multiple inheritence object does not extend ' . $className . '.');
		return $this;
	}
	
	function Cast($className)
	{
		$this->CastClass = $className;
		return $this;
	}
	
	function __get($name)
	{
		if(property_exists($this->SubObjects[$this->CastClass], $name) ||
			($this->SubObjects[$this->CastClass] instanceof Object && (method_exists($this->SubObjects[$this->CastClass], 'Get'.$name) || method_exists($this->SubObjects[$this->CastClass], 'get'.$name))))
				return $this->SubObjects[$this->CastClass]->$name;
		foreach($this->SubObjects as $object)
			if(property_exists($object, $name) ||
				($object instanceof Object && (method_exists($object, 'Get'.$name) || method_exists($object, 'get'.$name))))
					return $object->$name;
	}
	
	function __set($name, $value)
	{
		if(property_exists($this->SubObjects[$this->CastClass], $name) ||
			($this->SubObjects[$this->CastClass] instanceof Object && (method_exists($this->SubObjects[$this->CastClass], 'Set'.$name) || method_exists($this->SubObjects[$this->CastClass], 'set'.$name))))
				return $this->SubObjects[$this->CastClass]->$name = $value;
		foreach($this->SubObjects as $object)
			if(property_exists($object, $name) ||
				($object instanceof Object && (method_exists($object, 'Set'.$name) || method_exists($object, 'set'.$name))))
					return $object->$name = $value;		
	}
	
	function __call($name, $params)
	{
		if(method_exists($this->SubObjects[$this->CastClass], $name))
			return call_user_func_array(array($this->SubObjects[$this->CastClass], $name), $params);
		foreach($this->SubObjects as $object)
			if(method_exists($object, $name))
				return call_user_func_array(array($object, $name), $params);
	}
	
	// Component functions
	function GetShowStatus()			{return reset($this->SubObjects)->GetShowStatus();}
	function GetParentId()				{return reset($this->SubObjects)->GetParentId();}
	function GetParent($generations=1)	{return reset($this->SubObjects)->GetParent($generations);}
	function SearchEngineShow()			{reset($this->SubObjects)->SearchEngineShow();}
	function SecondGuessShowStatus()
	{
		parent::SecondGuessShowStatus();
		reset($this->SubObjects)->SecondGuessShowStatus();
	}
	function SecondGuessParent()
	{
		parent::SecondGuessParent();
		reset($this->SubObjects)->SecondGuessParent();
	}
	function SetParentId($parentId)
	{
		parent::SetParentId($parentId);
		reset($this->SubObjects)->SetParentId($parentId);
	}
	function Show()
	{
		parent::Show();
		reset($this->SubObjects)->Show();
	}
	function Bury()
	{
		parent::Bury();
		reset($this->SubObjects)->Bury();
	}
	function Resurrect()
	{
		parent::Resurrect();
		reset($this->SubObjects)->Resurrect();
	}
	
	// Interface functions
	/**
	 * @ignore
	 */
	function Count()
	{
		if($this->SubObjects[$this->CastClass] instanceof Countable)
			return $this->SubObjects[$this->CastClass]->Count();
		else 
			foreach($this->SubObjects as $object)
				if($object instanceof Countable)
					return $object->Count();
	}
	/**
	 * @ignore
	 */
	public function rewind() 
	{
		if($this->SubObjects[$this->CastClass] instanceof Iterator)
			return $this->SubObjects[$this->CastClass]->rewind();
		else 
			foreach($this->SubObjects as $object)
				if($object instanceof Iterator)
					return $object->rewind();
	}
	/**
	 * @ignore
	 */
	public function current() 
	{
		if($this->SubObjects[$this->CastClass] instanceof Iterator)
			return $this->SubObjects[$this->CastClass]->current();
		else 
			foreach($this->SubObjects as $object)
				if($object instanceof Iterator)
					return $object->current();
	}
	/**
	 * @ignore
	 */
	public function key() 
	{
		if($this->SubObjects[$this->CastClass] instanceof Iterator)
			return $this->SubObjects[$this->CastClass]->key();
		else 
			foreach($this->SubObjects as $object)
				if($object instanceof Iterator)
					return $object->key();
	}
	/**
	 * @ignore
	 */
	public function next() 
	{
		if($this->SubObjects[$this->CastClass] instanceof Iterator)
			return $this->SubObjects[$this->CastClass]->next();
		else 
			foreach($this->SubObjects as $object)
				if($object instanceof Iterator)
					return $object->next();
	}
	/**
	 * @ignore
	 */
	public function valid() 
	{
		if($this->SubObjects[$this->CastClass] instanceof Iterator)
			return $this->SubObjects[$this->CastClass]->valid();
		else 
			foreach($this->SubObjects as $object)
				if($object instanceof Iterator)
					return $object->valid();
	}
	/**
	 * @ignore
	 */
	function offsetExists($key)
	{
		if($this->SubObjects[$this->CastClass] instanceof ArrayAccess)
			return $this->SubObjects[$this->CastClass]->offsetExists($key);
		else 
			foreach($this->SubObjects as $object)
				if($object instanceof ArrayAccess)
					return $object->offsetExists($key);
	}
	/**
	 * @ignore
	 */
	function offsetGet($index)
	{
		if($this->SubObjects[$this->CastClass] instanceof ArrayAccess)
			return $this->SubObjects[$this->CastClass]->offsetGet($index);
		else 
			foreach($this->SubObjects as $object)
				if($object instanceof ArrayAccess)
					return $object->offsetGet($index);
	}
	/**
	 * @ignore
	 */
	function offsetSet($index, $val)
	{		
		if($this->SubObjects[$this->CastClass] instanceof ArrayAccess)
			return $this->SubObjects[$this->CastClass]->offsetSet($index, $val);
		else 
			foreach($this->SubObjects as $object)
				if($object instanceof ArrayAccess)
					return $object->offsetSet($index, $val);
	}
	/**
	 * @ignore
	 */
	function offsetUnset($index)
	{
		if($this->SubObjects[$this->CastClass] instanceof ArrayAccess)
			return $this->SubObjects[$this->CastClass]->offsetUnset($index);
		else 
			foreach($this->SubObjects as $object)
				if($object instanceof ArrayAccess)
					return $object->offsetUnset($index);
	}
	
}

?>