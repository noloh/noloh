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
	private $TabScrolling;
	private $TabScroller;
	private $ScrollerInfo;

	private $TabAlignment = 'Top';
	//InnerSugar InnerClasses
	static $_InTabScroller = 'HandleTabScroller';
	
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

		$this->TabScroller = new Panel(0, 0, '100%', 0);
		$this->TabBar = new Panel(0, 0, '100%', '100%');
		$this->Body = new Panel(0, 0, null, null, $this);
//		$this->Body = new Panel(0, 0, '100%', null, $this);

		$this->SetWidth($width);
		$this->SetHeight($height);

		$this->Body->Shifts[] = Shift::SizeWith($this);

		$this->TabPages = &$this->Body->Controls;
		$this->TabPages->AddFunctionName = 'AddTabPage';
		$this->TabPages->RemoveFunctionName = 'RemoveTabPage';

		$this->TabScroller->Layout = $this->Body->Layout = Layout::Relative;
		$this->Controls->AddRange($this->TabScroller, $this->Body);
	//	$this->Controls->Add($this->Body);
		$this->TabScroller->Controls->Add($this->TabBar);
		$this->TabBar->Controls->Add($this->Tabs);
	}
	/**
	* Gets the TabScoller Panel which contains the TabScroller arrows, and TabBar
	 */
	public function GetTabScroller()	{return $this->TabScroller;}
	/**
	 * @ignore
	 */
	public function SetHeight($height)
	{
		parent::SetHeight($height);
		$this->Body->SetHeight($height - $this->TabScroller->GetHeight());
	}
	/**
	* @ignore
	*/
	public function SetWidth($width)
	{
		parent::SetWidth($width);
		$this->Body->SetWidth($width);
		if(isset($this->ScrollerInfo))
		{
			if($this->TabScrolling === true)
			{
				if(isset($this->ScrollerInfo['back']))
					$width -= $this->ScrollerInfo['back']->GetWidth();
				if(isset($this->ScrollerInfo['next']))
					$width -= $this->ScrollerInfo['next']->GetWidth();
			}
			$this->TabScroller->SetWidth($width);
			if(!isset($this->TabScroller->Shifts['width']))
				$this->TabScroller->Shifts['width'] =  Shift::WidthWith($this);
		}
	}
	/**
	 * Returns index of the currently selected TabPage
	 * @return mixed
	 */
	public function GetSelectedIndex()
	{
		return $this->Tabs->GetSelectedIndex();
	}
	/**
	 * Returns the currently selected TabPage
	 * @return TabPage
	 */
	public function GetSelectedTabPage()	
	{
		$index = $this->GetSelectedIndex();
		if($index != -1)
			return $this->TabPages[$index];
		return null;
	}
	/**
	 * Returns the currently selected TabPage
	 * @deprecated Use SelectedTabPage instead
	 * @return TabPage
	 */
	public function GetSelectedTab()	{return $this->GetSelectedTabPage();}
	/**
	 * Sets the currently selected TabPage
	 * @param TabPage $tabPage
	 * @return TabPage
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
	 * Returns the SelectedTab. This is a convenient alias because different types of Controls may have different interpretations of "Value."
	 * @return SelectedTab
	 */
	function GetValue()			{return $this->GetSelectedTab();}
	/**
	 * Sets the SelectedTab. This is a convenient alias because different types of Controls may have different interpretations of "Value."
	 * @param SelectedTab $value
	 */
	function SetValue($value)	{return $this->SetSelectedTab($value);}
	/**
	 * Sets an TabPage of a particular index as selected
	 * @param integer $index
	 * @return integer
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
		$rolloverTab->Leave[] = new ClientEvent('_NTbPgRm', $rolloverTab);
		$tabHeight = $rolloverTab->GetHeight();

		if($tabHeight > $this->TabScroller->GetHeight())
		{
			$this->TabScroller->SetHeight($tabHeight);
			$this->SetHeight($this->Height);
		}
		$rolloverTab->SetLayout(Layout::Relative);
		$rolloverTab->CSSFloat = 'left';

		$count = $this->Tabs->Count();
		$this->Tabs->Add($rolloverTab);
		$this->TabBar->Controls->Add($rolloverTab);
//		ClientScript::Set($rolloverTab, 'TabPg', $tabPage);
		NolohInternal::SetProperty('TabPg', $tabPage->Id, $rolloverTab);
		if($count == 0)
			$this->SetSelectedIndex(0);
		else
			$tabPage->Visible = System::Cloak;
		
		$tabScrolling = $this->TabScrolling;
		if($tabScrolling === System::Auto || $tabScrolling === true)
		{
			if($tabScrolling === System::Auto)
			{
				ClientScript::Queue($this, '_NTbPgScrlChk', $this->TabBar);
				/*Temporary Hack for checking scrollbars, won't work if auto is
				set after adding tabs*/ 
				$rolloverTab->Leave['scroller'] = new ClientEvent('_NTbPgScrlChk', $this->TabBar);
			}
			ClientScript::Queue($this, '_NTbPgScrl', array($this->TabBar, 'style.left', -1));
		}
			
		return $this->Body->Controls->Add($tabPage, true);
	}
	function RemoveTabPage($tabPage)
	{
		$tabPage->GetRolloverTab()->Leave();
		$this->Body->Controls->Remove($tabPage, true);
	}
	function HandleTabScroller()
	{
		$args = func_get_args();
		$invocation = InnerSugar::$Invocation;
		$prop = strtolower(InnerSugar::$Tail);
		if($invocation == InnerSugar::Set)
		{
			switch($prop)
			{
				case 'back':
				case 'next':
					$this->SetScroller($prop, $args[0]);
					break;
				case 'scrollincrement':
				case 'scrollduration':
					$this->ScrollerInfo[$prop] = $args[0];
					ClientScript::Set($this->TabScroller, $prop, $args[0], '_N');
					break;
				default: throw new SugarException();
			}
		}
		elseif($invocation == InnerSugar::Get && isset($this->ScrollerInfo[$prop]))
			return $this->ScrollerInfo[$prop];
	}
	/**
	* Gets whether the TabScroller Tabs scroll. True always displays TabScroller arrows, while System::Auto will automatically decide.
	* @return true|false|System::Auto
	*/
	function GetTabScrolling()
	{
		return $this->TabScrolling !== null?$this->TabScrolling:false;
	}
	private function SetScrollerClient($prop, $value)
	{
		ClientScript::Set($this->TabScroller, $prop, $value, '_N');
		$this->ScrollerInfo[$prop] = $value;
	}
	private function SetScroller($type, $object, $bypass=false)
	{
		if(isset($this->ScrollerInfo[$type]))
			$this->Controls->Remove($this->ScrollerInfo[$type]);
			
		if(!$object)
			$object =  new Button($type == 'back'?'<':'>', 0, 0, 20, $this->TabScroller->GetHeight());
				
		if($type == 'back')
		{
			$this->Controls->PositionalInsert($this->ScrollerInfo['back'] = $object, 0, 0)
				->CasCSSFloat('left')
				->CasLayout(Layout::Relative)
//				->Click =  new ClientEvent("new _NAni('{$this->TabBar}', 'style.left', _N('{$this->TabBar}').offsetLeft + 100, 500);");
				->Click =  new ClientEvent('_NTbPgScrl', $this->TabBar, 'style.left', 1);
			ClientScript::Set($this->TabScroller, 'back', $this->ScrollerInfo['back'], '_N');
			if($this->TabScrolling !== true)
				$this->ScrollerInfo['back']->Visible = System::Vacuous;
			
		}
		elseif($type == 'next')
		{
			$this->Controls->Add($this->ScrollerInfo['next'] = $object)
//				->Click =  new ClientEvent("new _NAni('{$this->TabBar}', 'style.left', _N('{$this->TabBar}').offsetLeft - 100, 500);");
				->Click =  new ClientEvent('_NTbPgScrl', $this->TabBar, 'style.left', -1);
			$this->ScrollerInfo['next']->ReflectAxis('x');
			ClientScript::Set($this->TabScroller, 'next', $this->ScrollerInfo['next'], '_N');
			if($this->TabScrolling !== true)
				$this->ScrollerInfo['next']->Visible = System::Vacuous;
		}
		if(!$bypass)
			$this->SetWidth($this->GetWidth());
	}
	/**
	* Sets whether the TabScroller Tabs scroll. True always displays TabScroller arrows, while System::Auto will automatically decide.
	* 
	* @param true|false|System::Auto $scroll
	*/
	function SetTabScrolling($scroll)
	{
		$this->TabScrolling = $scroll;
		
		if($scroll !== false)
		{
			$this->TabBar->SetWidth(System::Auto);
			if(!isset($this->ScrollerInfo['scrollincrement']))
				$this->SetScrollerClient('scrollincrement', 200);
			if(!isset($this->ScrollerInfo['scrollduration']))
				$this->SetScrollerClient('scrollduration', 200);
			if(!isset($this->ScrollerInfo['back']))
				$this->SetScroller('back', null,  true);
			elseif($scroll === true)
				$this->ScrollerInfo['back']->SetVisible(true);
			if(!isset($this->ScrollerInfo['next']))
				$this->SetScroller('next', null, true);
			elseif($scroll === true)
				$this->ScrollerInfo['next']->SetVisible(true);
			//Change into tie-in of central SetWidth
			$this->SetWidth($this->Width);
		}
		else
		{
			if(isset($this->ScrollerInfo['back']))
				$this->ScrollerInfo['back']->SetVisible(System::Vacuous);
			if(isset($this->ScrollerInfo['next']))
				$this->ScrollerInfo['next']->SetVisible(System::Vacuous);
		}
//		$scroll === System::Auto?'auto':(($scroll === true)?true:false)
		ClientScript::Set($this->TabScroller, 'auto', $scroll === System::Auto, '_N');
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
		ClientScript::AddNOLOHSource('TabPanel.js');
		parent::Show();
	}
}
?>