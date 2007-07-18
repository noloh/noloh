<?php

class Table extends Control
{
	public $Rows;
	public $BuiltMatrix;
	public $ScrollLeft;
	public $ScrollTop;
	
	function Table($left=0, $top=0, $width=500, $height=500)
	{
		parent::Control($left, $top, $width, $height);
		$this->Rows = new ArrayList();
		$this->Rows->ParentId = $this->Id;
	}
	function BuildTable($numRows, $numCols, $typeAsString, $params="")
	{
		$this->BuiltMatrix = array(array());
		for($i = 0; $i < $numRows; ++$i)
		{
			$this->Rows->Add(new TableRow());
			for($j = 0; $j < $numCols; ++$j)
			{
				eval('$this->Rows->Item[$i]->Columns->Add(new TableColumn(new '.$typeAsString.'(' . $params . ')));');
				$this->BuiltMatrix[$i][$j] = &$this->Rows->Item[$i]->Columns->Item[$j];
				// Added This Line To Make Default Control Width Equal to Column Width
				$this->BuiltMatrix[$i][$j]->Controls->Item[0]->SetWidth($this->BuiltMatrix[$i][$j]->GetWidth());
			}
		}
	}
	function Show()
	{
		$initialProperties = parent::Show();
		$id = $this->Id;
		$initialProperties .= ",'style.overflow','auto'";
		NolohInternal::Show("DIV", $initialProperties, $this);
		$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.borderCollapse','collapse','style.position','relative','style.width','{$this->Width}px','style.height','{$this->Height}px'";
//		$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.position','relative'";
		//$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.borderCollapse','collapse','style.position','relative','style.width','{$this->Width}px','style.height','{$this->Height}px'";
		//$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.position','relative','style.width','{$this->Width}px','style.height','{$this->Height}px'";
		NolohInternal::Show("TABLE", $initialProperties, $this, $id);
		$initialProperties = "'id','{$id}InnerTBody', 'style.position','relative'";
		NolohInternal::Show("TBODY", $initialProperties, $this, $id."InnerTable");
		if($this->ScrollLeft != null)
			AddScript("document.getElementById('$this->Id').scrollLeft = $this->ScrollLeft;");
		if($this->ScrollTop != null)
			AddScript("document.getElementById('$this->Id').scrollTop = $this->ScrollTop;");
	}
}
?>