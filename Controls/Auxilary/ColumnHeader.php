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
//	private $Order;
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
		//Shift Needs to be able to work with nulls before this can work
//		$this->Caption = new Label($text, 0, 0, null, null);
		$this->Caption = new Label($text, 0, 0, null, null);
//		$this->Caption->Layout = Layout::Relative;
		if($width == System::Auto || $width == System::AutoHtmlTrim)
		{
			$autoLabel = new Label($text, 0, 0, $width, $height);
			$width = $autoLabel->Width + 25;
		}
//		$this->Caption->SetSize(null, null);
//		$this->CSSPaddingLeft = '10px';
//		$this->CSSPaddingRight = '10px';
//		 ?$this->Caption->GetWidth() + 25:$width
		parent::Panel($left, 0, $width, $height);
		$this->CSSBackgroundRepeat = "repeat-x";
		//SizeHandle
		$this->SizeHandle = new Image(System::ImagePath() . 'Std/ColSep.gif', 0, 6);
		$this->SizeHandle->ReflectAxis('x');
		$this->SizeHandle->Shifts[] = Shift::Width($this);
		
		$this->Caption->CSSClass = 'NColHead';
		$this->Caption->SetLayout(Layout::Relative);
		$this->SizeHandle->Cursor = Cursor::WestResize;
		$this->Caption->ParentId = $this->Id;
		$this->SizeHandle->ParentId = $this->Id;
//		$this->Controls->AddRange($this->Caption, $this->SizeHandle);
		$this->Cursor = Cursor::Arrow;
		$this->MouseOver = new ClientEvent("this.style.background = 'url(". System::ImagePath() . 'Std/HeadOrange.gif)' . '\';');
		$this->MouseOut = new ClientEvent("this.style.background = '';");
		//$this->Click = new ClientEvent("if(_N.LVMouseUp!=null) {_N.LVMouseUp=null;return;} this.parentNode.parentNode.style.cursor = 'wait';");
//		$this->Click[] = new ServerEvent($this, 'Sort');
		$this->Click = new ServerEvent($this, 'SetSelected', true);
		$this->Select = new ServerEvent($this, 'Toggle');
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
	 * @return Image The object that sorts the column
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
	/**
	 * Returns the object used to resize the Columns
	 * @return Image
	 */
	public function GetSizeHandle()	{return $this->SizeHandle;}
	/**
	 * Sorts the contents of the attached ListView by this Column.
	 */
	public function Sort()	 {$this->GetListView()->Sort($this);}
	/**
	* @ignore
	*/
	function Toggle()
	{
		$order = ListView::$Ordered;
//		System::Log($order);
		$arrowPath = System::ImagePath() . 'Std/' . (($order)?'ArrDwn.gif':'ArrUp.gif');
		if($this->OrderArrow == null)
		{
			$this->OrderArrow = new Image($arrowPath, 8, 10);
			$this->OrderArrow->ReflectAxis('x');
			$this->OrderArrow->ParentId = $this->Id;
			$this->Deselect = new ServerEvent($this->OrderArrow, 'SetVisible', false);
		}
		else
			$this->OrderArrow->SetSrc($arrowPath);
		$this->OrderArrow->Visible = true;
	}
	/**
	 * Associates this ColumnHeader with a ListView
	 * @param string|ListView $listView The ID or object of the ListView
	 * you wish to associate this ColumnHeader with.
	 */
	function SetListView($listView)
	{
		$this->ListViewId = $listView InstanceOf ListView?$listView->GetId():$listView;
	}
	/**
	 * Returns the ListView which this ColumnHeader is associated with.
	 * @return ListView
	 */
	function GetListView()	{return GetComponentById($this->ListViewId);}
	function __destruct()
	{
		unset($this->Caption);
		unset($this->SizeHandle);
		return parent::__destruct();
	}
}
?>