<?
/**
 * @package Collections
 */
class RolloverTabGroup extends RolloverGroup 
{
	private $RolloverTabs;

	function RolloverTabGroup()
	{
		parent::RolloverGroup();
		$this->RolloverTabs = new ImplicitArrayList($this, "Add");
		$this->RolloverTabs->InsertFunctionName = "Insert";
		$this->RolloverTabs->ParentId = $this->Id;
	}
	function GetRolloverTabs()	{return $this->RolloverTabs;}
	function Add(&$whatObject, $PassByReference = true)
	{
		if(!($whatObject instanceof RolloverTab))
			BloodyMurder("Non-RolloverTab added to a RolloverTabGroup.");
		//if($this->RolloverTabs->Count() == 0)
			//$whatObject->Selected = true;
		$whatObject->GroupName = $this->Id;
		$this->RolloverTabs->Add($whatObject, $PassByReference, true);
	}
	function AddRange($dotDotDot)
	{
		$numArgs = func_num_args();
		for($i = 0; $i < $numArgs; $i++)
		{
			$object = func_get_arg($i);
			if(!($object instanceof RolloverTab))
				BloodyMurder("Non-RolloverTab added to a RolloverTabGroup.");
			$object->GroupName = $this->Id;
			$this->RolloverTabs->Add(GetComponentById($object->Id), true, true);
		}
	}
	function Insert($obj, $index)
	{
		if(!($obj instanceof RolloverTab))
			BloodyMurder("Non-RolloverTab inserted to a RolloverTabGroup.");
		$obj->GroupName = $this->Id;
		$this->RolloverTabs->Insert($obj, $index, true);
	}
	function GetSelectedIndex()
	{
		$rolloverTabsCount = $this->RolloverTabs->Count();
		for($i = 0; $i < $rolloverTabsCount; $i++)
			if($this->RolloverTabs->Item[$i]->Selected == true)
				return $i;
		return -1;
	}
	function SetSelectedIndex($index)
	{
		if($index == -1)
		{
			$this->GetSelectedRolloverTab()->Selected = false;
			return;
		}
		$this->RolloverTabs->Item[$index]->SetSelected(true);
//		if($this->RolloverTabs->Item[$index]->Click != null)
//			$this->RolloverTabs->Item[$index]->Click->Exec();
	}
	function GetSelectedRolloverTab()
	{
		$selectedIndex = $this->GetSelectedIndex();
		if($selectedIndex != -1)
			return $this->RolloverTabs->Item[$selectedIndex];
		else 
			return null;

	}
	function SetSelectedRolloverTab(RolloverTab $whatRolloverTab)
	{
		$this->SetSelectedIndex($this->RolloverTabs->IndexOf($whatRolloverTab));
		//$this->RolloverTabs->Item[$this->RolloverTabs->IndexOf($whatRolloverTab)]->Selected = true;
	}
}

?>