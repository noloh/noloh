<?php
/**
 * @package Collections
 */
class Item
{
	public $Text;
	public $Value;
	
	function Item($value = '', $text = '')
	{
		$this->Value = $value;
		$this->Text = $text;
	}
}
	
?>