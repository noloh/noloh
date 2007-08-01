<?php
/**
 * @package Collections
 */
class ImplicitArrayList extends ArrayList 
{
	private $Source;
	public $AddFunctionName;
	public $InsertFunctionName;
	public $RemoveAtFunctionName;
	public $RemoveFunctionName;
	public $ClearFunctionName;
	
	function ImplicitArrayList($obj=null, $addFunctionName="", $removeAtFunctionName="", $clearFunctionName="")
	{
		parent::ArrayList();
		$this->Source = $obj instanceof Component ? $obj->Id : $obj;
		$this->AddFunctionName = $addFunctionName;
		$this->RemoveAtFunctionName = $removeAtFunctionName;
		$this->ClearFunctionName = $clearFunctionName;
	}
	
	function Add($object, $passByReference = true, $onlyAdd = false)
	{
		if($this->AddFunctionName=="" || $onlyAdd)
			return parent::Add($object, $passByReference);
		elseif(is_object($this->Source))
			return $this->Source->{$this->AddFunctionName}($object);
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->AddFunctionName}($object);
	}
	
	function Insert($object, $index, $onlyInsert = false)
	{
		if($this->InsertFunctionName=="" || $onlyInsert)
			return parent::Insert($object, $index);
		elseif(is_object($this->Source))
			return $this->Source->{$this->InsertFunctionName}($object, $index);
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->InsertFunctionName}($object, $index);
	}
	
	function Remove($object, $onlyRemove = false)
	{
		if($this->RemoveFunctionName=="" || $onlyRemove)
			return parent::Remove($object);
		elseif(is_object($this->Source))
			return $this->Source->{$this->RemoveFunctionName}($object);
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->RemoveFunctionName}($object);
	}
	
	function RemoveAt($index, $onlyRemove = false)
	{
		if($this->RemoveAtFunctionName=="" || $onlyRemove)
			return parent::RemoveAt($index);
		elseif(is_object($this->Source))
			return $this->Source->{$this->RemoveAtFunctionName}($index);
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->RemoveAtFunctionName}($index);		
	}
	
	function Clear($onlyClear = false)
	{
		if($this->ClearFunctionName=="" || $onlyClear)
			return parent::Clear();
		elseif(is_object($this->Source))
			return $this->Source->{$this->ClearFunctionName}();
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->ClearFunctionName}();
	}
	
	function offsetSet($index, $val)
	{
		if($index === null)
			$this->Add($val);
		else 
		{
			$this->RemoveAt($index);
			$this->Insert($val, $index);
		}
			//parent::offsetSet($index, $val);
		// Needs an else to replace an index!
	}
	
	function offsetUnset($index)
	{
		$this->Remove($this->Item[$index]);
		//$this->RemveAt($index);
	}
}

?>