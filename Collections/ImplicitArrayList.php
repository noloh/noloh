<?php
/**
 * ImplicitArrayList class
 *
 * An ImplicitArrayList is an ArrayList that allows the developer to define functions to be called when elements are added, 
 * inserted, removed, or cleared. Thus, they behave not unlike a {@link ServerEvent} for ArrayList functionality. If you have 
 * defined an Add function and attempt to add an element to the ImplicitArrayList, the function that you defined will be called 
 * instead. If you would like an actual add to happen, then you must pass in an additional true as a last parameter. 
 * 
 * <pre>
 * class Example
 * {
 * 	// Variable which will hold our ImplicitArrayList
 * 	public $Items;
 * 
 * 	function Example()
 * 	{
 * 		// Instantiates a new ImplicitArrayList which will call the functions of $this object, and whose AddFunction is AddItem
 * 		$this->Items = new ImplicitArrayList($this, "AddItem");
 * 		// Attempts to add an Item to the ImplicitArrayList, but instead will redirect to the AddItem function
 * 		$this->Items->Add(new Item("MyValue", "MyText"));
 * 	}
 * 	function AddItem($item)
 * 	{
 * 		Alert("An Item with a value of " . $item->Value . " has been added!");
 * 		// Will perform the actual add on the ArrayList, ignoring the fact that it's Implicit
 * 		$this->Items->Add($item, true, true);
 * 	}
 * }
 * </pre>
 * 
 * @package Collections
 */
class ImplicitArrayList extends ArrayList 
{
	private $Source;
	/**
	 * The name of the function that will be called on an Add
	 * @var string
	 */
	public $AddFunctionName;
	/**
	 * The name of the function that will be called on an Insert
	 * @var string
	 */
	public $InsertFunctionName;
	/**
	 * The name of the function that will be called on a RemoveAt
	 * @var string
	 */
	public $RemoveAtFunctionName;
	/**
	 * The name of the function that will be called on a Remove
	 * @var string
	 */
	public $RemoveFunctionName;
	/**
	 * The name of the function that will be called on a Clear
	 * @var string
	 */
	public $ClearFunctionName;
	/**
	 * Constructor
	 * Be sure to call this from the constructor of any class that extends ImplicitArrayList.
	 * @param object $obj The object whose functions will be called
	 * @param string $addFunctionName
	 * @param string $removeAtFunctionName
	 * @param string $clearFunctionName
	 * @return ImplicitArrayList
	 */
	function ImplicitArrayList($obj=null, $addFunctionName='', $removeAtFunctionName='', $clearFunctionName='')
	{
		parent::ArrayList();
		$this->Source = $obj instanceof Component ? $obj->Id : $obj;
		$this->AddFunctionName = $addFunctionName;
		$this->RemoveAtFunctionName = $removeAtFunctionName;
		$this->ClearFunctionName = $clearFunctionName;
	}
	/**
	 * Adds an element to the ArrayList.
	 * @param mixed $element The element to be added 
	 * @param boolean $setsByReference Indicates whether the ArrayList sets by reference as opossed to by value
	 * @param boolean $onlyAdd Specifies whether or not you want a default ArrayList Add, or the overidden AddFunction to be called
	 * @return mixed The element that has been added
	 */
	function Add($object, $passByReference = true, $onlyAdd = false)
	{
		if($this->AddFunctionName=='' || $onlyAdd)
			return parent::Add($object, $passByReference);
		elseif(is_object($this->Source))
			return $this->Source->{$this->AddFunctionName}($object);
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->AddFunctionName}($object);
	}
	/**
	 * Inserts an element into a particular index of the ArrayList, not overwriting what was previously there.
	 * If the index is an integer, the ArrayList is reindexed to fill in the gap.
	 * @param mixed $element The element to be inserted
	 * @param integer|string $index The index into which your element will be added
	 * @param boolean $onlyInsert Specifies whether or not you want a default ArrayList Insert, or the overidden InsertFunction to be called
	 * @return mixed The element that has been added
	 */
	function Insert($object, $index, $onlyInsert = false)
	{
		if($this->InsertFunctionName=='' || $onlyInsert)
			return parent::Insert($object, $index);
		elseif(is_object($this->Source))
			return $this->Source->{$this->InsertFunctionName}($object, $index);
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->InsertFunctionName}($object, $index);
	}
	/**
	 * Inserts an element into a particular index of the ArrayList, as well as a particular position, in the sense of the order in which foreach iterates
	 * @param mixed $element The element to be inserted
	 * @param mixed $index The index into which your element will be inserted
	 * @param integer $position The position into which your element will be inserted
	 * @param boolean $onlyInsert Specifies whether or not you want a default ArrayList Insert, or the overidden InsertFunction to be called
	 * @return mixed The Element that has been inserted
	 */
    function PositionalInsert($object, $index, $position, $onlyInsert = false)
    {
		if($this->InsertFunctionName=='' || $onlyInsert)
			return parent::PositionalInsert($object, $index, $position);
		elseif(is_object($this->Source))
			return $this->Source->{$this->InsertFunctionName}($object, $position);
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->InsertFunctionName}($object, $position);
    }
	/**
	 * Removes a particular element of the ArrayList.
	 * @param mixed $element The element to be removed
	 * @param boolean $onlyRemove Specifies whether or not you want a default ArrayList Remove, or the overidden RemoveFunction to be called
	 * @return boolean Whether the remove was successful
	 */
	function Remove($object, $onlyRemove = false)
	{
		if($this->RemoveFunctionName=='' || $onlyRemove)
			return parent::Remove($object, $onlyRemove);
		elseif(is_object($this->Source))
			return $this->Source->{$this->RemoveFunctionName}($object);
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->RemoveFunctionName}($object);
	}
	/**
	 * Removes an element at a particular index. 
	 * If the index is an integer, the ArrayList is reindexed to fill in the gap.
	 * @param integer|string $index The index of the element to be removed
	 * @param boolean $onlyRemoveAt Specifies whether or not you want a default ArrayList RemoveAt, or the overidden RemoveAtFunction to be called
	 */
	function RemoveAt($index, $onlyRemove = false)
	{
		if($this->RemoveAtFunctionName=='' || $onlyRemove)
			return parent::RemoveAt($index);
		elseif(is_object($this->Source))
			return $this->Source->{$this->RemoveAtFunctionName}($index);
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->RemoveAtFunctionName}($index);		
	}
	/**
	 * Clears the ArrayList.
	 * @param boolean $onlyClear Specifies whether or not you want a default ArrayList Clear, or the overidden ClearFunction to be called
	 */
	function Clear($onlyClear = false)
	{
		if($this->ClearFunctionName=='' || $onlyClear)
			return parent::Clear();
		elseif(is_object($this->Source))
			return $this->Source->{$this->ClearFunctionName}();
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->ClearFunctionName}();
	}
	/**
	 * @ignore
	 */
	function offsetSet($index, $val)
	{
		if($index === null)
			$this->Add($val);
		else 
		{
			if(isset($this->Elements[$index]))
				$this->RemoveAt($index);
			$this->Insert($val, $index);
		}
			//parent::offsetSet($index, $val);
		// Needs an else to replace an index!
	}
	/**
	 * @ignore
	 */
	function offsetUnset($index)
	{
		$this->Remove($this->Elements[$index]);
		//$this->RemveAt($index);
	}
}

?>