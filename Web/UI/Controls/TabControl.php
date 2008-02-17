<?php
/**
 * @package Web.UI.Controls
 */
class TabControl extends Panel
{
	const Top = 'Top';
	const Bottom = 'Bottom';
	public $TabPages;
	public $TabControlBar;
	public $TabPagesPanel;
	private $TabAlignment = 'Top';
	private $SelectedIndex = -1;
		
	function TabControl($left = 0, $top = 0, $width = 500, $height = 500)
	{
		parent::Panel($left, $top, null, null);
		$this->TabControlBar = new Panel(0, 0, null, 24);
		$this->TabPagesPanel = new Panel(0, $this->TabControlBar->GetHeight(), null, ($height - $this->TabControlBar->GetHeight()), $this);
		$this->SetWidth($width);
		$this->SetHeight($height);
		//$this->TabPagesPanel->CSSLeft_Border = "1px solid #91a7b7";
		//Added this line to Make the TabControl the TabPages Parent;
		//$this->TabPagesPanel->Controls->ParentId = $this->Id;
		$this->TabPages = &$this->TabPagesPanel->Controls;
		//$this->TabPages->SpecialFunction = "AddTabPage";
		$this->TabPages->AddFunctionName = 'AddTabPage';
		//$this->TabPages->SpecialObjectId = $this->Id;
		$this->Controls->Add($this->TabControlBar);
		$this->Controls->Add($this->TabPagesPanel);
	}
	public function SetWidth($newWidth)
	{
		parent::SetWidth($newWidth);
		$this->TabControlBar->SetWidth($newWidth);
		$this->TabPagesPanel->SetWidth($newWidth);
	}
	public function SetHeight($newHeight)
	{
		parent::SetHeight($newHeight);
		$this->TabPagesPanel->SetHeight($newHeight - $this->TabControlBar->GetHeight());
	}
	public function GetSelectedIndex()	
	{
		return $this->SelectedIndex;
	}
	public function GetSelectedTab()	{return $this->TabPages->Elements[$this->SelectedIndex];}
	public function SetSelectedTab($tabPage)
	{
		if(is_string($tabPage))
			$this->SetSelectedIndex($this->TabControlBar->Controls->IndexOf(GetComponentById($tabPage)));
		else 
			$this->SetSelectedIndex($this->TabPages->IndexOf($tabPage));
	}
	public function SetSelectedIndex($selectedIndex)
	{
		if($selectedIndex != $this->SelectedIndex)
		{
			$this->SelectedIndex = $selectedIndex;
			
			//Need to address the following line, currenty it breaks TabControl - Asher
			//$this->TabControlBar->Controls->Elements[$whatSelectedIndex]->SetSelected(true);
			//Why doesn't this work? - Asher, seems to be a priority thing. ---- Urgent
			QueueClientFunction($this, 'SetTabPage', array("'$this->Id'", "'{$this->TabControlBar->Controls->Elements[$selectedIndex]->Id}'","'{$this->TabPagesPanel->Controls->Elements[$selectedIndex]->Id}'", "0"), Priority::Low);
			if(!$this->Change->Blank())
				$this->Change->Exec();
		}
	}
	public function AddTabPage($tabPage)
	{	
		$temp = $tabPage->GetRolloverTab();
		$temp->Click = new ClientEvent("SetTabPage('{$this->Id}','{$temp->Id}','{$tabPage->Id}', 1);");
		$temp->TabPageId = $tabPage->Id;
		if($this->TabControlBar->Controls->Count < 1)
		{
			$this->TabControlBar->Height = $temp->Height;
			$this->TabPagesPanel->Height = ($this->Height - $this->TabControlBar->Height);
		}
		$temp->Left = (($tmpCount = $this->TabControlBar->Controls->Count()) > 0)?$this->TabControlBar->Controls->Elements[$tmpCount -1]->Right:0;
		$this->TabControlBar->Controls->Add($temp);
		$this->TabPagesPanel->Controls->Add($tabPage, true, true);
		if($this->TabPages->Count() == 1)
			$this->SetSelectedIndex(0);
		else
			$tabPage->Visible = System::Vacuous;
	}
	public function GetTabAlignment(){return $this->TabAlignment;}
	public function SetTabAlignment($tabAlignment)
	{
		$this->TabAlignment = $tabAlignment;
		if($this->TabAlignment == 'Top')
		{
			$this->TabControlBar->Left = 0;
			$this->TabControlBar->Top = 0; 
			$this->TabPagesPanel->Top = $this->TabControlBar->Height;
		}
		else if($this->TabAlignment == 'Bottom')
		{
			$this->TabControlBar->Left = 0;
			$this->TabControlBar->Top = $this->TabPagesPanel->Height;
			$this->TabPagesPanel->Top = 0;
		}
	}
	function Show()
	{
		AddNolohScriptSrc('TabControl.js');
		parent::Show();
	}
}
?>
