<?php
/**
 * TableColumn class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Auxiliary
 */
class TableColumn extends Panel
{
	function TableColumn($object=null, $width=null, $height=null)
	{
		Panel::Panel(null, null, $width, $height, $this);
		$this->Controls->AddFunctionName = "AddControl";
		$this->Layout = Layout::Relative;
		$this->SetControl($object);
	}
	function AddControl($control)
	{
		$this->Controls->Add($control, true, true);
		$control->Layout = Layout::Relative;
	}
	public function GetControl()
	{
		if($this->Controls->Count() > 0)
			return $this->Controls->Elements[0];
		return null;
	}
	public function SetControl($object=null)
	{
		if($object != null)
			if($this->Controls->Count() > 0)
				$this->Controls->Elements[0] = $object;
			else
				$this->Controls->Add($object);
	}
	/**
	 * @ignore
	 */
	function GetAddId()
	{
		return $this->Id . 'IC';
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		$initialProperties = Control::Show();
		NolohInternal::Show('TD', $initialProperties, $this);
//		$initialProperties = "'id','{$this->Id}InnerCol','style.position','relative','style.overflow','hidden'";
		//$initialProperties = "'id','{$this->Id}InnerCol','style.position','relative','style.overflow','hidden'";
		$initialProperties = "'style.position','relative','style.overflow','hidden','style.width','100%'";
		NolohInternal::Show('DIV', $initialProperties, $this, $this->Id, $this->Id.'IC');
	}
}

?>