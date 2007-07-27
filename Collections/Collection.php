<?php
/**
 * @ignore 
 *
 */
abstract class Collection extends ArrayObject
{
	public $Item = array();
	function Collection() {}
	abstract public function Add(&$whatObject);
	abstract public function AddInto(&$whatObject, $whatIndex);
	abstract public function AddRange($PassByReference = true, $DotDotDot);
	abstract public function Count();
	abstract public function RemoveAt($whatIndex);
	abstract public function RemoveItem(&$whatItem);
	abstract public function IndexOf(&$what);
	abstract public function Clear();
}
	
?>