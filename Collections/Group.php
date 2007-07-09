<?
abstract class Group extends Component
{
	function Group()
	{
		parent::Component();
	}
	abstract function GetSelectedIndex();
	abstract function SetSelectedIndex($index);
	//The following should actually be added via an interface.
	abstract function Add(&$object, $passByReference = true);
	abstract function AddRange($dotDotDot);
}
?>