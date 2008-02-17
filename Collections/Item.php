<?php
/**
 * @package Collections
 */
class Item
{
	public $Text;
	public $Value;
	
	function Item($text = '', $value = null)
	{
		$this->Text = $text;
		$this->Value = $value===null ? $text : $value;
	}
	
	/*
	function Item($value = '', $text = '')
	{
		$this->Value = $value;
		$this->Text = $text;
	}*/
}

?>