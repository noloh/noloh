<?php
/**
 * @package Controls/Extended
 */
/**
 * CheckListView class
 * 
 * This class needs a description...
 */
class CheckListView extends ListView
{
	private $CheckColumn;
	
	function CheckListView($left, $top, $width, $height)
	{
		parent::ListView($left, $top, $width, $height);
		$this->CheckColumn = new ColumnHeader(null, 0, 25, $this->ColumnsPanel->GetHeight());
		$this->CheckColumn->Click = null;
		$this->CheckColumn->ParentId = $this->ColumnsPanel->Id;
	}
	public function AddListViewItem(ListViewItem $listViewItem)
	{
		parent::AddListViewItem($listViewItem);
		$listViewItem->SubItems->PositionalInsert(new CheckBox(null, 0, 0, 25), 'Check', 0);
	}
	public function InsertListViewItem(ListViewItem $listViewItem, $idx)
	{
		parent::InsertListViewItem($listViewItem, $idx);
		$listViewItem->SubItems->PositionalInsert($tmpCheck = new CheckBox(null, 0, 0, 25), 'Check', 0);
//		$tmpCheck->Click[] = new ClientEvent('event.cancelBubble=true;event.stopPropagation();');
		$tmpCheck->Click[] = new ClientEvent(UserAgentDetect::IsIE()?'window.event.cancelBubble=true;':'event.cancelBubble=true;event.stopPropagation();');
	}
	function AddColumn($text, $width = System::Auto)
	{
		$tmpCount = $this->Columns->Count();
		$tmpRight = ($tmpCount > 0)?$this->Columns[$tmpCount-1]->GetRight():$this->CheckColumn->GetRight();
		if(is_string($text))
			$this->Columns->Add($tmpColumn = &new ColumnHeader($text, $tmpRight, $width, $this->ColumnsPanel->GetHeight()), true, true);
		elseif($text instanceof ColumnHeader)
		{
			$this->Columns->Add($tmpColumn = &$text, true, true);
			if($text->GetLeft() == System::Auto)
				$text->SetLeft($tmpRight);
		}
		if(($tmpRight = $tmpColumn->GetRight()) > $this->GetWidth())
			$this->InnerPanel->SetWidth($tmpRight);
		$this->MakeColumnShift($tmpColumn);
		$tmpColumn->SetListView($this->Id);
		$this->ColumnsPanel->BringToFront();
		$tmpColumn->SizeHandle->MouseDown[] = new ClientEvent("_N_LV_ResizeStart('{$this->Line->Id}', '$tmpColumn->Id', '{$this->InnerPanel->Id}');");
		$this->Line->Shifts[] = Shift::With($tmpColumn->SizeHandle, Shift::Left);
		/*foreach($this->LVItemsQueue as $key => $tmpListViewItem)
			if($this->Update($tmpListViewItem))
				unset($this->LVItemsQueue[$key]);*/
	}
	function GetCheckedListViewItems()
	{
		$tmpCheckItems = array();
		foreach($this->ListViewItems as $listViewItem)
		{
			$tmpCheckBox = &$listViewItem->SubItems['Check'];
			if($tmpCheckBox->Checked)
				$tmpCheckItems[] = $listViewItem;
		}
		return $tmpCheckItems;
	}
}
?>