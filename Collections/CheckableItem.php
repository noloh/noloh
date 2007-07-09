<?php

class CheckableItem extends Item
{
	public $Checked;
	
	function CheckableItem($whatValue = "", $whatText = "")
	{
		parent::Item($whatValue, $whatText);
		$this->Checked = false;
	}
}
	
?>