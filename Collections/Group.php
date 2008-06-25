<?
/**
 * Group class
 * 
 * A Group is a Component for collecting various elements implementing either the Groupable or MultiGroupable interfaces, for example, a Group of RadioButtons, or a Group of RolloverImages. 
 * It can then be used to determine which, if any, groupable elements are selected. The difference between Groupable and MultiGroupable is that only 1 Groupable element can be selected, whereas many MultiGroupable elements can be selected at once, e.g., a Group of CheckBoxes.
 * Note that a Group is not a Control, and does not have physical properties like Left, Top, etc... In this sense, it behaves much more like a Container than a Panel.
 * 
 * A Group is typically added to some Container or Panel and objects are added to it so that they too will show, for example:
 * <code>
 * // Instantiate a new Panel
 * $panel = new Panel();
 * // Instantiate a new Group
 * $group = new Group();
 * // Instantiate a new RolloverImage
 * // Add the group to the Panel
 * $panel->Controls->Add($group);
 * // Add a RolloverImage to that group
 * 
 * </code>
 * 
 * @package Collections
 */
class Group extends Component implements ArrayAccess, Countable, Iterator
{
	private $Groupees;
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
	 * @param boolean $setByReference Indicates whether the Group sets by reference as opossed to by value
	 * @return mixed The element that has been added
	 */
	function Add($element, $setByReference = true)
	{
		if(!($element instanceof Groupable || $element instanceof MultiGroupable))
			BloodyMurder('Object Added to Group does not implement Groupable or MultiGroupable');
		$element->SetGroupName($this->Id);
		if($this->GetShowStatus())
			NolohInternal::SetProperty('Group', $this->Id, $element);
		$this->Groupees->Add($element, $setByReference);
		return $element;
	}
	/**
	 * Adds an unlimited number elements to the Group.
	 * @param mixed ... Unlimited number of elements to be added
	 * <code>$group->AddRange($firstElement, $secondElement, $thirdElement, $fourthElement);</code>
	 */
	function AddRange($dotDotDot)
	{
		$args = func_get_args();
		$numArgs = count($args);
		for($i = 0; $i < $numArgs; ++$i)
			if($args[$i] instanceof Component)
				$this->Add(GetComponentById($args[$i]->Id));
			else 
				$this->Add($args[$i]);
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
		NolohInternal::SetProperty('Group', $this->Id, $element);
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
		if(!($element instanceof Groupable || $element instanceof MultiGroupable))
			BloodyMurder('Object Added to Group does not implement Groupable or MultiGroupable');
		$element->SetGroupName(null);
		NolohInternal::SetProperty('Group', '', $element);
		$this->Groupees->Remove($element);		
	}
	/**
	 * Removes an element at a particular index. 
	 * If the index is an integer, the Group is reindexed to fill in the gap.
	 * @param integer|string $index The index of the element to be removed
	 */
	function RemoveAt($index)
	{
		$this->Remove($this->Groupees->Elements[$index]);
	}
	/**
	 * Clears the Group.
	 */
	function Clear()
	{
		AddScript('window.'.$this->Id.'.Elements=[];', Priority::High);
		//AddScript('window.'.$this->Id.'.=new Group();', Priority::High);
		//QueueClientFunction($this, 'window.'.$this->Id.'.Elements=Array', array(), true, Priority::High);
		foreach($this->Groupees->Elements as $groupee)
		{
			NolohInternal::SetProperty('Group', '', $groupee);
			$groupee->SetGroupName(null);
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
	 * Selects the element which is at a particular index, or Deselects if the parameter is -1 or null
	 * @param integer|string $index
	 */
	function SetSelectedIndex($index)
	{
		if($index == -1 || $index === null)
			$this->Deselect(true);
		else
			$this->SetSelectedElement($this->Groupees[$index]);
	}
	/**
	 * Returns the Value of the first selected element of the Group, or null if it is not found
	 * @return mixed
	 */
	function GetSelectedValue()
	{
		if(($element = $this->GetSelectedElement()) != null)
			return ($tmpValue = $element->Value) == null?$element->Text:$tmpValue;
		else
			return null;
	}
	/**
	 * Selects the first element which has a particular Value
	 * @param string $value
	 */
	function SetSelectedValue($value)
	{
		foreach($this->Groupees as $groupee)
			if($groupee->GetValue() == $value)
				return $this->SetSelectedElement($groupee);
	}
	/**
	 * Deselects the first Selected element
	 * @param boolean $deselectMultiGroupables
	 */
	function Deselect($deselectMultiGroupables = false)
	{
		if(!isset($GLOBALS['_NGroupSelecting'.$this->Id]))
		{
			$oldElement = $this->GetSelectedElement();
			if($oldElement != null && ($deselectMultiGroupables || !($oldElement instanceof MultiGroupable)))
				$oldElement->SetSelected(false);
		}
	}
	/**
	 * Returns the first element that is Selected, or null if none are
	 * @return mixed
	 */
	function GetSelectedElement()
	{
		$tmpIndex = $this->GetSelectedIndex();
		return $tmpIndex != -1?$this->Groupees->Elements[$tmpIndex]:null;
	}
	/**
	 * Selects a particular element
	 * @param mixed $element
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
	}
	/**
	 * Returns Text of the first element of the Group, or the empty string if it is not found
	 * @return string
	 */
	function GetSelectedText()
	{
		return ($element = $this->GetSelectedElement()) != null ? $element->GetText() : '';
	}
	/**
	 * Selects the first element which has a particular Text
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
		//QueueClientFunction($this, 'NOLOHChangeByObj', array('window.'.$this->Id, '\''.$eventType.'\'', '\''.$this->GetEvent($eventType)->GetEventString($eventType,$this->Id).'\''));
		QueueClientFunction($this, 'NOLOHChangeByObj', array('window.'.$this->Id, '\'onchange\'', '\''.$this->GetEvent($eventType)->GetEventString($eventType,$this->Id).'\''));
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		parent::Show();
		AddNolohScriptSrc('Group.js');
		AddScript('window.'.$this->Id.'=new Group();', Priority::High);
		foreach($this->Groupees as $groupee)
			NolohInternal::SetProperty('Group', $this->Id, $groupee);
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		foreach($this->Groupees as $control)
			$control->SearchEngineShow();
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
			$this->Add($val, true);
		else
		{
			$val->SetGroupName($this->Id);
			if($this->GetShowStatus())
				NolohInternal::SetProperty('Group', $this->Id, $val);
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