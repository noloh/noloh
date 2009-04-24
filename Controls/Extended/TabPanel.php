<?php
/**
 * TabPanel class
 *
 * A TabPanel is a Panel with a TabBar that allows you to navigate quickly between several TabPages. 
 * 
 * <pre>
 * $tabPanel = new TabPanel();
 * $tabPanel->TabPages->Add($people = new TabPage('People'));
 * $tabPanel->TabPages->Add($business = new TabPage('Businesses'));
 * 
 * //Now we can add some things to our TabPages
 * $people->Controls->Add(...);
 * $business->Controls->Add(...);
 * 
 * //Now we add the TabPanel to some Panel's Controls 
 * $this->Controls->Add($tabPanel);
 * </pre>
 * 
 * @package Controls/Extended
 */
class TabPanel extends Panel
{
	/**
	 * The Panel containing the TabPanel's RolloverTabs
	 * @var Panel
	 */
	public $TabBar;
	/**
	 * The Panel used to display all TabPages of the TabPanel. All TabPages are automatically added to this Panel
	 * @var Panel
	 */
	public $Body;
	/**
	 * An ArrayList containing the TabPanel's TabPages. TabPages are added to this ArrayList for displaying in the TabPanel.
	 * @var ArrayList
	 */
	public $TabPages;
	private $Tabs;
	
	private $TabAlignment = 'Top';
	/**
	 * Constructor
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 */	
	function TabPanel($left = 0, $top = 0, $width = 500, $height = 500)
	{
		parent::Panel($left, $top, null, null);
		$this->Tabs = new Group();
		$this->Tabs->Change = new ClientEvent('_NTbPgSt', $this->Tabs->Id);
		
		$this->TabBar = new Panel(0, 0, '100%', 0);
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
	/**
	 * @ignore
	 */
	public function SetHeight($height)
	{
		parent::SetHeight($height);
		$this->Body->SetHeight($height-$this->TabBar->GetHeight());
	}
	/**
	 * Returns index of the currently selected TabPage
	 * 
	 * @return mixed
	 */
	public function GetSelectedIndex()	
	{
		return $this->Tabs->GetSelectedIndex();
	}
	/**
	 *  Returns the currently selected TabPage
	 *  @return TabPage
	 */
	public function GetSelectedTabPage()	{return $this->Tabs->GetSelectedIndex();}
	/**
	 * @deprecated Use SelectedTabPage instead
	 * Returns the currently selected TabPage
	 */
	public function GetSelectedTab()	{return $this->GetSelectedTabPage();}
	/**
	 * Sets the currently selected TabPage
	 * @param TabPage $tabPage
	 * @return TabPage;
	 */
	public function SetSelectedTabPage($tabPage)
	{
		if(is_string($tabPage))
			$this->SetSelectedIndex($this->TabPanelBar->Controls->IndexOf(GetComponentById($tabPage)));
		else 
			$this->SetSelectedIndex($this->TabPages->IndexOf($tabPage));
		return $tabPage;
	}
	/**
	 * Sets an TabPage of a particular index as selected
	 * @param integer $index
	 * @return integer;
	 */
	public function SetSelectedIndex($selectedIndex)
	{
		$this->Tabs->SetSelectedIndex($selectedIndex);
		return $selectedIndex;
	}
	/**
	 * @ignore
	 */
	public function AddTabPage($tabPage)
	{	
		if(!is_object($tabPage))
			$tabPage = new TabPage($tabPage);
		
		$rolloverTab = $tabPage->GetRolloverTab();
		$tabHeight = $rolloverTab->GetHeight();
		
		if($tabHeight > $this->TabBar->GetHeight())
		{
			$this->TabBar->SetHeight($tabHeight);
			$this->SetHeight($this->Height);
		}
		$rolloverTab->Layout = Layout::Relative;
		$rolloverTab->CSSFloat = 'left';

		$count = $this->Tabs->Count();
		$this->Tabs->Add($rolloverTab);
		$this->Body->Controls->Add($tabPage, true);
		NolohInternal::SetProperty('TabPg', $tabPage->Id, $rolloverTab);
		if($count == 0)
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
	/**
	 * Gets the orientation of the TabPanel's TabBar
	 * @return Layout::Top|Layout::Bottom
	 */
	public function GetTabOrientation(){return $this->TabAlignment;}
	/**
	 * Sets the orientation of the TabPanel's TabBar
	 * @param Layout::Top|Layout::Bottom
	 */
	public function SetTabOrientation($orientation)
	{
		$this->TabAlignment = $tabAlignment;
		if($this->TabAlignment == Layout::Top)
		{
			$this->TabBar->Left = 0;
			$this->TabBar->Top = 0; 
			$this->Body->Top = $this->TabPanelBar->Height;
		}
		else if($this->TabAlignment == Layout::Bottom)
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