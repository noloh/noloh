<?php
/**
 * DataConstraint class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Data
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