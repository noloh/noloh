<?
/**
 * @ignore 
 *
 */
class Group extends Component implements ArrayAccess, Countable, Iterator
{
	private $Elements;
	
	function Group()
	{
		parent::Component();
		$this->Elements = new ArrayList();
		$this->Elements->ParentId = $this->Id;
	}
	function Add($element, $setByReference = true)
	{
		if(!($element instanceof Groupable || $element instanceof MultiGroupable))
			BloodyMurder('Object Added to Group does not implement Groupable or MultiGroupable');
		$element->SetGroupName($this->Id);
		$this->Elements->Add($element, $passByReference);
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
		$this->Elements->Insert($element, $index);
	}
	function Remove($element)
	{
		if(!($element instanceof Groupable || $element instanceof MultiGroupable))
			BloodyMurder('Object Added to Group does not implement Groupable or MultiGroupable');
		$element->SetGroupName(null);
		$this->Elements->Remove($element);		
	}
	function RemoveAt($index)
	{
		$this->Remove($this->Elements->Item[$index]);
	}
	function Clear()
	{
		foreach($this->Elements as $element)
			$element->SetGroupName(null);
		$this->Elements->Clear();
	}
	function GetSelectedIndex()
	{
		foreach($this->Elements as $index => $element)
			if($element->GetSelected())
				return $index;
		return -1;
	}
	function SetSelectedIndex($index)
	{
		$this->SetSelectedElement($this->Elements[$index]);
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
		foreach($this->Elements as $element)
			if($element->GetValue() == $value)
			{
				$this->SetSelectedElement($element);
				return;
			}
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
		return $tmpIndex != -1?$this->Elements->Item[$tmpIndex]:null;
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
	function SetSelectedText()
	{
		foreach($this->Elements as $element)
			if($element->GetText() == $value)
			{
				$this->SetSelectedElement($element);
				return;
			}		
	}
	/**
	 * @ignore
	 */
	function Bury()
	{
		$RadioButtonsCount = $this->RadioButtons->Count();
		for($i = 0; $i < $RadioButtonsCount; $i++)
			$this->RadioButtons->Item[$i]->Bury();
		parent::Bury();
	}
	/**
	 * @ignore
	 */	
	function Count()						{return $this->Elements->Count();}
	/**
	 * @ignore
	 */
	function GetCount()						{return $this->Elements->Count();}
	/**
	 * @ignore
	 */
	public function rewind() 				{$this->Elements->rewind();}
	/**
	 * @ignore
	 */
	public function current() 				{return $this->Elements->current();}
	/**
	 * @ignore
	 */
	public function key() 					{return $this->Elements->key();}
	/**
	 * @ignore
	 */
	public function next() 					{return $this->Elements->next();}
	/**
	 * @ignore
	 */
	public function valid() 				{return $this->Elements->valid();}
	/**
	 * @ignore
	 */
	function offsetExists($key)				{return $this->Elements->offsetExists($key);}
	/**
	 * @ignore
	 */
	function offsetGet($index)				{return $this->Elements->offsetGet($index);}
	/**
	 * @ignore
	 */
	function offsetSet($index, $val)		{$this->Elements->offsetSet($index, $val);}
	/**
	 * @ignore
	 */
	function offsetUnset($index)			{$this->Elements->offsetUnset($index);}
	/**
	 * @ignore
	 */	
	function __call($function, $args)
	{
		call_user_method_array($function, $this->Elements, $args);
	}
}

?>