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
	 * @param bool $setByReference Indicates whether the Group sets by reference as opossed to by value
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
	function Insert($element, $index)
	{
		if(!($element instanceof Groupable || $element instanceof MultiGroupable))
			BloodyMurder('Object Added to Group does not implement Groupable or MultiGroupable');
		$element->SetGroupName($this->Id);
		NolohInternal::SetProperty('Group', $this->Id, $element);
		$this->Groupees->Insert($element, $index);
	}
	function Remove($element)
	{
		if(!($element instanceof Groupable || $element instanceof MultiGroupable))
			BloodyMurder('Object Added to Group does not implement Groupable or MultiGroupable');
		$element->SetGroupName(null);
		NolohInternal::SetProperty('Group', '', $element);
		$this->Groupees->Remove($element);		
	}
	function RemoveAt($index)
	{
		$this->Remove($this->Groupees->Elements[$index]);
	}
	function Clear()
	{
		AddScript('window.'.$this->Id.'.Elements=Array();', Priority::High);
		//AddScript('window.'.$this->Id.'.=new Group();', Priority::High);
		//QueueClientFunction($this, 'window.'.$this->Id.'.Elements=Array', array(), true, Priority::High);
		foreach($this->Groupees->Elements as $groupee)
		{
			NolohInternal::SetProperty('Group', '', $groupee);
			$groupee->SetGroupName(null);
		}
		$this->Groupees->Clear();
	}
	function GetSelectedIndex()
	{
		foreach($this->Groupees as $index => $groupee)
			if($groupee->GetSelected())
				return $index;
		return -1;
	}
	function SetSelectedIndex($index)
	{
		if($index == -1 || $index === null)
			$this->Deselect(true);
		else
			$this->SetSelectedElement($this->Groupees[$index]);
	}
	function GetSelectedValue()
	{
		if(($element = $this->GetSelectedElement()) != null)
			return ($tmpValue = $element->Value) == null?$element->Text:$tmpValue;
		else
			return null;
	}
	function SetSelectedValue($value)
	{
		foreach($this->Groupees as $groupee)
			if($groupee->GetValue() == $value)
				return $this->SetSelectedElement($groupee);
	}
	function Deselect($deselectMultiGroupables = false)
	{
		if(!isset($GLOBALS['_NGroupSelecting'.$this->Id]))
		{
			$oldElement = $this->GetSelectedElement();
			if($oldElement != null && ($deselectMultiGroupables || !($oldElement instanceof MultiGroupable)))
				$oldElement->SetSelected(false);
		}
	}
	function GetSelectedElement()
	{
		$tmpIndex = $this->GetSelectedIndex();
		return $tmpIndex != -1?$this->Groupees->Elements[$tmpIndex]:null;
	}
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
	function GetSelectedText()
	{
		return ($element = $this->GetSelectedElement()) != null ? $element->GetText() : '';
	}
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
		foreach($this->Controls as $control)
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