<?php
/**
 * @package Controls/Extended
 */
/**
 * TabControl class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 */
class TabControl extends Panel
{
	const Top = 'Top';
	const Bottom = 'Bottom';
	
	public $TabBar;
	public $Body;
	public $TabPages;
	public $Tabs;
	
	private $TabAlignment = 'Top';
	private $SelectedIndex = -1;
	
		
	function TabControl($left = 0, $top = 0, $width = 500, $height = 500)
	{
		parent::Panel($left, $top, $width, $height);
		$this->Tabs = new Group();
		$this->Tabs->Change = new ClientEvent('_NStTbPg', $this->Id, $this->Tabs->Id);
		
		$this->TabBar = new Panel(0, 0, '100%', 25);
		$this->Body = new Panel(0, 0, null, 'auto', $this);
		
//		$this->SetWidth($width);
//		$this->SetHeight($height);
		$this->TabPages = &$this->Body->Controls;
		$this->TabPages->AddFunctionName = 'AddTabPage';
		
		$this->TabBar->LayoutType = $this->Body->LayoutType = Layout::Relative;
		$this->Controls->Add($this->TabBar);
		$this->Controls->Add($this->Body);
		$this->TabBar->Controls->Add($this->Tabs);
	}
	/*public function SetWidth($newWidth)
	{
		parent::SetWidth($newWidth);
		$this->TabControlBar->SetWidth($newWidth);
		$this->TabPagesPanel->SetWidth($newWidth);
	}
	public function SetHeight($newHeight)
	{
		parent::SetHeight($newHeight);
		$this->TabPagesPanel->SetHeight($newHeight - $this->TabControlBar->GetHeight());
	}*/
	/*function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === null)
			return ',\'onchange\',\''.$this->GetEventString('Change').'\'';
		
		$preStr = '';
		if($eventTypeAsString == 'Change')
			$preStr = '_NStTbPg("'.$this->Id.'","' . $this->Tabs->Id . '");';
		return $preStr . parent::GetEventString($eventTypeAsString);
	}*/
	public function GetSelectedIndex()	
	{
		return $this->Tabs->GetSelectedIndex();
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
		$this->Tabs->SetSelectedIndex($selectedIndex);
	}
	public function AddTabPage($tabPage)
	{	
		$rolloverTab = $tabPage->GetRolloverTab();
/*		if($this->TabControlBar->Controls->Count < 1)
		{
			$this->TabControlBar->Height = $temp->Height;
			$this->TabPagesPanel->Height = ($this->Height - $this->TabControlBar->Height);
		}*/
		$rolloverTab->Left = (($tmpCount = $this->Tabs->Count()) > 0)?$this->Tabs[$tmpCount - 1]->GetRight():0;
		$this->Tabs->Add($rolloverTab);
		$this->Body->Controls->Add($tabPage, true, true);
		NolohInternal::SetProperty('TabPg', $tabPage->Id, $rolloverTab);
		if($tmpCount == 0)
			$this->SetSelectedIndex(0);
		else
			$tabPage->Visible = System::Vacuous;
	}
	public function GetChange()
	{
		return $this->Tabs->Change['user'];
	}
	public function SetChange($event)
	{
		$this->Tabs->Change['user'] = $event;
	}
	public function GetTabAlignment(){return $this->TabAlignment;}
	public function SetTabAlignment($tabAlignment)
	{
		$this->TabAlignment = $tabAlignment;
		if($this->TabAlignment == 'Top')
		{
			$this->TabBar->Left = 0;
			$this->TabBar->Top = 0; 
			$this->Body->Top = $this->TabControlBar->Height;
		}
		else if($this->TabAlignment == 'Bottom')
		{
			$this->TabBar->Left = 0;
			$this->TabBar->Top = $this->TabPagesPanel->Height;
			$this->Body->Top = 0;
		}
	}
	function Show()
	{
		AddNolohScriptSrc('TabControl.js');
		//$initialProperties .= $this->GetEventString(null);
		parent::Show();
	}
}
?>
