<?php
/**
 * ArrayList class file
 */

/**
 * @package Collections
 * 
 * ArrayList class
 * 
 * An ArrayList is an array with additional functionality. 
 * Namely, you may use the standard square bracket notation on it to read\write to indices, or traverse through it with a foreach loop.
 * In addition, it is equipped with functions normally expected from arrays, such as IndexOf.
 * 
 * <code>
 * // Instantiate a new ArrayList
 * $arrayList = new ArrayList();
 * // Adds the string "Hello" into the ArrayList.
 * $arrayList->Add("Hello");
 * // Adds the number 42 into the ArrayList. These two ways of adding are identical.
 * $arrayList[] = 42;
 * // Iterates the ArrayList as a standard foreach loop and alerts the contents
 * foreach($arrayList as $value)
 * 	Alert($value);
 * // First "Hello" will be alerted, followed 42.
 * </code>
 * 
 * 
 * Moreover, an ArrayList is capable of setting the Parent of an added Component, thus allowing it to display correctly. 
 * This is useful for more advanced functionality. {@see Component::GetParent()} 
 * 
 * <code>
 * class Sample extends Control
 * {
 * 		public $SubControls;
 * 
 * 		function Sample()
 * 		{
 * 			// Instantiate a new ArrayList
 * 			$this->SubControls = new ArrayList();
 * 			// Set its ParentId equal to the Id of $this
 * 			$this->SubControls->ParentId = $this->Id;
 * 			// Instantiate a new Label
 * 			$tempLabel = new Label();
 * 			// Add that label to the SubControls ArrayList
 * 			$this->SubControls->Add($tempLabel);
 * 			// Get the Parent of the Label
 * 			$labelsParent = $tempLabel->Parent;
 * 			// $labelsParent is identical with $this
 * 		}
 * }
 * </code>
 * 
 * @property-read integer $Count The length of the ArrayList
 * 
 */
class ArrayList extends Object implements ArrayAccess, Countable, Iterator
{
	/**
	 * The underlying array of the ArrayList.
	 * @var array
	 */ 
	public $Item;
	/**
	 * The Id of the Component that will represent the Parent of the items to be added.
	 * @var string
	 */ 
	public $ParentId;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends ArrayList.
	 * @param array An array representing the initial items. Note that the Parents of these items will not be changed.
	 */ 
	function ArrayList($elements=array())
	{
		$this->Item = $elements;
	}
	/**
	 * @ignore
	 */
	protected function PreAdd($element)
	{
		/*if($element instanceof Control && $element->GetZIndex() == null)
			$element->_NSetZIndex(++$_SESSION['HighestZIndex']);*/
		if($this->ParentId != null && $element instanceof Component)
			$element->SetParentId($this->ParentId);
	}
	/**
	 * Adds an element to the ArrayList.
	 * @param mixed $element The element to be added 
	 * @param bool $setsByReference Indicates whether the ArrayList sets by reference as opossed to by value
	 * @return mixed The element that has been added
	 */
	function Add($element, $setsByReference = true)
	{
		$this->PreAdd($element);
		if($setsByReference)
			$this->Item[] = &$element;
		else 
			$this->Item[] = $element;
		return $element;
	}
	/**
	 * Adds an unlimited number elements to the ArrayList.
	 * @param mixed ... Unlimited number of elements to be added
	 * <code>$arrayList->AddRange($firstElement, $secondElement, $thirdElement, $fourthElement);</code>
	 */
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
	/**
	 * Inserts an element into a particular index of the ArrayList, not overwriting what was previously there.
	 * If the index is an integer, the ArrayList is reindexed to fill in the gap.
	 * If the index is a string and there already is an element at the specified index, that element's index will be appended
	 * with a ' (indicating prime), to make room for the element being inserted.
	 * @param mixed $element The element to be inserted
	 * @param integer|string $index The index into which your element will be added
	 * @return mixed The element that has been added
	 * <code>
	 * // Inserts a new Button into the zeroth index
	 * $this->Controls->Insert(new Button('Click'), 0);
	 * // The Controls ArrayList will now begin with the said button, followed by whatever else was there previously
	 * </code><br>
	 * <code>
	 * // Instantiates a new ArrayList
	 * $arr = new ArrayList();
	 * // Inserts the string "a" into a string position in the ArrayList
	 * $arr["ind"] = "a";
	 * // Inserts the string "b" into the same string position in the ArrayList
	 * $arr->Insert("b", "ind");
	 * // Inserts the string "c" into the same string position in the ArrayList
	 * $arr->Insert("c", "ind");
	 * // At this point of the program:
	 * // "c" will be in the position "ind"
	 * // "b" will be in the position "ind'" 
	 * // "a" will be in the position "ind''"
	 * </code>
	 */
	function Insert($element, $index)
	{
		$oldItems = $this->Item;
		if($this->ParentId != null && $element instanceof Component && isset($oldItems[$index]) && $oldItems[$index] instanceof Component)
			$_SESSION['NOLOHControlInserts'][$element->Id] = $oldItems[$index]->Id;
		if(is_numeric($index))
		{
			$this->Item = array_slice($oldItems, 0, $index);
			$this->Add($element, true, true);
			$this->Item = array_merge($this->Item, array_slice($oldItems, $index));
		}
		elseif(is_string($index))
		{
			$this->PreAdd($element);
			$this->InsertIntoStringHelper($element, $index);
		}
		return $element;
	}
	/**
	 * @ignore
	 */
	private function InsertIntoStringHelper($element, $index)
	{
		if(isset($this->Item[$index]))
			$this->InsertIntoStringHelper($this->Item[$index], $index . '\'');
		$this->Item[$index] = &$element;
	}

