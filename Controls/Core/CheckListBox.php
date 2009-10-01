<?php
/**
 * CheckListBox class
 *
 *  A CheckListBox is a rectangular ListControl where the user is able to see all the Items as CheckBoxes and may select several of them at the same time.
 *
 * <pre>
 * // Instantiates a new CheckListBox
 * $checkListBox = new CheckListBox();
 * // Adds an Item to the CheckListBox
 * $checkListBox->Items->Add(new Item("value1", "text1"));
 * // Adds another Item to the CheckListBox
 * $checkListBox->Items->Add(new Item("value2", "text2"));
 * </pre>
 *
 * @package Controls/Core
 */
class CheckListBox extends ListControl
{
	/**
	 * The Group of CheckBox objects
	 * @var ArrayList
	 */
	public $CheckBoxes;

	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends CheckListBox.
	* @param integer $left
	* @param integer $top
	* @param integer $width
	* @param integer $height
	*/
	function CheckListBox($left = 0, $top = 0, $width = 83, $height = 40)
	{
		parent::ListControl($left, $top, $width, $height);
		$this->CheckBoxes = new Group();
		$this->CheckBoxes->SetParentId($this->Id);
		$this->SetCSSClass();
	}
	/**
	 * @ignore
	 */
	function SetCSSClass($cssClass=null)
	{
		parent::SetCSSClass('NCheckListBox '. $cssClass);
	}
	/**
	 * @ignore
	 */
	function GetChange()
	{
		return $this->CheckBoxes->GetChange();
	}
	/**
	 * @ignore
	 */
	function SetChange($change)
	{
		return $this->CheckBoxes->SetChange($change);
	}
	/**
	 * Adds an Item to the Items ArrayList.
	 * <br> This is equivalent to:
	 * <pre>$this->Items->Add($item)</pre>
	 * @param Item $item
	 * @return Item
	 */
	function AddItem($item)
	{
		$top = $this->CheckBoxes->Count() == 0 ? 0 : $this->CheckBoxes[$this->CheckBoxes->Count()-1]->Bottom;
		if(is_string($item))
		{
			$checkItem = new Item($item, $item);
			$newCheckBox = new CheckBox($item, 0, $top, System::Auto, System::Auto);
		}
		elseif(is_object($item))
		{
			if($item instanceof Item)
			{
				$checkItem = $item;
				$newCheckBox = new CheckBox($item->Text, 0, $top, System::Auto, System::Auto);
			}
			elseif($item instanceof CheckBox)
			{
				$checkItem = new Item($item->Text, $item->Text);
				$newCheckBox = $item;
				$newCheckBox->SetLeft(0);
				$newCheckBox->SetTop($top);
			}
		}
		$this->Items->Add($checkItem, true);
		$this->CheckBoxes->Add($newCheckBox);
		return $item;
	}
	/**
	 * Removes an Item from a particular index of the Items ArrayList.
	 * <br> This is equivalent to:
	 * <pre>$this->Items->RemoveAt($index)</pre>
	 * @param integer $index
	 */
	public function RemoveItemAt($index)
	{
		$this->Items->RemoveAt($index, true);
		$this->CheckBoxes->RemoveAt($index);
		$checkBoxCount = $this->CheckBoxes->Count();
		$this->CheckBoxes[$index]->Top = $index == 0 ? 0 : $this->CheckBoxes[$index-1]->Bottom;
		for($i=$index+1; $i<$checkBoxCount; ++$i)
			$this->CheckBoxes[$i]->Top = $this->CheckBoxes[$i-1]->Bottom;
	}
	/**
	 * Clears the Items ArrayList.
	 * <br> This is equivalent to:
	 * <pre>$this->Items->Clear()</pre>
	 */
	function ClearItems()
	{
		$this->Items->Clear(true);
		$this->CheckBoxes->Clear();
	}
	/**
	 * Returns the index of the first selected Item
	 * @return integer
	 */
	function GetSelectedIndex()
	{
		$checkBoxCount = $this->CheckBoxes->Count();
		for($i=0; $i<$checkBoxCount; $i++)
			if($this->CheckBoxes[$i]->Checked)
				return $i;
		return -1;
	}
	/**
	 * Selects or deselect an Item whose index in the Items ArrayList matches the parameter
	 * @param integer $index
	 * @param boolean $select Indicates whether the Item should be selected or deseleted
	 */
	function SetSelectedIndex($index, $select=true)
	{
		$this->CheckBoxes[$index]->Checked = $select;
	}
	/**
	 * Returns an array of all the indices of the selected Items
	 * @return array
	 */
	function GetSelectedIndices()
	{
		$checkedArray = array();
		$checkBoxCount = $this->CheckBoxes->Count();
		for($i=0; $i<$checkBoxCount; $i++)
			if($this->CheckBoxes[$i]->Checked)
				$checkedArray[] = $i;
		return $checkedArray;
	}
	/**
	 * Selects or deselects those and only those Items whose index in the Items ArrayList is an elements of the specified array.
	 * @param array|ArrayList $selectedIndices
	 * @param boolean $select Indicates whether the Item should be selected or deseleted
	 */
	function SetSelectedIndices($selectedIndices, $select=true)
	{
		foreach($selectedIndices as $idx)
			$this->SetSelectedIndex($idx, $select);
	}
	/**
	 * Gets the Value of a selected Item
	 * @return string
	 */
	function GetSelectedValue()
	{
		$selIdx = $this->GetSelectedIndex();
		return $selIdx != -1 ? $this->Items->Elements[$selIdx]->Value : '';
	}
	/**
	 * Selects an item whose Value matches the value passed in
	 * Note:This sets the SelectedIndex to the <b>FIRST</b> occurence of the Value in the Items ArrayList
	 * <br> Can also be called as a property
	 * <pre>$this->SelectedValue = 42;</pre>
	 * @param string $value
	 * @param boolean $select Indicates whether the Item should be selected or deseleted
	 * @return mixed If found, the value passed in; otherwise null.
	 */
	function SetSelectedValue($value, $select=true)
	{
		$checkBoxCount = $this->CheckBoxes->Count();
		for($i=0; $i<$checkBoxCount; $i++)
			if($this->Items->Elements[$i]->Value == $value)
			{
				$this->CheckBoxes[$i]->Checked = $select;
				return $value;
			}
		return null;
	}
	/**
	 * Returns the SelectedValue. This is a convenient alias because different types of Controls may have different interpretations of "Value."
	 * @return string
	 */
	function GetValue()			{return $this->GetSelectedValue();}
	/**
	 * Sets the SelectedValue. This is a convenient alias because different types of Controls may have different interpretations of "Value."
	 * @param string $value
	 */
	function SetValue($value)	{return $this->SetSelectedValue($value);}
	/**
	 * Returns an array of all the values of the selected Items
	 * @return array
	 */
	function GetSelectedValues()
	{
		$checkedArray = array();
		$selectedIndices = $this->GetSelectedIndices();
		foreach($selectedIndices as $idx)
			$checkedArray[] = $this->Items->Elements[$idx]->Value;
		return $checkedArray;
	}
	/**
	 * Selects or deselects those and only those Items whose values are elements of the specified array.
	 * @param array $array
	 * @param boolean $select Indicates whether the Items should be selected or deseleted
	 */
	function SetSelectedValues($array, $select=true)
	{
		foreach($array as $val)
			$this->SetSelectedValue($val, $select);
	}
	/**
	 * Gets the Text of a selected Item
	 * @return string
	 */
	function GetSelectedText()
	{
		$selIdx = $this->GetSelectedIndex();
		return $selIdx != -1 ? $this->Items->Elements[$selIdx]->Text : '';
	}
	/**
	 * Selects an item whose Text matches the value passed in
	 * Note:This sets the SelectedIndex to the <b>FIRST</b> occurence of the Value in the Items ArrayList
	 * <br> Can also be called as a property
	 * <pre>$this->SelectedText= 42;</pre>
	 * @param string $text
	 * @param boolean $select Indicates whether the Item should be selected or deseleted
	 * @return mixed If found, the value passed in; otherwise null.
	 */
	function SetSelectedText($text, $select=true)
	{
		$checkBoxCount = $this->CheckBoxes->Count();
		for($i=0; $i<$checkBoxCount; $i++)
			if($this->Items->Elements[$i]->Text == $text)
			{
				$this->CheckBoxes[$i]->Checked = $select;
				return $text;
			}
		return null;
	}
	/**
	 * Returns an array of all the texts of the selected Items
	 * @return array
	 */
	function GetSelectedTexts()
	{
		$checkedArray = array();
		$selectedIndices = $this->GetSelectedIndices();
		foreach($selectedIndices as $idx)
			$checkedArray[] = $this->Items->Elements[$idx]->Text;
		return $checkedArray;
	}
	/**
	* Selects or deselects those and only those Items whose texts are elements of the specified array.
	* @param array $array
	* @param boolean $select Indicates whether the Items should be selected or deseleted
	*/
	function SetSelectedTexts($array, $select=true)
	{
		foreach($array as $val)
			$this->SetSelectedText($val, $select);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		NolohInternal::Show('DIV', parent::Show(), $this);
		return $initialProperties;
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShow($indent);
		if($str !== false)
		{
			echo $indent, '<DIV ', $str, ">\n";
			$this->NoScriptShowChildren($indent);
			echo $indent, "</DIV>\n";
		}
	}
}

?>