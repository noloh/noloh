<?php
class CheckListView extends ListView
{
	function CheckListView($whatLeft, $whatTop, $whatWidth, $whatHeight)
	{
		parent::ListView($whatLeft, $whatTop, $whatWidth, $whatHeight);
	}
	public function AddItem(ListViewItem $whatListViewItem)
	{
		if($whatListViewItem != null)
		{
			$tempRow = new TableRow();
			$tempSubItemCount = $whatListViewItem->SubItems->Count();
			for($i = 0; $i<$this->Columns->Count(); $i++)
			{
				$tmpColumn = &new TableColumn(null, $this->Columns->Item[$i]->Width + $this->SpacerWidth);
				$this->ResizeImages->Item[$i]->Shifts[] = Shift::Width($tmpColumn);
				$this->ResizeImages->Item[$i]->Shifts[] = "Array(\"INNERCOLUMN{$tmpColumn->DistinctId}\",1,0,1,null,null,null,1)";
				//$this->ResizeImages->Item[$i]->Shifts[] = Shift::Width($tmpColumn);
				$tempRow->Columns->Add($tmpColumn);
			}
			for($i = 0; ($i < $whatListViewItem->SubItems->Count() && $i < $this->Columns->Count()); $i++)
			{
				$tmpItem = &$whatListViewItem->SubItems->Item[$i];
				if($tmpItem instanceof Label || $tmpItem instanceof Link)
				{
					if(/*$this->Checkable == true &&*/ $i == 0)
						$tempRow->Columns->Item[$i]->Control = new CheckBox($whatListViewItem->SubItems[$i]->Text, 0, 0, System::Auto);
					else
						$tempRow->Columns->Item[$i]->Control = $tmpItem;
				}
				else
				{
					if(/*$this->Checkable == true &&*/ $i == 0)
						$tempRow->Columns->Item[$i]->Control = new CheckBox($whatListViewItem->SubItems[$i], 0, 0, System::Auto);
					else
						$tempRow->Columns->Item[$i]->Control = new Label($whatListViewItem->SubItems[$i], 0, 0, System::Auto);
				}
			}
			$tmpDiff = $i;
			$whatListViewItem->ListView = $this;
			$this->DataTable->Rows->Add($tempRow);
			$this->ListViewItems->Add($whatListViewItem);
			$this->Relationships[$whatListViewItem->DistinctId] = array('TableIndex' => $this->DataTable->Rows->Count() - 1, 'ColumnCount' => $tmpDiff);
		}
	}
	function Update(ListViewItem $whatListViewItem = null)
	{
		if($whatListViewItem != null)
		{
			$tmpRelationship = &$this->Relationships[$whatListViewItem->DistinctId];
			$tmpRow = $this->DataTable->Rows->Item[$tmpRelationship['TableIndex']];
			$tmpSubItemCount = $whatListViewItem->SubItems->Count();
			if($tmpSubItemCount > $tmpRelationship['ColumnCount'] &&  $tmpRelationship['ColumnCount'] < $this->Columns->Count())
			{
				$tmpItem = $whatListViewItem->SubItems[$tmpRelationship['ColumnCount']];
				if($tmpItem instanceof Label || $tmpItem instanceof Link)
					$tmpRow->Columns->Item[$tmpRelationship['ColumnCount']]->Control = $tmpItem;
				elseif($tmpSubItemCount == 1)
					$tmpRow->Columns->Item[$tmpRelationship['ColumnCount']]->Control = new CheckBox($whatListViewItem->SubItems[$tmpRelationship['ColumnCount']],  0, 0, System::Auto);
				else
					$tmpRow->Columns->Item[$tmpRelationship['ColumnCount']]->Control = new Label($whatListViewItem->SubItems[$tmpRelationship['ColumnCount']],  0, 0, System::Auto);
				//$tmpRow->Columns->Item[$tmpSubItemCount - 1]->Control = new Label($whatListViewItem->SubItems[$tmpSubItemCount - 1],  0, 0, System::Auto);
				$tmpRelationship['ColumnCount']++;
			}
		}
	}
	function GetCheckedListViewItems()
	{
		$tmpCheckItems = array();
		$tmpCount = $this->ListViewItems->Count();
		for($i=0; $i < $tmpCount; $i++)
		{
			$tmpCheckBox = &$this->DataTable->Rows->Item[$i]->Columns->Item[0]->Controls->Item[0];
			if($tmpCheckBox->Checked == true)
				$tmpCheckItems[] = $this->ListViewItems[$i];
		}
		return $tmpCheckItems;
	}
}
?>