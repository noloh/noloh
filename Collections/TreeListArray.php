<?php
/**
 * @package Collections
 */
class TreeListArray extends ArrayList 
{
	function TreeListArray()
	{
		parent::ArrayList();
	}
	
	function AddInto($whatNode, $whatIndex)
	{
		$before = array_slice($this->Item, 0, $whatIndex);
		$this->Add($whatNode);
		$this->Item = array_merge($before, array_slice($this->Item, $whatIndex));
	}
}

?>