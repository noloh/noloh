<?php
/**
 * ColumnHeader class
 *
 * ColumnHeaders are used in conjuction with ListViews.  When you add columns to a ListView, 
 * you're actually adding ColumnHeader objects to the column bar of ListView. Usually
 * manual instantiation is unnecessary since ListView's Columns->Add function will instantiate
 * a ColumnHeader for you.
 * 
 * However, you can still instantiate and add your own ColumnHeaders to ListView.
 * <pre>
 * $people = new ListView();
 * $people->Columns->Add(new ColumnHeader('Column 1'));
 * </pre>
 * 
 * Please note that the above is equivalent to:
 * <pre>
 * $people = new ListView();
 * $people->Columns->Add('Column 1');
 * </pre>
 * 
 * @package Controls/Auxiliary
 */
class ColumnHeader extends Panel
{
	/*
	 */
	const Ascending = 0, Descending = 1;
	private $Order;
	private $ListViewId;
	private $SizeHandle;
	private $OrderArrow;
	private $Caption;
	
	/**
	 * Constructor
	 * @param string $text Text of the column
	 * @param integer $left Left of the column
	 * @param integer $width Width of the column
	 * @param integer $height Height of the column
	 */
	function ColumnHeader($text=null, $left = System::Auto, $width = System::Auto, $height=20)
	{
		$this->Caption = new Label($text, 0, 0, $width, $height);
		parent::Panel($left, 0, ($width == System::Auto || $width == System::AutoHtmlTrim)?$this->Caption->GetWidth() + 25:$width, $height);
		$this->CSSBackground_Repeat = "repeat-x";
		$this->SizeHandle = new Image(System::ImagePath() . 'Std/ColSep.gif', $this->Width - 3, 6);
		$this->Caption->CSSClass = 'NColHead';
		$this->SizeHandle->Cursor = Cursor::WestResize;
		$this->Caption->ParentId = $this->Id;
		$this->SizeHandle->ParentId = $this->Id;
		$this->Cursor = Cursor::Arrow;
		$this->MouseOver = new ClientEvent("this.style.background = 'url(". System::ImagePath() . 'Std/HeadOrange.gif)' . '\';');
		$this->MouseOut = new ClientEvent("this.style.background = '';");
		//$this->Click = new ClientEvent("if(_N.LVMouseUp!=null) {_N.LVMouseUp=null;return;} this.parentNode.parentNode.style.cursor = 'wait';");
		$this->Click[] = new ServerEvent($this, 'Sort');
        //$this->SizeHandle->MouseUp = new ClientEvent('_N.LVMouseUp=true;');
	}
	/**
	 * Returns the Label object that displays the Text of the column.
	 * @return Label The Caption Label of the ColumnHeader
	 */
	function GetCaption()	{return $this->Caption;}
	/**
	 * Returns the object that orders the columns. The default OrderArrow
	 * is an Image object.
	 * @return Object The object that sorts the column
	 */
	function GetOrderArrow()	{return $this->OrderArrow;}
	/**
	 * Sets the text of the column
	 * @param string $text The text to diplay in the the column
	 */
	function SetText($text)	{$this->Caption->SetText($text);}
	/**
	 * Returns the text of the column
	 * @return string The text to diplay in the the column
	 */
	function GetText()		{return $this->Caption->GetText();}
	public function GetSizeHandle()	{return $this->SizeHandle;}
	/**
	 * Sorts the contents of the attached ListView by this Column.
	 */
	public function Sort()
	{
		$listView = $this->GetListView();
		$listView->Sort($this, $this->Order);
		$arrowPath = System::ImagePath() . 'Std/' . (($this->Order)?'ArrDwn.gif':'ArrUp.gif');
		if($this->OrderArrow == null)
		{
			$this->OrderArrow = new Image($arrowPath, $this->GetWidth() - 17, 12);
			$this->OrderArrow->ParentId = $this->Id;
			$this->SizeHandle->Shifts[] = Shift::Left($this->OrderArrow);
//			$this->SizeHandle->Shifts[] = Shift::Width($this);
//			$this->OrderArrow->Shifts[] = Shift::With($this, Shift::Left);
		}
		else
			$this->OrderArrow->SetSrc($arrowPath);
		$this->OrderArrow->Visible = true;
		$listView->SetCursor($listView->Cursor);//$this->Cursor;
		$this->Order = !$this->Order;
	}
	/**
	 * Associates this ColumnHeader with a ListView
	 * @oaram mixed string|ListView $listViewId The ID or object of the ListView
	 * you wish to associate this ColumnHeader with.
	 */
	function SetListView($listViewId)
	{
		if($listViewId InstanceOf ListView)
			$this->ListViewId = $listViewId->Id;
		else
			$this->ListViewId = $listViewId;
	}
	/**
	 * Returns the ListView which this ColumnHeader is associated with.
	 * @return ListView
	 */
	function GetListView()	{return GetComponentById($this->ListViewId);}
}

?>