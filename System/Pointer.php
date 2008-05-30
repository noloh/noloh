<?php
/**
 * @ignore
 */
class Pointer
{
	public $RefId;
	
	function Pointer(Component $comp)
	{
		$this->RefId = $comp->Id;
	}
	
	function Dereference()
	{
		return GetComponentById($this->RefId);
	}
	
	function __toString()
	{
		return $this->RefId;
	}
	
	/*
	function __sleep()
	{
		$_SESSION["NOLOH".$this->refId] = GetComponentById($this->refId);
	}
	
	function __get($nm)
	{
		eval('return $_SESSION["NOLOH'.$this->refId.'"]->'.$nm.';');
	}
	
	function __set($nm, $val)
	{
		eval('$_SESSION["NOLOH'.$this->refId.'"]->'.$nm.'=$val;');
	}
	
	function __call($meth, $arg)
	{
		eval('return $_SESSION["NOLOH'.$this->refId.'"]->'.$meth.'('.implode(",",$arg).');');
	}
	*/
}

?>