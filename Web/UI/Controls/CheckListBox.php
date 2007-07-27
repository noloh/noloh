<?php
/**
 * @package UI
 * @subpackage Controls
 */	
class CheckListBox extends Panel
{
	public $Items;
	public $CheckBoxes;
	
	function CheckListBox($left = 0, $top = 0, $width = 83, $height = 40)  
	{
		parent::Panel($left, $top, $width, $height, $this);
		$this->Items = new ArrayList();
		$this->BackColor = "white";
		$this->Border = "2px ridge";
		$this->CheckBoxes = &$this->Controls;
		$this->CheckBoxes->AddFunctionName = "AddCheckBox";
	}
	function AddCheckBox($item)
	{
		if($this->Controls->Count() == 0)
			$whatTop = 0;
		else 
			$whatTop = $this->Controls->Item[$this->Controls->Count()-1]->Bottom;
			
		if(is_string($item))
		{
			$checkItem = new Item($item, $item);
			$newCheckBox = new CheckBox($item, 0, $whatTop, $this->Width);
		}
		elseif(is_object($item))
		{
			if($item instanceof Item)
			{
				$checkItem = $item;
				$newCheckBox = new CheckBox($item->Text, 0, $whatTop, $this->Width);
			}
			elseif($item instanceof CheckBox)
			{
				$checkItem = new Item($item->Text, $item->Text);
				$newCheckBox = $item;
			}
		}
		//$newCheckBox->Checked = &$CheckItem->Checked;
		$this->Items->Add($checkItem);
		//if(func_num_args()==1)
		$this->Controls->Add($newCheckBox, true, true);
		return $item;
	}
	function GetSelectedIndex()
	{
		$controlCount = $this->Controls->Count();
		for($i=0; $i<$controlCount; $i++)
			if($this->Controls->Item[$i]->Checked)
				return $i;
		return -1;
	}
	function SetSelectedIndex($idx, $bool=true)
	{
		$this->Controls->Item[$idx]->Checked = $bool;
	}
	function GetSelectedIndices()
	{
		$checkedArray = array();
		$controlCount = $this->Controls->Count();
		for($i=0; $i<$controlCount; $i++)
			if($this->Controls->Item[$i]->Checked)
				$checkedArray[] = $i;
		return $checkedArray;
	}
	function SetSelectedIndices($array, $bool=true)
	{
		foreach($array as $idx)
			$this->SetSelectedIndex($idx, $bool);
	}
	function GetSelectedValue()
	{
		$selIdx = $this->GetSelectedIndex();
		return $selIdx != -1 ? $this->Items->Item[$selIdx]->Value : "";
	}
	function SetSelectedValue($val, $bool=true)
	{
		$controlCount = $this->Controls->Count();
		for($i=0; $i<$controlCount; $i++)
			if($this->Items->Item[$i]->Value == $val)
				$this->Controls->Item[$i]->Checked = $bool;
	}
	function GetSelectedValues()
	{
		$checkedArray = array();
		$selectedIndices = $this->GetSelectedIndices();
		foreach($selectedIndices as $idx)
			$checkedArray[] = $this->Items->Item[$idx]->Value;
		return $checkedArray;
	}
	function SetSelectedValues($array, $bool=true)
	{
		foreach($array as $val)
			$this->SetSelectedValue($val, $bool);
	}
}

?>