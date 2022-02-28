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
 * 		$this->Items->Add($item, true);
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
	function __construct($obj=null, $addFunctionName='', $removeAtFunctionName='', $clearFunctionName='')
	{
		parent::__construct();
		$this->Source = $obj instanceof Component ? $obj->Id : $obj;
		$this->AddFunctionName = $addFunctionName;
		$this->RemoveAtFunctionName = $removeAtFunctionName;
		$this->ClearFunctionName = $clearFunctionName;
	}
	/**
	 * Adds an element to the ArrayList.
	 * @param mixed $element The element to be added
	 * @param boolean $onlyAdd Specifies whether or not you want a default ArrayList Add, or the overidden AddFunction to be called
	 * @return mixed The element that has been added
	 */
	function Add($element, $onlyAdd = false)
	{
		if($onlyAdd)
			if(isset($GLOBALS['_NImplArrInsert']))
				return parent::Insert($element, $GLOBALS['_NImplArrInsert']);
			else 
				return parent::Add($element);
		elseif(!$this->AddFunctionName)
			return parent::Add($element);
		elseif(is_object($src = $this->Source) || ($src = GetComponentById($this->Source==null?$this->ParentId:$this->Source)))
			return $src->{$this->AddFunctionName}($element);
		elseif(class_exists($this->Source))
			return call_user_func(array($this->Source, $this->AddFunctionName), $element);
	}
	/**
	 * Inserts an element into a particular index of the ArrayList, not overwriting what was previously there.
	 * If the index is an integer, the ArrayList is reindexed to fill in the gap.
	 * @param mixed $element The element to be inserted
	 * @param integer|string $index The index into which your element will be added
	 * @param boolean $onlyInsert Specifies whether or not you want a default ArrayList Insert, or the overidden InsertFunction to be called
	 * @return mixed The element that has been added
	 */
	function Insert($element, $index, $onlyInsert = false)
	{
		if($onlyInsert)
			return parent::Insert($element, $index);
		elseif(!$this->InsertFunctionName)
		{
			if($this->AddFunctionName)
			{
				$GLOBALS['_NImplArrInsert'] = $index;
				$this->Add($element);
				unset($GLOBALS['_NImplArrInsert']);
				return $element;
			}
			else
				return parent::Insert($element, $index);
		}
		elseif(is_object($src = $this->Source) || ($src = GetComponentById($this->Source==null?$this->ParentId:$this->Source)))
			return $src->{$this->InsertFunctionName}($element, $index);
		elseif(class_exists($this->Source))
			call_user_func(array($this->Source, $this->InsertFunctionName), $element, $index);
	}
	/**
	 * Inserts an element into a particular index of the ArrayList, as well as a particular position, in the sense of the order in which foreach iterates
	 * @param mixed $element The element to be inserted
	 * @param mixed $index The index into which your element will be inserted
	 * @param integer $position The position into which your element will be inserted
	 * @param boolean $onlyInsert Specifies whether or not you want a default ArrayList Insert, or the overidden InsertFunction to be called
	 * @return mixed The Element that has been inserted
	 */
    function PositionalInsert($element, $index, $position, $onlyInsert = false)
    {
		if(!$this->InsertFunctionName || $onlyInsert)
			return parent::PositionalInsert($element, $index, $position);
		elseif(is_object($this->Source))
			return $this->Source->{$this->InsertFunctionName}($element, $position);
		else
			return GetComponentById($this->Source==null?$this->ParentId:$this->Source)->{$this->InsertFunctionName}($element, $position);
    }
	/**
	 * Removes a particular element of the ArrayList.
	 * @param mixed $element The element to be removed
	 * @param boolean $onlyRemove Specifies whether or not you want a default ArrayList Remove, or the overidden RemoveFunction to be called
	 * @return boolean Whether the remove was successful
	 */
	function Remove($element, $onlyRemove = false)
	{
		if(!$this->RemoveFunctionName || $onlyRemove)
			return parent::Remove($element, $onlyRemove);
		elseif(is_object($src = $this->Source) || ($src = GetComponentById($this->Source==null?$this->ParentId:$this->Source)))
			return $src->{$this->RemoveFunctionName}($element);
		elseif(class_exists($this->Source))
			call_user_func(array($this->Source, $this->RemoveFunctionName), $element);
	}
	/**
	 * Removes an element at a particular index. 
	 * If the index is an integer, the ArrayList is reindexed to fill in the gap.
	 * @param integer|string $index The index of the element to be removed
	 * @param boolean $onlyRemove Specifies whether or not you want a default ArrayList RemoveAt, or the overidden RemoveAtFunction to be called
	 */
	function RemoveAt($index, $onlyRemove = false)
	{
		if(!$this->RemoveAtFunctionName || $onlyRemove)
			return parent::RemoveAt($index);
		elseif(is_object($src = $this->Source) || ($src = GetComponentById($this->Source==null?$this->ParentId:$this->Source)))
			return $src->{$this->RemoveAtFunctionName}($index);
		elseif(class_exists($this->Source))
			call_user_func(array($this->Source, $this->RemoveAtFunctionName), $index);
	}
	/**
	 * Clears the ArrayList.
	 * @param boolean $onlyClear Specifies whether or not you want a default ArrayList Clear, or the overidden ClearFunction to be called
	 */
	function Clear($onlyClear = false)
	{
		if(!$this->ClearFunctionName || $onlyClear)
			return parent::Clear();
		elseif(is_object($src = $this->Source) || ($src = GetComponentById($this->Source==null?$this->ParentId:$this->Source)))
			return $src->{$this->ClearFunctionName}();
		elseif(class_exists($this->Source))
			call_user_func(array($this->Source, $this->ClearFunctionName));
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