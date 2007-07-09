<?php

class Item
{
	public $Text;
	public $Value;
	
	function Item($whatValue = "", $whatText = "")
	{
		$this->Value = $whatValue;
		$this->Text = $whatText;
	}
}
	
?>