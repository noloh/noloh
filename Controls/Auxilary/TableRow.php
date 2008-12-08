<?php
/**
 * TableRow class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Auxiliary
 */
class TableRow extends Control
{
	public $Columns;
	public $Span;
	
	function TableRow()
	{
		parent::Control(0, 0, null, null);
		$this->Layout = Layout::Relative;
		$this->Columns = new ArrayList();
		$this->Columns->ParentId = $this->Id;
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		NolohInternal::Show('TR', parent::Show(), $this, $this->ParentId.'InnerTBody');
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		foreach($this->Columns as $column)
			$column->SearchEngineShow();
	}
}
?>