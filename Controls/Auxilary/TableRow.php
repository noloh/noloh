<?php
/**
 * TableRow class
 *
 * A TableRow is a Row within a Table objet.
 * 
 * The following is an example of using a TableRow in conjunction with Table and TableColumn:
 * <pre>
 *     $table = new Table();
 *     $table->Rows->Add($row = new TableRow());
 *     $row->Columns->AddRange('Column 1', 'Column2');
 * </pre>
 * 
 * @package Controls/Auxiliary
 */
class TableRow extends Control
{
	/**
	 * An ArrayList holding the Columns of this TableRow
	 * @var ArrayList
	 */
	public $Columns;
	
	function TableRow($columns = null)
	{
		parent::Control(0, 0, null, null);
		$this->Layout = Layout::Relative;
		$this->Columns = new ImplicitArrayList($this, 'AddColumn');
		$this->Columns->ParentId = $this->Id;
		
		if(is_array($columns))
			foreach($columns as $column)
				$this->AddColumn($column);
	}
	/**
	 * @ignore
	 */
	public function AddColumn($column)
	{	
		if(!($column instanceof TableColumn))
			$column = &new TableColumn($column);
		
		$this->Columns->Add($column, true);
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
		$this->SearchEngineShowChildren();
	}
}
?>