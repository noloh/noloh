<?
/**
 * @ignore 
 */
class Group extends Component implements ArrayAccess, Countable, Iterator
{
	private $Groupees;
	
	function Group()
	{
		parent::Component();
		$this->Groupees = new ArrayList();
		$this->Groupees->ParentId = $this->Id;
	}
	function Add($element, $setByReference = true)
	{
		if(!($element instanceof Groupable || $element instanceof MultiGroupable))
			BloodyMurder('Object Added to Group does not implement Groupable or MultiGroupable');
		$element->SetGroupName($this->Id);
		if($this->GetShowStatus())
			NolohInternal::SetProperty('Group', $this->Id, $element);
		$this->Groupees->Add($element, $setByReference);
	}
	function AddRange($dotDotDot)
	{
		$numArgs = func_num_args();
		for($i = 0; $i < $numArgs; ++$i)
			$this->Add(GetComponentById(func_get_arg($i)->Id));
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
		foreach($this->Groupees as $groupee)
			$groupee->SetGroupName(null);
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
		if(!isset($GLOBALS['_NGroupSelecting'.$this->Id]))
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
	function offsetSet($index, $val)		{$this->Groupees->offsetSet($index, $val);}
	/**
	 * @ignore
	 */
	function offsetUnset($index)			{$this->Groupees->offsetUnset($index);}
	/**
	 * @ignore
	 */	
	function __call($function, $args)
	{
		call_user_method_array($function, $this->Groupees, $args);
	}
}

?>