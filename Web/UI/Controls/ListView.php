<?php
class ListView extends Panel
{
	public $ListViewItems;
	public $Columns;
	private $ColumnsPanel;
	private $LVItemsQueue = array();
	private $BodyPanelsHolder;
	protected $BodyPanels;
	
	function GetColumnPanel(){return $this->ColumnsPanel;}
	function ListView($left, $top, $width, $height)
	{
		parent::Panel($left, $top, $width, $height)/*, $this)*/;
		$this->ColumnsPanel = new Panel(0, 0, $width, 20, $this);
		$this->ColumnsPanel->CSSBackground_Image = "url(". NOLOHConfig::GetNOLOHPath() . "Web/UI/Controls/Images/Win/DataGridColumnHeaderBack.gif)";
		$this->ColumnsPanel->Controls->AddFunctionName = "AddColumn";
		$this->Columns = &$this->ColumnsPanel->Controls;
		$this->ListViewItems = new ImplicitArrayList($this, "AddListViewItem");
		$this->ListViewItems->ParentId = $this->DistinctId;
		$this->BodyPanelsHolder = new Panel(0, $this->ColumnsPanel->Bottom, $width, $height - $this->ColumnsPanel->Height);
		$this->BodyPanels = &$this->BodyPanelsHolder->Controls;
		$this->BodyPanelsHolder->Scrolling = System::Auto;
		$this->Controls->AddRange($this->ColumnsPanel, $this->BodyPanelsHolder);
		$this->ModifyScroll();
		/*$this->ColumnPanel = new Panel(0, 0, $this->Width, 22);
		$this->ColumnPanel->CSSBackground_Image = "url(". NOLOHConfig::GetNOLOHPath() . "Web/UI/Controls/Images/windows/DataGridColumnHeaderBack.gif)";
		$this->CSSBorder = "1px solid #716f64";*/
	}
	//function GetColumns(){return $this->Columns;}
	function AddColumn($text, $width = System::Auto)
	{
		$tmpCount = $this->Columns->Count();
		if(is_string($text))
			$this->Columns->Add($tmpColumn = &new ColumnHeader($text, ($tmpCount > 0)?$this->Columns[$tmpCount-1]->Right:0, $width), true, true);
		elseif($text instanceof ColumnHeader)
		{
			$this->Columns->Add($tmpColumn =& $text);
			if($text->Left = System::Auto)
				$text->Left = $tmpCount > 0?$this->Columns[$tmpCount-1]->Right:0;
		}
//		if($tmpColumn->Right < $this->Width)
//		{
//			$tmpLeftOver = $this->Width - $tmpColumn->Right;
//			$tmpSubLeftOver = round($tmpLeftOver/($tmpCount + 1));
//			for($i=0;$i<$tmpCount + 1; $i++)
//			{
//				$this->Columns[$i]->Width += $tmpSubLeftOver;
//				if($i>0)
//					$this->Columns[$i]->Left = $this->Columns[$i-1]->Right;
//			}
//		}
		$this->BodyPanels->Add($tmpPanel = new Panel($tmpColumn->Left, 0, $tmpColumn->Width, 0));
		
		//$tmpColumn->SizeHandle->Shifts[] = Shift::Width($tmpPanel);
		$tmpColumn->Shifts[] = Shift::Width($tmpPanel);
		//$tmpColumn->SizeHandle->Shifts[] = Shift::Left($tmpColumn->SizeHandle);
		$tmpColumn->Shifts[] = Shift::Left($tmpColumn->SizeHandle);
		//$tmpColumn->SizeHandle->Shifts[] = Shift::Width($tmpColumn);
		$tmpColumn->Shifts[] = Shift::Width($tmpColumn);
		$tmpColumn->Shifts[] = Shift::Width($this->ColumnsPanel);
		if($tmpCount > 0)
		{
			$tmpColumn->Shifts[] = Shift::With($this->Columns[$tmpCount -1], Shift::Left);
			$tmpPanel->Shifts[] = Shift::With($this->BodyPanels[$tmpCount -1], Shift::Left);
		}
		$this->ColumnsPanel->BringToFront();
		/*foreach($this->LVItemsQueue as $key => $tmpListViewItem)
			if($this->Update($tmpListViewItem))
				unset($this->LVItemsQueue[$key]);*/
	}
	function AddListViewItem(ListViewItem $listViewItem)
	{
		$tmpSubItemCount = $listViewItem->SubItems->Count;
		$tmpColCount = $this->Columns->Count;
		$this->ListViewItems->Add($listViewItem, true, true);
		$listViewItem->SetListView($this);
		for($i=0;$i<$tmpSubItemCount && $i < $tmpColCount;$i++)
		{
			if($listViewItem->SubItems->Item[$i] !== null)
			{
				$tmpBodyControls = &$this->BodyPanels[$i]->Controls;
				if($i == 0)
				{
					$listViewItem->SubItems->Item[$i]->Top = $tmpTop = ((($tmpBodyCount = $tmpBodyControls->Count) > 0)?$tmpBodyControls[$tmpBodyCount-1]->Bottom:0);
					$this->BodyPanels[$i]->Height += $listViewItem->SubItems[$i]->Height;
				}
				else
				{
					$listViewItem->SubItems->Item[$i]->Top = $tmpTop;
					$this->BodyPanels[$i]->Height = $this->BodyPanels[0]->Height;
				}
//				//$listViewItem->SubItems->Item[$i]->Top = (($tmpBodyCount) > 0)?$tmpBodyControls[$tmpBodyCount -1]->Bottom:0;
//					if(!isset($listViewItem->SubItems[$i]->Height))
//						print_r($listViewItem->SubItems[$i]);
//				Alert($listViewItem->SubItems[$i]->Text);
				$tmpBodyControls->Add($listViewItem->SubItems[$i]);
				//print(get_class($listViewItem->SubItems[$i]));//->Height);
				//$this->BodyPanels[$i]->Height += $listViewItem->SubItems[$i]->Height;
				$listViewItem->SubItems->Item[$i]->Left = 0;
				//$listViewItem->SubItems->Item[$i]->Width = System::Auto;//$this->Columns[$i]->Width;
			}
			//}
		}
		/*
		if($tmpSubItemCount > $tmpColCount)
			if(empty($this->LVItemsQueue["{$listViewItem->DistinctId}"])) 
				$this->LVItemsQueue["{$listViewItem->DistinctId}"] = $listViewItem;*/
	}
//	function AddListViewItem(ListViewItem $listViewItem)
//	{
//		$tmpSubItemCount = $listViewItem->SubItems->Count;
//		$tmpColCount = $this->Columns->Count;
//		$this->ListViewItems->Add($listViewItem, true, true);
//		for($i=0;$i<$tmpSubItemCount && $i < $tmpColCount;$i++)
//		{
////			if($this->BodyPanels[$i] != null)
////			{
//				$tmpBodyControls = $this->BodyPanels[$i]->Controls;
//				$listViewItem->SubItems[$i]->Top = (($tmpBodyCount = $tmpBodyControls->Count) > 0)?$tmpBodyControls[$tmpBodyCount -1]->Bottom:0;
//				$tmpBodyControls->Add($listViewItem->SubItems[$i]);
//				$this->BodyPanels[$i]->Height += $listViewItem->SubItems[$i]->Height;
//				$listViewItem->SubItems[$i]->Left = 0;
////			}
//		}
//		if($tmpSubItemCount > $tmpColCount)
//			if(empty($this->LVItemsQueue["{$listViewItem->DistinctId}"])) 
//				$this->LVItemsQueue["{$listViewItem->DistinctId}"] = $listViewItem;
//	}
	function Update($listViewItem=null/*, $addToQueue=true*/)
	{
		//Need to change this function to allow for more optimized adding of subcolumns,
		//Currently it iterates throught all subcolumns, but should only iterate through NEW subcolumns
		$tmpColCount = $this->Columns->Count();
		$tmpIndex = $this->ListViewItems->IndexOf($listViewItem);
		if($tmpIndex != -1)//null))
		{
			$tmpSubItemCount = $listViewItem->SubItems->Count();
			for($i=0; ($i<$tmpSubItemCount && $i < $tmpColCount); ++$i)
			{
//				if($this->BodyPanels[$i]->Controls->GetCount() <= $tmpIndex)
//				{
				if(!isset($this->BodyPanels[$i]->Controls[$tmpIndex]))
				{
					$this->BodyPanels[$i]->Controls[$tmpIndex] = $listViewItem->SubItems[$i];
					$this->BodyPanels[$i]->Height += $listViewItem->SubItems[$i]->Height;
					$this->BodyPanels[$i]->Controls[$tmpIndex]->Left = 0;
				}
//				}
			}
			if($tmpSubItemCount <= $tmpColCount)
				return true;/*
			else
				if(empty($this->LVItemsQueue["{$listViewItem->DistinctId}"]))
					$this->LVItemsQueue["{$listViewItem->DistinctId}"] = $listViewItem;*/
		}
		return false;
	}
	//Function will Consolidate adding parts of Update() and AddListViewItem()
	private function AddListViewItemToBodyPanel($bodyPanel, $listViewItem)
	{
		
	}
	private function ModifyScroll()
	{
		$this->BodyPanelsHolder->Scroll = new ClientEvent("Noloh_UI_ListView_ScrollColumnPanel('{$this->BodyPanelsHolder->DistinctId}', '{$this->ColumnPanel->DistinctId}')");
	}
	public function Clear()
	{
		$this->ListViewItems->Clear();
		$bodyPanelsCount = $this->BodyPanels->Count;
		for($i=0; $i<$bodyPanelsCount; $i++)
			$this->BodyPanels[$i]->Controls->Clear();
		$this->BodyPanels->Clear();
		$this->Columns->Clear();
		$this->LVItemsQueue = array();
	}
	function Show()
	{
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/ListView.js");
		parent::Show();
	}
}
	
