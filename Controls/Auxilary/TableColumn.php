<?php
/**
 * TableColumn class
 *
 * A TableColumn is a Column within a TableRow of a Table.
 * 
 * The following is an example of using a TableColumn in conjunction with TableRow and Table
 * <pre>
 *     $table = new Table();
 *     $table->Rows->Add($row = new TableRow());
 *     $row->Columns->AddRange('Column 1', 'Column2');
 * </pre>
 * 
 * @package Controls/Auxiliary
 */
class TableColumn extends Panel
{
	/**
	 * Constructor of TableColumn
	 * @param mixed $object Optional text or an object that will reside in this Column.
	 * @param mixed $width The Width of the Column,
	 * @param mixed $height The Height of the Column
	 */
	function TableColumn($object=null, $width=null, $height=null)
	{
		Panel::Panel(null, null, $width, $height, $this);
		$this->Controls->AddFunctionName = "AddControl";
		$this->Layout = Layout::Relative;
		$this->SetControl($object);
	}
	/**
	 * @ignore
	 */
	function AddControl($control)
	{
		$this->Controls->Add($control, true);
		$control->Layout = Layout::Relative;
	}
	/**
	 * Returns the first Control of the Column
	 * @return Control
	 */
	public function GetControl()
	{
		if($this->Controls->Count() > 0)
			return $this->Controls->Elements[0];
		return null;
	}
	/**
	 * Assigns the first Control of the column.
	 * @param mixed $object Either text or an Object that you want to be the base
	 * Control of the column. If text is passed in a Label will be automatically
	 * created for you.
	 * @return Control
	 */
	public function SetControl($object=null)
	{
		if($object != null)
		{
			if(is_string($object))
				$object = new Label($object, 0, 0, null, null);
				
			if($this->Controls->Count() > 0)
				$this->Controls->Elements[0] = $object;
			else
				$this->Controls->Add($object);
		}
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