<?php

class ArrayList implements ArrayAccess, Countable, Iterator
{
	public $Item;
	public $ParentId;
	public $SpecialFunction;
	public $SpecialObjectId;

	function ArrayList($items=array())
	{
		$this->Item = $items;
	}

	protected function PreActualAdd($object)
	{
		if($object instanceof Control && $object->GetZIndex() == null)
			$object->SetZIndex(GetGlobal("HighestZIndex") + 1);
		if($this->ParentId != null && $object instanceof Component)
		{
			//Temporary fix for setting child components PositionType of parent with no height to relative - Asher
//			if($object instanceof Control && ($tmpParent = GetComponentById($this->ParentId)) instanceof Control && $tmpParent->Height === null)
//				$object->PositionType = 2;
				//Alert("wtf man");*/
			$object->SetParentId($this->ParentId);
		}
	}
	
	protected function ActualAdd($object, $passByReference)
	{
		//if(is_object($whatObject))
			if($passByReference)
				$this->Item[] = &$object;
			else 
				$this->Item[] = $object;
		/*else 
			if($passByReference)
				$this->Item[] = &$whatObject;
			else 
				$this->Item[] = $whatObject;
		*/
	}

	function Add($object, $passByReference = true)
	{
		$this->PreActualAdd($object);
		$this->ActualAdd($object, $passByReference);
		return $object;
	}

	function Insert($object, $index)
	{
		$oldItems = $this->Item;
		$this->Item = array_slice($this->Item, 0, $index);
		$this->Add($object, true, true);
		$this->Item = array_merge($this->Item, array_slice($oldItems, $index));
	}

	function AddRange($dotDotDot)
	{
		$numArgs = func_num_args();
		$Args = func_get_args();
		for($i = 0; $i < $numArgs; $i++)
			if($Args[$i] instanceof Component)
				$this->Add(GetComponentById($Args[$i]->Id));
			else 
				$this->Add($Args[$i]);
	}

	function RemoveAt($index)
	{
		if(isset($this->Item[$index]) && $this->Item[$index] instanceof Component && $this->Item[$index]->ParentId == $this->ParentId)
			$this->Item[$index]->SetParentId(null);
		array_splice($this->Item, $index, 1);
	}

	function RemoveItem($item)
	{
		$idx = $this->IndexOf($item);
		if($idx != -1)
		{
			$this->RemoveAt($idx);
			return true;
		}
		return false;
	}

	function IndexOf($what)
	{
		$idx = array_search($what, $this->Item, true);
		return $idx===false ? -1 : $idx;
		/*
		foreach($this->Item as $idx => $val)
			if($val === $what)
				return $idx;
		return -1;
		*/
	}

	function Clear()
	{
		foreach($this->Item as $val)
			if($val instanceof Component && $val->GetParentId()==$this->ParentId)
				$val->SetParentId(null);
		$this->Item = array();
	}
	
	function __get($nm)
	{
		if($nm == "Count")
			return count($this->Item);
		return null;
	}
	
	function Count()
	{
		return count($this->Item);
	}
	
	public function rewind() 
	{
		reset($this->Item);
	}
	
	public function current() 
	{
		return current($this->Item);
	}
	
	public function key() 
	{
		return key($this->Item);
	}
	
	public function next() 
	{
		return next($this->Item);
	}
	
	public function valid() 
	{
		return $this->current() !== false;
	}
	
	function offsetExists($key)
	{
		return(array_key_exists($key, $this->Item));
	}

	function offsetGet($index)
	{/*
		if(get_class($this->Item[$index])=="Pointer")
			return GetComponentById($this->Item[$index]);
		else*/
		
		//if(isset($this->Item[$index]))
			return $this->Item[$index];
		//else 
		//	return null;
	}

	function offsetSet($index, $val)
	{
		/*if(is_object($val) && is_subclass_of($val, "Component"))
			$this->Item[$index] = new Pointer($val);
		else */
		$this->PreActualAdd($val);
		
		if($index === null)
			$this->ActualAdd($val, true);
		else
		{
			$this->RemoveAt($index);
			$this->Item[$index] = &$val;
		}
	}

	function offsetUnset($index)
	{
		$this->RemoveAt($index);
	}

	/*
	function __sleep()
	{
		foreach($this->Item as $i => $val)
			if(is_object($val) && $val instanceof Component)
				$this->Item[$i] = &new Pointer($val);
		return array_keys((array)$this);
	}
	
	function RestoreValues()
	{
		//$ItemCount = count($this->Item);
		//for($i=0; $i<$ItemCount; $i++)
		foreach($this->Item as $i => $val)
			if(is_object($val) && $val instanceof Pointer)
				$this->Item[$i] = &$val->Dereference();
	}

	function ReferencedComponents()
	{
		$ret = array();
		$ItemCount = count($this->Item);
		for($i=0; $i<$ItemCount; $i++)
		{
			if(is_object($this->Item[$i]) && is_subclass_of($this->Item[$i], "Component"))
			{
				$ret[$this->Item[$i]->Id] = $this->Item[$i];
				$ret += $this->Item[$i]->ReferencedComponents();
			}
			/*elseif(is_array($this->Item[$i]))
				$ret += ArrayReferencedComponents($this->Item[$i]);*
		}
		return $ret;
	}
	
	function __wakeup()
	{
		$ItemCount = count($this->Item);
		for($i=0; $i<$ItemCount; $i++)
			if(is_object($this->Item[$i]) && get_class($this->Item[$i]) == "Pointer")
			//if(IsPointer($this->Item[$i]))
			{
				//$tmp = $this->Item[$i];
				//unset($this->Item[$i]);
				$this->Item[$i] = $this->Item[$i]->Dereference();
				//$this->Item[$i] = DereferencePointer($tmp);
				//unset($tmp);
			}
	}
	*/
}

?>