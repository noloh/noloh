<?php
/**
 * @package Web.UI.Controls
 */
class BulletedList extends Control
{
	public $Ordered = false;
	public $ListItems;
	public $Type;
	public $Start;
	
	function BulletedList($whatLeft=0, $whatTop=0, $whatWidth=83, $whatHeight=50)
	{
		parent::Control($whatLeft, $whatTop, $whatWidth, $whatHeight);
		$this->ListItems = new ArrayList();
	}
	
	function Show($IndentLevel=0)
	{
		$parentShow = parent::Show();
		
		if($this->Ordered)
			$TagStr = "OL";
		else 
			$TagStr = "UL";
		
		$dispStr = str_repeat("  ", $IndentLevel) . "<DIV " . $parentShow . "'><" . $TagStr;
		
		if($this->Type != null)
			$dispStr .= " type='" . $this->Type . "'>";
		if($this->Start != null)
			$dispStr .= " start=" . $this->Start . ">";
			
		$ItemsCount = $this->ListItems->Count();
		for($i = 0; $i < $ItemsCount; $i++)
		{
			$IthItem = &$this->ListItems->Item[$i];
			if(!is_object($IthItem))
				$dispStr .= str_repeat("  ", $IndentLevel+1) . "<LI>" . $IthItem . "</LI>";
			elseif(get_class($IthItem) == "BulletedList")
				$IthItem->Show($IndentLevel+2);
			else 
			{
				$dispStr .= str_repeat("  ", $IndentLevel+1) . "<LI>";
				$IthItem->Show($IndentLevel+2);
				$dispStr .= str_repeat("  ", $IndentLevel+1) . "</LI>";
			}
		}
		$dispStr .= str_repeat("  ", $IndentLevel) . "</" . $TagStr . "></DIV>";
		NolohInternal::Show($dispStr, $this);
	}
}

?>