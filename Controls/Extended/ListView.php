<?php
/**
 * ListView class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Extended
 */
class ListView extends Panel
{
	const Ascending = true, Descending = false;
	public $ListViewItems;
	public $Columns;
	protected $ColumnsPanel;
	protected $LVItemsQueue = array();
	protected $InnerPanel;
	protected $Line;
	private $BodyPanelsHolder;
	
	function GetColumnPanel(){return $this->ColumnsPanel;}
	function ListView($left, $top, $width, $height)
	{
		parent::Panel($left, $top, $width, $height)/*, $this)*/;
		$this->ColumnsPanel = new Panel(0, 0, $width, 28, $this);
		$this->ColumnsPanel->CSSBackground_Image = "url(". NOLOHConfig::GetNOLOHPath() . "Images/Std/HeadBlue.gif)";
		$this->ColumnsPanel->CSSBackground_Repeat = "repeat-x";
		$this->ColumnsPanel->Controls->AddFunctionName = "AddColumn";
		$this->Columns = &$this->ColumnsPanel->Controls;
		$this->BodyPanelsHolder = new Panel(0, $this->ColumnsPanel->Bottom, $width, $height - $this->ColumnsPanel->Height);
		$this->BodyPanelsHolder->Scrolling = System::Auto;
		$this->InnerPanel = new Panel(0, 0, null, 'auto', $this);
		$this->InnerPanel->Layout = Layout::Web;
		$this->BodyPanelsHolder->Scrolling = System::Auto;
		$this->ListViewItems = &$this->InnerPanel->Controls;
		$this->ListViewItems->AddFunctionName = 'AddListViewItem';
		$this->ListViewItems->ClearFunctionName = 'ClearListViewItems';
		$this->ListViewItems->InsertFunctionName = 'InsertListViewItem';
		$this->BodyPanelsHolder->Controls->Add($this->InnerPanel);
		$this->Line = new Label('', 0, 0, 3, '100%');
		$this->Line->Visible = false;
		$this->Line->BackColor = '#808080';
		$this->Line->ParentId = $this->Id;
		$this->Controls->AddRange($this->ColumnsPanel, /*$this->InnerPanel*/ $this->BodyPanelsHolder);
		$this->ModifyScroll();
	}
	//function GetColumns(){return $this->Columns;}
	function AddColumn($text, $width = System::Auto)
	{
		$tmpCount = $this->Columns->Count();
		if(is_string($text))
			$this->Columns->Add($tmpColumn = &new ColumnHeader($text, ($tmpCount > 0)?$this->Columns[$tmpCount-1]->GetRight():0, $width, $this->ColumnsPanel->GetHeight()), true, true);
		elseif($text instanceof ColumnHeader)
		{
			$this->Columns->Add($tmpColumn = &$text, true, true);
			if($text->GetLeft() == System::Auto)
				$text->SetLeft($tmpCount > 0?$this->Columns[$tmpCount-1]->GetRight():0);
		}
		if(($tmpRight = $tmpColumn->GetRight()) > $this->GetWidth())
			$this->InnerPanel->SetWidth($tmpRight);

		$this->MakeColumnShift($tmpColumn);
		$tmpColumn->SetListView($this->Id);
		$this->ColumnsPanel->BringToFront();
		$tmpColumn->SizeHandle->MouseDown[] = new ClientEvent("_N_LV_ResizeStart('{$this->Line->Id}', '$tmpColumn->Id', '{$this->InnerPanel->Id}');");
		$this->Line->Shifts[] = Shift::With($tmpColumn->SizeHandle, Shift::Left);
		
		foreach($this->LVItemsQueue as $key => $tmpListViewItem)
			if($this->Update($tmpListViewItem))
				unset($this->LVItemsQueue[$key]);
	}
	protected function MakeColumnShift($tmpColumn)
	{
		if(($tmpCount = $this->Columns->Count) > 1)
			$tmpColumn->Shifts[] = Shift::With($this->Columns[$tmpCount - 2], Shift::Left);//, Shift::Mirror, 1, null, -1);
		$tmpColumn->SizeHandle->Shifts[] = Shift::Left($tmpColumn->SizeHandle);
		$tmpColumn->SizeHandle->Shifts[] = Shift::Width($tmpColumn);
	}
	function AddListViewItem(ListViewItem $listViewItem)
	{
		$this->ListViewItems->Add($listViewItem, true, true);
		$this->SetItemProperties($listViewItem);
		/*
		if($tmpSubItemCount > $tmpColCount)
			if(empty($this->LVItemsQueue["{$listViewItem->Id}"])) 
				$this->LVItemsQueue["{$listViewItem->Id}"] = $listViewItem;*/
	}
	function InsertListViewItem($listViewItem, $idx)
	{
		$this->ListViewItems->Insert($listViewItem, $idx, true);
		$this->SetItemProperties($listViewItem);
	}
	function Update(ListViewItem $listViewItem=null, $startColumn=null/*, $addToQueue=true*/)
	{
		return true;
		/*//Need to change this function to allow for more optimized adding of subcolumns,
		//Currently it iterates throught all subcolumns, but should only iterate through NEW subcolumns
		$tmpColCount = $this->Columns->Count();
		$tmpIndex = $this->ListViewItems->IndexOf($listViewItem);
		if($tmpIndex != -1)//null))
		{
			$tmpSubItemCount = $listViewItem->SubItems->Count();
			for($i=0; ($i<$tmpSubItemCount && $i < $tmpColCount); ++$i)
			{
				$this->BodyPanels[$i]->Controls[$tmpIndex] = $listViewItem->SubItems[$i];
				$this->BodyPanels[$i]->Controls[$tmpIndex]->SetLeft(0);
				}
			}
			if($tmpSubItemCount <= $tmpColCount)
				return true;
		}
		return false;*/
	}
	//Function will Consolidate adding parts of Update() and AddListViewItem()
	private function SetItemProperties(ListViewItem $listViewItem)
	{
		$tmpSubItemCount = $listViewItem->SubItems->Count();
		$tmpColCount = $this->Columns->Count();
		$listViewItem->SetListView($this);
		if($tmpColCount > 0 && $tmpSubItemCount > 0)
		{
			for($i=0;$i<$tmpSubItemCount && $i < $tmpColCount;++$i)
			{
				if($listViewItem->SubItems->Elements[$i] !== null)
				{
					$listViewItem->SubItems->Elements[$i]->SetLeft($this->Columns->Elements[$i]->GetLeft());
					$listViewItem->SubItems->Elements[$i]->SetWidth($this->Columns->Elements[$i]->GetWidth());
				}
			}
		}
	}
	private function ModifyScroll()
	{
		$this->BodyPanelsHolder->Scroll = new ClientEvent("_N_LV_ModScroll('{$this->BodyPanelsHolder->Id}', '{$this->ColumnPanel->Id}');");
	}
	public function ClearListViewItems(/*$clearBodyPanels=true*/)
	{
		$this->ListViewItems->Clear(true);
		$this->LVItemsQueue = array();
	}
	public function Clear()
	{
		$this->ClearListViewItems(false);
		$this->Columns->Clear();
	}
	function GetDataBind()
	{
		$this->SetCursor($this->BodyPanelsHolder->GetCursor());
		return $this->GetEvent('DataBind');
	}	
	function SetDataBind($newEvent)
	{
		$this->SetEvent($newEvent, 'DataBind');
	}
	function Show()
	{
		AddNolohScriptSrc('ListView.js', true);
		parent::Show();
	}
	public function Sort($column, $order=true)
	{
		if($column instanceof ColumnHeader)
			$tmpIndex = $this->Columns->IndexOf($column);
		elseif(is_int($column))
			$tmpIndex = $column;
		else return;
		$tmpCount = $this->Columns->Count;
		for($i=0; $i < $tmpCount; ++$i)
		{
			if($this->Columns[$i]->OrderArrow != null)
				$this->Columns[$i]->OrderArrow->SetVisible(false);
		}
		$tmpArray = array();
		$tmpCount = $this->ListViewItems->Count();
		foreach($this->ListViewItems->Elements as $key => $listViewItem)
			$tmpArray[$key] = $listViewItem->SubItems[$tmpIndex]->GetText();	
		if(!$order)
			asort($tmpArray);
		else
			arsort($tmpArray);
		
		$tmpNewArray = array();
		$clientArray = 'Array(';
		foreach($tmpArray as $key => $val)
		{
			$tmpNewArray[$key] = &$this->ListViewItems->Elements[$key];
			$clientArray .= '\'' . $tmpNewArray[$key]->Id .'\',';
		}
		$clientArray = rtrim($clientArray, ',') . ')';
		QueueClientFunction($this, '_N_LV_Sort', array('"'.$this->InnerPanel->Id.'",'.$clientArray));
	}
}
?>