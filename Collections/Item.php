<?php
/**
 * Item class
 * 
 * An Item is an Object for holding a Text and Value pair. These are most commonly used in connection with ListControl but could serve for anything requiring a Text and Value pair.
 * One important thing to note about Item is that it will not in any way ping its parent collection when its Text or Value changes. Instead, a new Item should be created.
 * 
 * @package Collections
 */
class Item extends Object
{
	/**
	 * The Item's Text
	 * @var mixed
	 */
	public $Text;
	/**
	 * The Item's Value
	 * @var mixed
	 */
	public $Value;
	/**
	 * Constructor. Be sure to call this from the constructor of any class that extends Item.
	 * @param mixed $text
	 * @param mixed $value
	 * @return Item
	 */
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