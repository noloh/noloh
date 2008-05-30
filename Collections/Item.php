<?php
/**
 * @package Collections
 */
/**
 * Item class
 * 
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
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