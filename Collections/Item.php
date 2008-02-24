<?php
/**
 * @package Collections
 */
class Item
{
	public $Text;
	public $Value;
	
	function Item($text = '', $value = '')
	{
		$this->Text = $text;
		$this->Value = func_num_args()==0 ? $text : $value;
	}
	
	/*
	function Item($value = '', $text = '')
	{
		$this->Value = $value;
		$this->Text = $text;
	}*/
}

?>