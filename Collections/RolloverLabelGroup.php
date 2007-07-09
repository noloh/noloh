<?php
class RolloverLabelGroup extends RolloverGroup
{
	private $RolloverLabels;
	
	function RolloverLabelGroup()
	{
		parent::RolloverGroup();
		$this->RolloverLabels = new ImplicitArrayList($this, "Add");
		$this->RolloverLabels->ParentId = $this->DistinctId;
	}
	function GetRolloverLabels()	{return $this->RolloverLabels;}
	function Add(&$whatObject, $PassByReference = true)
	{
		if(!($whatObject instanceof RolloverLabel))
			BloodyMurder("Non-RolloverLabel added to a RolloverLabelGroup.");
		//if($this->RolloverTabs->Count() == 0)
			//$whatObject->Selected = true;
		$whatObject->GroupName = $this->DistinctId;
		$this->RolloverLabels->Add($whatObject, $PassByReference, true);
	}
	function AddRange($dotDotDot)
	{
		$numArgs = func_num_args();
		for($i = 0; $i < $numArgs; $i++)
		{
			$whatObject = &func_get_arg($i);
			if(!($whatObject instanceof RolloverLabel))
				BloodyMurder("Non-RolloverLabel added to a RolloverLabelGroup.");
			$whatObject->GroupName = $this->DistinctId;
			$this->RolloverLabels->Add(GetComponentById($whatObject->DistinctId), $PassByReference, true);
		}
		unset($numArgs, $DotDotDot);
	}
	
	function GetSelectedIndex()
	{
		$rolloverLabelsCount = $this->RolloverLabels->Count();
		for($i = 0; $i < $rolloverLabelsCount; $i++)
			if($this->RolloverLabels->Item[$i]->Selected == true)
				return $i;
		return -1;
	}
	function SetSelectedIndex($index)
	{
		if($index == -1)
		{
			$this->GetSelectedRolloverLabel()->SetSelected(false);
			return;
		}
		$this->RolloverLabels->Item[$index]->SetSelected(true);
//		if($this->RolloverLabels->Item[$index]->Click != null)
//			$this->RolloverLabels->Item[$index]->Click->Exec();
	}
	function GetSelectedRolloverLabel()
	{
		$selectedIndex = $this->GetSelectedIndex();
		if($selectedIndex != -1)
			return $this->RolloverLabels->Item[$selectedIndex];
		else 
		{
			$temp = null;
			return $temp;
		}
	}
	function SetSelectedRolloverLabel(RolloverLabel $rolloverLabel)
	{
		$this->SetSelectedIndex($this->RolloverLabels->IndexOf($rolloverLabel));
	}
	function Hide()
	{
		$rolloverLabelCount = $this->RolloverLabels->Count();
		for($i = 0; $i < $rolloverLabelCount; $i++)
			$this->RolloverLabels->Item[$i]->Hide();
		parent::Hide();
	}
}
?>