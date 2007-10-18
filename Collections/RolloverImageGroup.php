<?
/**
 * @package Collections
 */
class RolloverImageGroup extends RolloverGroup 
{
	private $RolloverImages;

	function RolloverImageGroup()
	{
		parent::RolloverGroup();
		$this->RolloverImages = new ArrayList();
	}
	
	function Add(&$whatObject, $PassByReference = true)
	{
		//if(get_class($whatObject) != "RolloverImage")
		if(!is_a($whatObject, "RolloverImage"))
			BloodyMurder("Non-RolloverImage added to a RolloverImageGroup.");
		if($this->RolloverImages->Count() == 0)
			$whatObject->Selected = true;
		$whatObject->GroupName = $this->Id;
		$this->RolloverImages->Add($whatObject, $PassByReference);
	}
	
	function AddRange($dotDotDot)
	{
		$numArgs = func_num_args();
		for($i = 0; $i < $numArgs; $i++)
		{
			$whatObject = func_get_arg($i);
			if(!is_a($whatObject, "RolloverImage"))
			//if(get_class($whatObject) != "RolloverImage")
				BloodyMurder("Non-RolloverImage added to a RolloverImageGroup.");
			$whatObject->GroupName = $this->Id;
			$this->RolloverImages->Add(GetComponentById($whatObject->Id), true);
		}
		unset($numArgs, $dotDotDot);
	}
	
	function GetSelectedIndex()
	{
		$rolloverImagesCount = $this->RolloverImages->Count();
		for($i = 0; $i < $rolloverImagesCount; $i++)
			if($this->RolloverImages->Item[$i]->Selected == true)
				return $i;
		return -1;
	}
	
	function SetSelectedIndex($index)
	{
		$this->RolloverImages->Item[$index]->Selected = true;
	}
	
	function &GetSelectedRolloverImage()
	{
		$SelectedIndex = $this->GetSelectedIndex();
		if($SelectedIndex != -1)
			return $this->RolloverImages->Item[$SelectedIndex];
		else 
		{
			$temp = null;
			return $temp;
		}
	}
	
	function SetSelectedRolloverImage(RolloverImage $whatRolloverImage)
	{
		$this->RolloverImages->Item[$this->RolloverImages->IndexOf($whatRolloverImage)]->Selected = true;
	}
	
	function Show()
	{
		$ParentShow = parent::Show();
		if($ParentShow == false)
			return;
		
		$RolloverImagesCount = $this->RolloverImages->Count();
		for($i = 0; $i < $RolloverImagesCount; $i++)
			$this->RolloverImages->Item[$i]->Show();
	}
}

?>