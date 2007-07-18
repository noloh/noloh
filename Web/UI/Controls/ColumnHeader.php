<?php
	
class ColumnHeader extends Label
{
	private $Order = 0;
	private $ListView;
	private $SizeHandle;
	
	function ColumnHeader($text=null, $left = System::Auto, $width = System::Auto, $height=20)
	{
		parent::Label($text, $left, 0, $width, $height);
		$this->SizeHandle = new Image(NOLOHConfig::GetNOLOHPath()."Web/UI/Controls/Images/Win/DataGridColumnDivider.gif", $this->Width - 5, 0);
		$this->SizeHandle->Cursor = Cursor::WestResize;
		$this->SizeHandle->ParentId = $this->Id;
		$this->Cursor = Cursor::Arrow;
		//$this->Font = "11px Tahoma";
		$this->CSSMargin = "3px";
		//$this->Click = new ServerEvent($this, "Sort");
	}
	public function GetSizeHandle()	{return $this->SizeHandle;}
	public function Sort()
	{
		$this->ListView->Sort($this, $this->Order);
		if($this->Order == 1)
			$this->Order = 0;
		else
			$this->Order++;
	}
	function SetListView(ListView $listView)
	{
		$this->ListView = $listView;
	}
	function GetListView(){return $this->ListView;}
}

?>