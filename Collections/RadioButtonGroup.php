<?

class RadioButtonGroup extends RolloverGroup 
{
	public $RadioButtons;

	function RadioButtonGroup()
	{
		parent::RolloverGroup();
		$this->RadioButtons = new ArrayList();
		$this->RadioButtons->ParentId = $this->DistinctId;
	}
	function Add(&$object, $passByReference = true)
	{
		if(!($object instanceof RadioButton))
			BloodyMurder("Non-RadioButton added to a RadioButtonGroup.");
		$object->GroupName = $this->DistinctId;
		$this->RadioButtons->Add($object, $passByReference);
	}
	function AddRange($dotDotDot)
	{
		$numArgs = func_num_args();
		for($i = 0; $i < $numArgs; $i++)
		{
			if(!(func_get_arg($i) instanceof RadioButton))
				BloodyMurder("Non-RadioButton added to a RadioButtonGroup.");
			$this->Add(GetComponentById(func_get_arg($i)->DistinctId));
		}
	}
	//Deprecated
	function GetValue()
	{
		/*for($i = $this->RadioButtons->Count(); $i >= 0; $i--)
			if($this->RadioButtons->Item[$i]->Checked == true)
				return $this->RadioButtons->Item[$i]->Text;
		*/
		for($i = $this->RadioButtons->Count() -1; $i >= 0; $i--)
			if($this->RadioButtons->Item[$i]->Checked == true)
				return $this->RadioButtons->Item[$i]->Value == null ? 
					$this->RadioButtons->Item[$i]->Text :
					$this->RadioButtons->Item[$i]->Value;
				
		return null;
	}
	function SetSelectedIndex($index)
	{
	}
	function GetSelectedIndex(){return $this->SelectedIndex;}
	function GetSelectedValue()
	{
		$tmpCount = $this->RadioButtons->Count();
		for($i = 0;  $i < $tmpCount; $i++)
			if($this->RadioButtons->Item[$i]->Checked == true)
				return $this->RadioButtons->Item[$i]->Value == null ? 
					$this->RadioButtons->Item[$i]->Text :
					$this->RadioButtons->Item[$i]->Value;
				
		return null;
	}
	//Deprecated
	function GetText()
	{
		$tmpCount = $this->RadioButtons->Count();
		for($i = 0;  $i < $tmpCount; $i++)
			if($this->RadioButtons->Item[$i]->Checked == true)
				return $this->RadioButtons->Item[$i]->Text;

		return null;
	}
	function GetSelectedText()
	{
		$tmpCount = $this->RadioButtons->Count();
		for($i = 0;  $i < $tmpCount; $i++)
			if($this->RadioButtons->Item[$i]->Checked == true)
				return $this->RadioButtons->Item[$i]->Text;

		return null;
	}
	function SetSelectedValue($value)
	{
		$tmpCount = $this->RadioButtons->Count();
		for($i = 0;  $i < $tmpCount; $i++)
			if($this->RadioButtons->Item[$i]->Value == $value)
			{
				$this->RadioButtons->Item[$i]->Checked = true;
				return;
			}
	}
	/*function Show()
	{
		$ParentShow = parent::Show();
		if($ParentShow === false)
			return;
		
		$RadioButtonsCount = $this->RadioButtons->Count();
		for($i = 0; $i < $RadioButtonsCount; $i++)
			$this->RadioButtons->Item[$i]->Show();
	}*/
	function Hide()
	{
		$RadioButtonsCount = $this->RadioButtons->Count();
		for($i = 0; $i < $RadioButtonsCount; $i++)
			$this->RadioButtons->Item[$i]->Hide();
		parent::Hide();
	}
}

?>