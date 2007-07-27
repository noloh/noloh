<?php
/**
 * @package Collections
 */
abstract class RolloverGroup extends Component
{
	function RolloverGroup()
	{
		parent::Component();
	}
	abstract function GetSelectedIndex();
	abstract function SetSelectedIndex($index);
	//The following should actually be added via an interface.
	abstract function Add(&$object, $passByReference = true);
	abstract function AddRange($dotDotDot);
}