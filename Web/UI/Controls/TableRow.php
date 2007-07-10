<?php

class TableRow extends Control
{
	public $Columns;
	public $Span;
	
	function TableRow()
	{
		parent::Control(0, 0, null, 20);
		$this->PositionType = 1;
		$this->Columns = new ArrayList();
		$this->Columns->ParentId = $this->DistinctId;
	}
	
	function Show()
	{
		$intialProperties = parent::Show();
		//$intialProperties .= ",'style.border','0px'";
		NolohInternal::Show("TR", $intialProperties, $this, $this->ParentId."InnerTBody");
	}
}
?>