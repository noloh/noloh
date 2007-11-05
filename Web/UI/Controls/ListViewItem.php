<?php	
/**
 * @package Web.UI.Controls
 */
class ListViewItem extends Panel //extends Component
{
	public $Checked;
	public $SubItems;
	private $ListViewId;
		
	function ListViewItem($objOrText = null)
	{
		parent::Panel(null, null, '100%', 20, $this);
		$this->Scrolling = System::Full;
		$this->LayoutType = Layout::Relative;
		$this->SubItems = &$this->Controls;//new ImplicitArrayList($this, "AddSubItem");
		$this->SubItems->AddFunctionName = 'AddSubItem';
		if($objOrText != null)
			$this->AddSubItem($objOrText);
	}
	function SetListView($listView)	{$this->ListViewId = $listView->Id;}
	function AddSubItem($objOrText=null)
	{
		$this->SubItems->Add((is_string($objOrText) || $objOrText == null)?$objOrText = new Label($objOrText, null, null, null, null):$objOrText, true, true);
		$objOrText->SetCSSClass("NLVItem");	
		//$objOrText->Width = '100%';
		/*$objOrText->CSSFloat = 'left';
		$objOrText->LayoutType = 1;*/
		if($this->ListViewId != null)
			GetComponentById($this->ListViewId)->Update($this);
		if(($tmpHeight = $objOrText->GetHeight()) > $this->GetHeight())
			$this->SetHeight($tmpHeight);
	}
}		
?>