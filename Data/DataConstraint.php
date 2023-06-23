<?php
/**
 * @ignore
 */
class DataConstraint extends Base
{
	private $Columns;
	
	function __construct($columns)
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