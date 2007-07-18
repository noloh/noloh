<?php
	
class ListViewItem extends Object //extends Component
{
	public $Checked;
	public $SubItems;
	private $ListViewId;
		
	function ListViewItem($objOrText = null)
	{
		//parent::Component();
		$this->SubItems = new ImplicitArrayList($this, "AddSubItem");
		if($objOrText != null)
			$this->AddSubItem($objOrText);
	}
	function SetListView($listView)	{$this->ListViewId = $listView->Id;}
	function AddSubItem($objOrText=null)
	{
		$this->SubItems->Add((is_string($objOrText) || $objOrText == null)?$objOrText = new Label($objOrText, 0, 0, System::Auto):$objOrText, true, true);
//		if(($tmpParent = $this->GetParent("ListView")) != null)
//			$tmpParent->Update($this);
		$objOrText->CSSClass = "NLVItem";
		if($this->ListViewId != null)
			GetComponentById($this->ListViewId)->Update($this);
//		$this->SubItems->Add(new Label("Testing", 0, 0, System::Auto), true, true);
//		if(($tmpParent = $this->GetParent("ListView")) != null)
//			$tmpParent->Update($this);
	}
}
		
?>