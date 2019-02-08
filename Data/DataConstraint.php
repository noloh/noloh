<?php
/**
 * @ignore
 */
class DataConstraint extends Base
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