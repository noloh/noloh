<?php
/**
 * @internal
 */
class DataConstraint extends Object
{
	private $Columns;
	
	function DataConstraint($columns)
	{
		$this->SetColumns($columns);
	}
	function SetColumns($columns)
	{
		$this->Columns = $columns;
	}
	function GetColumns()	{return $this->Columns;}
}
?>