//	public function Sort($whatColumn, $whatOrder)
//	{
//		if($whatColumn instanceof ColumnHeader)
//			$tempIndex = $this->Columns->IndexOf($whatColumn);
//		else if(is_int($whatColumn))
//			$tempIndex = $whatColumn;
//		else return;
//		$tempArray = array();
//		$tempRows = array();
//		$tmpCount = $this->DataTable->Rows->Count();
//		for($i=0; $i < $tmpCount; $i++)
//		{
//			$tempArray[] = $this->DataTable->Rows->Item[$i]->Columns->Item[$tempIndex]->Control->Text;
//			$tempRows[] = $this->DataTable->Rows->Item[$i];
//		}
//		if($whatOrder == 0)
//			asort($tempArray);
//		else
//			arsort($tempArray);
//		$tempArrayKeys = array_keys($tempArray);
//		$tmpCount = count($tempArrayKeys);
//		for($i=0; $i< $tmpCount; $i++)
//			$this->DataTable->Rows[$i] = $tempRows[$tempArrayKeys[$i]];
//	}
//	public function ClearListView()
//	{
//		$this->Items->Clear();
//		$this->DataTable->Rows->Clear();
//		$this->ResizeImages->Clear();
//		/*for($i = 0; $i < $this->Columns->Count(); $i++)
//		{
//			//$this->ResizeImages[$i]->Shifts[] = Shift::Left($this->ResizeImages[$i]);
//			$this->ResizeImages[$i]->Shifts[] = Shift::Width($this->Columns[$i]);
//			for($j=0; $j < $this->ResizeImages->Count(); $i++)
//			{
//				if($j != $i)
//					$this->ResizeImages->Item[$j]->Shifts[] = Shift::Left($this->Columns[$j]);
//				$this->ResizeImages->Item[$j]->Shifts[] = Shift::Left($this->ResizeImages[$j]);
//			}
//		}*/
//		$this->ListViewItems->Clear();
//		$this->ColumnPanel->Controls->Clear();
//		$this->Columns->Clear();
//	}
//	public function Show($IndentLevel = 0)
//	{
//		$parentShow = parent::Show($IndentLevel);
//		if($parentShow == false)
//			return false;
//		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/DataGrid.js");
//		return $parentShow;
//	}
//}
?>