    function PositionalInsert($element, $index, $position)
    {
		$oldItems = $this->Item;
   		if($this->ParentId != null && $element instanceof Component && isset($oldItems[$position]) && $oldItems[$position] instanceof Component)
			$_SESSION['NOLOHControlInserts'][$element->Id] = $oldItems[$position]->Id;
    	$this->Item = array_slice($oldItems, 0, $position);
        $this->Insert($element, $index, true);
    	$this->Item = array_merge($this->Item, array_slice($oldItems, $position));
        return $element;
    }
	/**
	 * Removes an element at a particular index. 
	 * If the index is an integer, the ArrayList is reindexed to fill in the gap.
	 * @param integer|string $index The index of the element to be removed
	 */
	function RemoveAt($index)
	{
		if(isset($this->Item[$index]) && $this->Item[$index] instanceof Component && $this->Item[$index]->ParentId == $this->ParentId)
			$this->Item[$index]->SetParentId(null);
		if(is_numeric($index))
			array_splice($this->Item, $index, 1);
		else 
			unset($this->Item[$index]);
	}
	/**
	 * Removes the first occurrence of a particular element from the ArrayList.
	 * @param mixed $element The element to be removed
	 * @return bool Whether the remove was successful
	 */
	function Remove($element)
	{
		$idx = $this->IndexOf($element);
		if($idx != -1)
		{
			if(func_num_args()==1 || !func_get_arg(1))
				$this->RemoveAt($idx);
			else
				$this->RemoveAt($idx, true);
			return true;
		}
		return false;
	}
	/**
	 * Finds the index of a particular element. Numeric indices start from 0 on.
	 * @param mixed $element The element to be searched for
	 * @return integer|string If found, the index of the element. Otherwise, -1.
	 */
	function IndexOf($element)
	{
		$idx = array_search($element, $this->Item, true);
		return $idx===false ? -1 : $idx;
	}
	/**
	 * Clears the ArrayList.
	 */
	function Clear()
	{
		foreach($this->Item as $val)
			if($val instanceof Component && $val->GetParentId()==$this->ParentId)
				$val->SetParentId(null);
		$this->Item = array();
	}
	/**
	 * The length of the ArrayList.
	 * This may also be accessed as a property, as in,
	 * <code>if($this->Controls->Count==0)</code>
	 * @return integer
	 */
	function Count()
	{
		return count($this->Item);
	}
	/**
	 * @ignore
	 */
	function GetCount()
	{
		return count($this->Item);
	}
	/**
	 * @ignore
	 */
	public function rewind() 
	{
		reset($this->Item);
	}
	/**
	 * @ignore
	 */
	public function current() 
	{
		return current($this->Item);
	}
	/**
	 * @ignore
	 */
	public function key() 
	{
		return key($this->Item);
	}
	/**
	 * @ignore
	 */
	public function next() 
	{
		return next($this->Item);
	}
	/**
	 * @ignore
	 */
	public function valid() 
	{
		return $this->current() !== false;
	}
	/**
	 * @ignore
	 */
	function offsetExists($key)
	{
		return(array_key_exists($key, $this->Item));
	}
	/**
	 * @ignore
	 */
	function offsetGet($index)
	{
		return $this->Item[$index];
	}
	/**
	 * @ignore
	 */
	function offsetSet($index, $val)
	{		
		if($index === null)
			$this->Add($val, true);
		else
		{
			$this->PreAdd($val);
			$this->RemoveAt($index);
			$this->Item[$index] = &$val;
		}
	}
	/**
	 * @ignore
	 */
	function offsetUnset($index)
	{
		$this->RemoveAt($index);
	}
}

?>