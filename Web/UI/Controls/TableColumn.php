<?php

class TableColumn extends Panel
{
	//public $Span;
	function TableColumn($object=null, $width=null, $height=null)
	{
		Panel::Panel(null, null, $width, $height, $this);
		$this->Controls->AddFunctionName = "AddControl";
		$this->PositionType = 1;
		//$this->Border = "1px solid black";
		$this->SetControl($object);
	}
	function AddControl($control)
	{
		//if(func_num_args()==1)
		$this->Controls->Add($control, true, true);
		$control->PositionType = 1;
	}
	public function GetControl()
	{
		if($this->Controls->Count() > 0)
			return $this->Controls->Item[0];
		return null;
	}
	public function SetControl($object=null)
	{
		if($object != null)
			if($this->Controls->Count() > 0)
				$this->Controls->Item[0] = $object;
			else
				$this->Controls->Add($object);
	}
	function GetAddId()
	{
		return $this->DistinctId . 'IC';
	}
	function Show()
	{
		$initialProperties = Control::Show();
		NolohInternal::Show("TD", $initialProperties, $this);
//		$initialProperties = "'id','{$this->DistinctId}InnerCol','style.position','relative','style.overflow','hidden'";
		//$initialProperties = "'id','{$this->DistinctId}InnerCol','style.position','relative','style.overflow','hidden'";
		$initialProperties = "'id','{$this->DistinctId}IC','style.position','relative','style.overflow','hidden','style.width','{$this->Width}px'";
		NolohInternal::Show("DIV", $initialProperties, $this, $this->DistinctId);
	}
}

?>