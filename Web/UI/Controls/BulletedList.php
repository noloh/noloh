<?php
/**
 * @ignore
 */
class BulletedList extends Control
{
	public $Ordered = false;
	public $ListItems;
	public $Type;
	public $Start;
	
	function BulletedList($left=0, $top=0, $width=83, $height=50)
	{
		parent::Control($left, $top, $width, $height);
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
			$IthItem = &$this->ListItems->Elements[$i];
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