<?php
/**
 * TabPanel class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Extended
 */
class TabPanel extends Panel
{
	const Top = 'Top';
	const Bottom = 'Bottom';
	
	public $TabBar;
	public $Body;
	public $TabPages;
	public $Tabs;
	
	private $TabAlignment = 'Top';
	private $SelectedIndex = -1;
	
		
	function TabPanel($left = 0, $top = 0, $width = 500, $height = 500)
	{
		parent::Panel($left, $top, null, null);
		$this->Tabs = new Group();
		$this->Tabs->Change = new ClientEvent('_NStTbPg', $this->Id, $this->Tabs->Id);
		
		$this->TabBar = new Panel(0, 0, '100%', 25);
		$this->Body = new Panel(0, 0, '100%', null, $this);
		
		$this->SetWidth($width);
		$this->SetHeight($height);

		$this->Body->Shifts[] = Shift::With($this, Shift::Height);
		
		$this->TabPages = &$this->Body->Controls;
		$this->TabPages->AddFunctionName = 'AddTabPage';
		
		$this->TabBar->Layout = $this->Body->Layout = Layout::Relative;
		$this->Controls->Add($this->TabBar);
		$this->Controls->Add($this->Body);
		$this->TabBar->Controls->Add($this->Tabs);
	}
	/*public function SetWidth($newWidth)
	{
		parent::SetWidth($newWidth);
		$this->TabPanelBar->SetWidth($newWidth);
		$this->TabPagesPanel->SetWidth($newWidth);
	}
	public function SetHeight($newHeight)
	{
		parent::SetHeight($newHeight);
		$this->TabPagesPanel->SetHeight($newHeight - $this->TabPanelBar->GetHeight());
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
	/**
	 * @ignore
	 */
	public function SetHeight($height)
	{
		parent::SetHeight($height);
		$this->Body->SetHeight($height-$this->TabBar->GetHeight());
	}
	public function GetSelectedIndex()	
	{
		return $this->Tabs->GetSelectedIndex();
	}
	public function GetSelectedTab()	{return $this->TabPages->Elements[$this->SelectedIndex];}
	public function SetSelectedTab($tabPage)
	{
		if(is_string($tabPage))
			$this->SetSelectedIndex($this->TabPanelBar->Controls->IndexOf(GetComponentById($tabPage)));
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
/*		if($this->TabPanelBar->Controls->Count < 1)
		{
			$this->TabPanelBar->Height = $temp->Height;
			$this->TabPagesPanel->Height = ($this->Height - $this->TabPanelBar->Height);
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
	/**
	 * @ignore
	 */
	public function GetChange()
	{
		return $this->Tabs->Change['user'];
	}
	/**
	 * @ignore
	 */
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
			$this->Body->Top = $this->TabPanelBar->Height;
		}
		else if($this->TabAlignment == 'Bottom')
		{
			$this->TabBar->Left = 0;
			$this->TabBar->Top = $this->TabPagesPanel->Height;
			$this->Body->Top = 0;
		}
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		AddNolohScriptSrc('TabPanel.js');
		//$initialProperties .= $this->GetEventString(null);
		parent::Show();
	}
}
?>
