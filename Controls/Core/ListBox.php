<?php
/**
 * ListBox class
 *
 * A ListBox is a rectangular ListControl where the user is able to see all the Items and may select several of them at the same time.
 * This differs from a {@see ComboBox} in that a ComboBox must be pulled down in order to view the list of items and only one may be selected.
 * 
 * <pre>
 * // Instantiates a new ListBox
 * $listBox = new ListBox();
 * // Adds an Item to the ListBox
 * $listBox->Items->Add(new Item("value1", "text1"));
 * // Adds another Item to the ListBox
 * $listBox->Items->Add(new Item("value2", "text2"));
 * </pre>
 * 
 * @package Controls/Core
 */
class ListBox extends ListControl
{
	/**
	 * @ignore
	 */
	private $SelectedIndices;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Listbox.
	 * @param integer $left
	 * @param integer $top
	 * @param integer $width
	 * @param integer $height
	 * @return ListBox
	 */
	function ListBox($left = 0, $top = 0, $width = 83, $height = 40)
	{
		parent::ListControl($left, $top, $width, $height);
		$this->SelectedIndices = array();
	}
	/**
	 * Returns the index of the first selected Item
	 * @return integer
	 */
	function GetSelectedIndex()
	{
		if($this->SelectedIndices == null)
			return -1;
		else 
		{
			$i = 0;
			$pos = min($this->SelectedIndices);
			//Should be cached
			foreach($this->Items as $idx => $item)
			{
				if($i == $pos)
					return $idx;
				$i++;
			}
		}
	}
	/**
	* Returns the numerical position of the ListBox whose element is Selected, or -1 if it's not found
	* @return integer
	*/
	function GetSelectedPosition()
	{
		if($this->SelectedIndices == null)
			return -1;
		else
			return min($this->SelectedIndices);
	}
	/**
	* @ignore
	*/
	public function RemoveItemAt($index)
	{
		parent::RemoveItemAt($index);
		if(is_int($index))
		{
			$count = count($this->SelectedIndices);
			for($i = 0; $i < $count; ++$i)
			{
				$key = $this->SelectedIndices[$i];
				if(is_numeric($key) && $key > $index)
					--$this->SelectedIndices[$i];
			}
		}
	}
	/**
	 * Selects an Item whose index in the Items ArrayList matches the parameter
	 * @param integer $index
	 */
	function SetSelectedIndex($index)
	{
		if(!in_array($index, $this->SelectedIndices, true))
		{
			$this->SelectedIndices[] = $index;
			parent::SetSelectedIndex($index);
			ClientScript::Queue($this, '_NLstCtrSaveSelInds', array($this), true, Priority::Low);
		}
	}
	/**
	 * Returns an array of all the indices of the selected Items
	 * @return array
	 */
	function GetSelectedIndices()
	{
		return $this->SelectedIndices;
	}
	/**
	 * Selects those and only those Items whose index in the Items ArrayList is an element of the specified array.
	 * @param array|ArrayList $selectedIndices
	 */
	function SetSelectedIndices($selectedIndices)
	{
		$this->ClearSelected();
		if(is_array($selectedIndices))
			foreach($selectedIndices as $idx)
				$this->SetSelectedIndex($idx);
	}
	/**
	 * Returns an array of all the values of the selected Items
	 * @return array
	 */
	function GetSelectedValues()
	{
		$selectedArray = array();
		$selectedIndices = $this->GetSelectedIndices();
		//Alert(count($this->SelectedIndices) . ' is the number of selected indices');
		foreach($selectedIndices as $idx)
			$selectedArray[] = $this->Items->Elements[$idx]->Value;
		return $selectedArray;
	}
	/**
	 * Selects those and only those Items whose value in the Items ArrayList is an element of the specified array.
	 * @param array|ArrayList $selectedValues
	 */
	function SetSelectedValues($selectedValues)
	{
		$this->ClearSelected();
		if(is_array($selectedValues))
			foreach($selectedValues as $value)
				$this->SetSelectedValue($value);
	}
	/**
	 * Selects or deselects an Item whose index in the Items ArrayList matches the parameter
	 * @param integer $index
	 * @param boolean $select Indicates whether the Item should be selected or deseleted
	 */
	function SetSelected($index, $select)
	{
		if($select)
			$this->SetSelectedIndex($index);
		elseif(in_array($index, $this->SelectedIndices, true))
		{
			//NolohInternal::SetProperty("options[$index].selected", false, $this);
			//QueueClientFunction($this, "_N('$this->Id').options[$index].selected=false;void", array(0));
			ClientScript::Queue($this, '_NLstCtrDesel', array($this, $index), false);
			//AddScript("_N('$this->Id').options[$index].selected=false");
			unset($this->SelectedIndices[array_search($index, $this->SelectedIndices)]);
			ClientScript::Queue($this, '_NLstCtrSaveSelInds', array($this), true, Priority::Low);
		}
	}
	/**
	 * Deselects all of the ListBox's Items
	 */
	function ClearSelected()
	{
		$this->SelectedIndices = array();
		ClientScript::Queue($this, '_NLstCtrClrSel', array($this), false);
		ClientScript::Queue($this, '_NLstCtrSaveSelInds', array($this), true, Priority::Low);
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		$preStr = '';
		if($eventTypeAsString == 'Change')
			$preStr = '_NSave("'.$this->Id.'","_NSelectedIndices",_NLstCtrExplSelInds(this.options));';
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function Set_NSelectedIndices($indicesString)
	{
		$this->SelectedIndices = $indicesString!=='' ? explode('~d2~', $indicesString) : array();
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		ClientScript::AddNOLOHSource('ListControl.js');
		$initialProperties = parent::Show() . '\'multiple\',\'true\'' . $this->GetEventString(null);
		NolohInternal::Show('SELECT', $initialProperties, $this);
		ClientScript::Queue($this, '_NLstCtrSaveSelInds', array($this), true, Priority::Low);
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = Control::NoScriptShow($indent);
		if($str !== false)
			echo $indent, '<SELECT MULTIPLE ', $str, ">\n", ListControl::NoScriptShow($indent), $indent, "</INPUT>\n";
	}
}

?>