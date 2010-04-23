<?php
/**
 * Group class
 * 
 * A Group is a Component for collecting various elements implementing either the Groupable or MultiGroupable interfaces, for example, a Group of RadioButtons, or a Group of RolloverImages. 
 * It can then be used to determine which, if any, groupable elements are selected. The difference between Groupable and MultiGroupable is that only 1 Groupable element can be selected, whereas many MultiGroupable elements can be selected at once, e.g., a Group of CheckBoxes.
 * Note that a Group is not a Control, and does not have physical properties like Left, Top, etc... In this sense, it behaves much more like a Container than a Panel.
 * 
 * A Group is typically added to some Container or Panel and objects are added to it so that they too will show, for example:
 * <pre>
 * // Instantiate a new Panel
 * $panel = new Panel();
 * // Instantiate a new Group
 * $group = new Group();
 * // Instantiate a new RolloverImage
 * // Add the group to the Panel
 * $panel->Controls->Add($group);
 * // Add a RolloverImage to that group
 * 
 * </pre>
 * 
 * @package Collections
 */
class Group extends Component implements ArrayAccess, Countable, Iterator
{
	private $Groupees;
	/**
	 * @ignore
	 */
	public $WaitingList;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Group.
	 * @return Group
	 */
	function Group()
	{
		parent::Component();
		$this->Groupees = new ArrayList();
		$this->Groupees->ParentId = $this->Id;
		$this->WaitingList = array();
	}
	/**
	 * Returns the Change Event of the Group
	 * @return Event
	 */
	function GetChange()				{return $this->GetEvent('Change');}
	/**
	 * Sets the Change Event of the Group
	 * @param Event $change
	 */
	function SetChange($change)			{return $this->SetEvent($change, 'Change');}
	/**
	 * Adds an element to the Group.
	 * @param mixed $element The element to be added 
	 * @return mixed The element that has been added
	 */
	function Add($element)
	{
		if(!($element instanceof Groupable || $element instanceof MultiGroupable))
			BloodyMurder('Object Added to Group does not implement Groupable or MultiGroupable');
		$element->SetGroupName($this->Id);
		/*if($this->GetShowStatus())
			NolohInternal::SetProperty('Group', $this->Id, $element);*/
		$this->Groupees->Add($element);
		return $element;
	}
	/**
	 * Adds an unlimited number elements to the Group, or the contents (keys will not be preserved) of one array if that is the lone parameter.
	 * @param mixed,... $dotDotDot Unlimited number of elements to be added
	 * <pre>
	 * // The following two statements have the same effect.
	 * $group->AddRange($firstElement, $secondElement, $thirdElement, $fourthElement);
	 * $group->AddRange(array($firstElement, $secondElement, $thirdElement, $fourthElement));
	 * </pre>
	 */
	function AddRange($dotDotDot)
	{
		$numArgs = func_num_args();
		$args = $numArgs === 1 && (is_array($dotDotDot) || $dotDotDot instanceof Iterator) ? $dotDotDot : func_get_args();
		foreach($args as $val)
			$this->Add($val);
	}
	/**
	 * Inserts an element into a particular index of the Group, not overwriting what was previously there.
	 * If the index is an integer, the Group is reindexed to fill in the gap.
	 * If the index is a string and there already is an element at the specified index, that element's index will be appended
	 * with a ' (indicating prime), to make room for the element being inserted.
	 * @param mixed $element The element to be inserted
	 * @param integer|string $index The index into which your element will be inserted
	 * @return mixed The element that has been inserted
	 */
	function Insert($element, $index)
	{
		if(!($element instanceof Groupable || $element instanceof MultiGroupable))
			BloodyMurder('Object Added to Group does not implement Groupable or MultiGroupable');
		$element->SetGroupName($this->Id);
		//NolohInternal::SetProperty('Group', $this->Id, $element);
		$this->Groupees->Insert($element, $index);
		return $element;
	}
	 /** 
	  * Removes the first occurrence of a particular element from the Group.
	  * @param mixed $element The element to be removed
	  * @return boolean Whether the remove was successful
	  */
	function Remove($element)
	{
		if($this->Groupees->Remove($element))
		{
			$element->SetGroupName(null);
			if($element->GetSelected() && !$this->Change->Blank())
				$this->Change->Exec();
			return true;
		}
		return false;
	}
	/**
	 * Removes an unlimited number elements from the Group.
	 * @param mixed,... $dotDotDot Unlimited number of elements to be removed
	 * <pre>
	 * // The following two statements have the same effect.
	 * $group->RemoveRange($firstElement, $secondElement, $thirdElement, $fourthElement);
	 * $group->RemoveRange(array($firstElement, $secondElement, $thirdElement, $fourthElement));
	 * </pre>
	 */
	function RemoveRange($dotDotDot)
	{
		$numArgs = func_num_args();
		$args = $numArgs === 1 && (is_array($dotDotDot) || $dotDotDot instanceof Iterator) ? $dotDotDot : func_get_args();
		foreach($args as $val)
			$this->Remove($val);
	}
	/**
	 * Removes an element at a particular index. An element must exist there or an error is given.
	 * If the index is an integer, the Group is reindexed to fill in the gap.
	 * @param integer|string $index The index of the element to be removed
	 * @return mixed The element that was removed
	 */
	function RemoveAt($index)
	{
		if(isset($this->Groupees->Elements[$index]))
		{
			$this->Remove($element = $this->Groupees->Elements[$index]);
			if($element->GetSelected() && !$this->Change->Blank())
				$this->Change->Exec();
			return $element;
		}
		else 
			BloodyMurder('Index ' . $index . ' does not exist and cannot be removed from the group');
	}
	/**
	 * Clears the Group.
	 */
	function Clear()
	{
		$change = $this->Change;
		AddScript('_N.'.$this->Id.'.Elements=[];', Priority::High);
		//AddScript('window.'.$this->Id.'.=new Group();', Priority::High);
		//QueueClientFunction($this, 'window.'.$this->Id.'.Elements=Array', array(), true, Priority::High);
		foreach($this->Groupees->Elements as $groupee)
		{
			//NolohInternal::SetProperty('Group', '', $groupee);
			$groupee->SetGroupName(null);
			if($groupee->GetSelected() && !$change->Blank())
				$change->Exec();
		}
		$this->Groupees->Clear();
	}
	/**
	 * Returns the first index of the Group whose element is Selected, or -1 if it is not found
	 * @return integer|string
	 */
	function GetSelectedIndex()
	{
		foreach($this->Groupees as $index => $groupee)
			if($groupee->GetSelected())
				return $index;
		return -1;
	}
	/**
	* Returns the numerical position of the Group whose element is Selected, or -1 if it's not found
	* @return integer
	*/
	function GetSelectedPosition()
	{
		$i = 0;
		foreach($this->Groupees as $groupee)
			if($groupee->GetSelected())
				return $i;
			else ++$i;
		return -1;
	}
	/**
	 * Returns an array of selected indices, indexed numerically.
	 * @return array
	 */
	function GetSelectedIndices()
	{
		$array = array();
		foreach($this->Groupees as $index => $groupee)
			if($groupee->GetSelected())
				$array[] = $index;
		return $array;
	}
	/**
	 * Selects the element which is at a particular index, or Deselects if the parameter is -1 or null. If attempting to set, be sure that an element exists at that index, or an error will be given.
	 * @param integer|string $index
	 * @return $element The element that is selected
	 */
	function SetSelectedIndex($index)
	{
		if($index === -1 || $index === null)
			$this->Deselect(true);
		elseif(isset($this->Groupees[$index]))
		{
			$this->SetSelectedElement($element = $this->Groupees[$index]);
			return $element;
		}
		else
			BloodyMurder('Index ' . $index . ' does not exist and cannot be selected in a group.');
	}
	/**
	 * Returns the Value of the first selected element of the Group, or its Text if the element has a null Value, or null if no element is selected.
	 * @return mixed
	 */
	function GetSelectedValue()
	{
		if(($element = $this->GetSelectedElement()) !== null)
			return ($element->HasProperty('Value') && (($tmpValue = $element->Value) !== null))?$tmpValue:$element->Text;
		else
			return null;
	}
	/**
	 * Returns an array of selected values, indexed numerically.
	 * @return array
	 */
	function GetSelectedValues()
	{
		$array = array();
		foreach($this->Groupees as $groupee)
			if($groupee->GetSelected())
				$array[] = ($groupee->HasProperty('Value') && (($tmpValue = $groupee->Value) !== null))?$tmpValue:$groupee->Text;
		return $array;
	}
	/**
	 * Selects the first element which has a particular Value, or if an element does not have a Value property, its Text is compared instead.
	 * @param string $value The Value to be selected
	 * @return mixed The element that was selected, or null if no matches were found
	 */
	function SetSelectedValue($value)
	{
		foreach($this->Groupees as $groupee)
			if($groupee->HasProperty('Value'))
			{
				if($groupee->GetValue() == $value)
					return $this->SetSelectedElement($groupee);
			}
			elseif($groupee->GetText() == $value)
				return $this->SetSelectedElement($groupee);
		return null;
	}
	/**
	 * Deselects the first Selected element
	 * @param boolean $deselectMultiGroupables
	 * @return mixed The element that was deselected, or null if nothing was selected
	 */
	function Deselect($deselectMultiGroupables = false)
	{
		if(!isset($GLOBALS['_NGroupSelecting'.$this->Id]))
		{
			$oldElement = $this->GetSelectedElement();
			if($oldElement !== null && ($deselectMultiGroupables || !($oldElement instanceof MultiGroupable)))
			{
				$oldElement->SetSelected(false);
				return $oldElement;
			}
		}
		return null;
	}
	/**
	 * Returns the first element that is Selected, or null if no matches are found.
	 * @return mixed
	 */
	function GetSelectedElement()
	{
		$tmpIndex = $this->GetSelectedIndex();
		return $tmpIndex !== -1 ? $this->Groupees->Elements[$tmpIndex] : null;
	}
	/**
	 * Returns an array of all Selected elements, indexed numerically.
	 * @return array
	 */
	function GetSelectedElements()
	{
		$array = array();
		foreach($this->Groupees as $groupee)
			if($groupee->GetSelected())
				$array[] = $groupee;
		return $array;
	}
	/**
	 * Selects a particular element.
	 * @param mixed $element
	 * @return mixed The element that was selected
	 */
	function SetSelectedElement($element)
	{
		if(!isset($GLOBALS['_NGroupSelecting'.$this->Id]) && !$element->GetSelected())
		{
			$this->Deselect(!($element instanceof MultiGroupable));
			$GLOBALS['_NGroupSelecting'.$this->Id] = true;
			//if($oldElement != null && !($oldElement instanceof MultiGroupable && $element instanceof MultiGroupable))
			//	$oldElement->SetSelected(false);
			$element->SetSelected(true);
			unset($GLOBALS['_NGroupSelecting'.$this->Id]);
		}
		return $element;
	}
	/**
	 * Returns Text of the first Selected element of the Group, or the empty string if no matches are found.
	 * @return string
	 */
	function GetSelectedText()
	{
		return ($element = $this->GetSelectedElement()) !== null ? $element->GetText() : '';
	}
	/**
	 * Returns an array of all selected texts, indexed numerically.
	 * @return array
	 */
	function GetSelectedTexts()
	{
		$array = array();
		foreach($this->Groupees as $groupee)
			if($groupee->GetSelected())
				$array[] = $groupee->Text;
		return $array;
	}
	/**
	 * Selects the first element with a specified Text.
	 * @param string $text
	 */
	function SetSelectedText($text)
	{
		foreach($this->Groupees as $groupee)
			if($groupee->GetText() == $text)
				return $this->SetSelectedElement($groupee);
	}
	/**
	 * @ignore
	 */
	function UpdateEvent($eventType)
	{
		//QueueClientFunction($this, '_NChangeByObj', array('window.'.$this->Id, '\''.$eventType.'\'', '\''.$this->GetEvent($eventType)->GetEventString($eventType,$this->Id).'\''));
		QueueClientFunction($this, '_NChangeByObj', array('_N.'.$this->Id, '\'onchange\'', '\''.$this->GetEvent($eventType)->GetEventString($eventType,$this->Id).'\''));
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		parent::Show();
		AddNolohScriptSrc('Group.js');
		AddScript('_N.'.$this->Id.'=new _NGroup();', Priority::High);
		$listCount = count($this->WaitingList);
		for($i=0; $i<$listCount; ++$i)
		{
			$obj = &GetComponentById($this->WaitingList[$i]);
			NolohInternal::SetProperty($obj instanceof Groupable ? 'Group' : 'GroupM', $this->Id, $obj);
		}
		$this->WaitingList = null;
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		$this->SearchEngineShowChildren();
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$this->NoScriptShowChildren($indent);
	}
	/**
	 * @ignore
	 */
	function Bury()
	{
		foreach($this->Groupees as $groupee)
			$groupee->Bury();
		parent::Bury();
	}
	/**
	* @ignore
	*/
	function Resurrect()
	{
		foreach($this->Groupees as $groupee)
			$groupee->Resurrect();
		parent::Resurrect();	
	}
	/**
	 * @ignore
	 */	
	function Count()						{return $this->Groupees->Count();}
	/**
	 * @ignore
	 */
	function GetCount()						{return $this->Groupees->Count();}
	/**
	 * @ignore
	 */
	public function rewind() 				{$this->Groupees->rewind();}
	/**
	 * @ignore
	 */
	public function current() 				{return $this->Groupees->current();}
	/**
	 * @ignore
	 */
	public function key() 					{return $this->Groupees->key();}
	/**
	 * @ignore
	 */
	public function next() 					{return $this->Groupees->next();}
	/**
	 * @ignore
	 */
	public function valid() 				{return $this->Groupees->valid();}
	/**
	 * @ignore
	 */
	function offsetExists($key)				{return $this->Groupees->offsetExists($key);}
	/**
	 * @ignore
	 */
	function offsetGet($index)				{return $this->Groupees->offsetGet($index);}
	/**
	 * @ignore
	 */
	function offsetSet($index, $val)		
	{
		if($index === null)
			$this->Add($val);
		else
		{
			if(!($val instanceof Groupable || $val instanceof MultiGroupable))
				BloodyMurder('Object Added to Group does not implement Groupable or MultiGroupable');
			$val->SetGroupName($this->Id);
			/*if($this->GetShowStatus())
				NolohInternal::SetProperty('Group', $this->Id, $val);*/
			$this->Groupees->offsetSet($index, $val);
			/*if(isset($this->Groupees[$index]))
				$this->RemoveAt($index);
			$this->Groupees[$index] = &$val;*/
		}
	}
	/**
	 * @ignore
	 */
	function offsetUnset($index)			
	{
		$this->RemoveAt($index);
	}
	/**
	 * @ignore
	 */	
	function __call($function, $args)
	{
		call_user_method_array($function, $this->Groupees, $args);
	}
}

?>