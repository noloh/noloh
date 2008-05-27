<?php
/**
 * @package Web.UI.Controls
 */	

/**
 * ColumnHeader class
 *
 * This class needs a description...
 */
class ColumnHeader extends Panel
{
	const Ascending = 0, Descending = 1;
	private $Order;
	private $ListViewId;
	private $SizeHandle;
	private $OrderArrow;
	private $Caption;
	
	function ColumnHeader($text=null, $left = System::Auto, $width = System::Auto, $height=20)
	{
		$this->Caption = new Label($text, 0, 0, $width, $height);
		parent::Panel($left, 0, ($width == System::Auto || $width == System::AutoHtmlTrim)?$this->Caption->GetWidth() + 25:$width, $height);
		$this->CSSBackground_Repeat = "repeat-x";
		$this->SizeHandle = new Image(NOLOHConfig::GetNOLOHPath().'Images/Std/ColSep.gif', $this->Width - 3, 6);
		$this->Caption->CSSClass = 'NColHead';
		$this->SizeHandle->Cursor = Cursor::WestResize;
		$this->Caption->ParentId = $this->Id;
		$this->SizeHandle->ParentId = $this->Id;
		$this->Cursor = Cursor::Arrow;
		$this->MouseOver = new ClientEvent("this.style.background = 'url(". NOLOHConfig::GetNOLOHPath() . 'Images/Std/HeadOrange.gif)' . '\';');
		$this->MouseOut = new ClientEvent("this.style.background = '';");
		$this->Click = new ClientEvent("if(tmpMouseUp!=null) {tmpMouseUp=null;return;} this.parentNode.parentNode.style.cursor = 'wait';");
		$this->Click[] = new ServerEvent($this, 'Sort');
        $this->SizeHandle->MouseUp = new ClientEvent('tmpMouseUp=true;');
	}
	function GetCaption()	{return $this->Caption;}
	function GetOrderArrow()	{return $this->OrderArrow;}
	function SetText($text)	{$this->Caption->SetText($text);}
	function GetText()		{return $this->Caption->GetText();}
	public function GetSizeHandle()	{return $this->SizeHandle;}
	public function Sort()
	{
		$tmpListView = $this->GetListView();
		$tmpListView->Sort($this, $this->Order);
		$tmpSrc = NOLOHConfig::GetNOLOHPath().'Images/Std/' . (($this->Order)?'ArrDwn.gif':'ArrUp.gif');
		if($this->OrderArrow == null)
		{
			$this->OrderArrow = new Image($tmpSrc, $this->GetWidth() - 17, 12);
			$this->OrderArrow->ParentId = $this->Id;
			$this->SizeHandle->Shifts[] = Shift::Left($this->OrderArrow);
//			$this->OrderArrow->Shifts[] = Shift::With($this, Shift::Left);
		}
		else
			$this->OrderArrow->SetSrc($tmpSrc);
		$this->OrderArrow->Visible = true;
		$tmpListView->SetCursor($tmpListView->Cursor);//$this->Cursor;
		$this->Order = !$this->Order;
	}
	function SetListView($listViewId){$this->ListViewId = $listViewId;}
	function GetListView()	{return GetComponentById($this->ListViewId);}
}

?>