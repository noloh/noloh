<?php 
/**
 * MenuItem class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Auxiliary
 */
class MenuItem extends Panel
{
	//public $RolloverImage;
	private $Checked;
	public $DefaultItem;
	public $MenuItemsPanel;
	public $MenuItems;
	private $TextLabel;
	private $Icon;
	private $Arrow;
	
	//TODO: Click to hide concept, show delay, hide delay, auto display sub menu on over, or click
	function MenuItem($textOrControl, $height=18)
	{
		parent::Panel(null, null, null, $height);
		if($textOrControl instanceof Control)
			$this->TextLabel = $textOrControl;
		else
		{
			$this->TextLabel = new Label($textOrControl, 0,0, System::Auto, 18);
			$this->TextLabel->CSSClass = 'NMnuItm';
		}
		$this->MenuItemsPanel = new Panel(0, 0, 0, 0, $this);
		$this->SetWidth($this->TextLabel->GetWidth() + 15);
		$this->TextLabel->SetWidth('100%');
		$this->TextLabel->Cursor = Cursor::Arrow;
		$this->MenuItemsPanel->Scrolling = System::Full;
		$this->MenuItemsPanel->Border = '1px solid #B3B9C7';
		$this->MenuItemsPanel->BackColor = 'white';
		$this->MenuItemsPanel->Visible = System::Vacuous;
		$this->MenuItems = &$this->MenuItemsPanel->Controls;
		$this->MenuItems->AddFunctionName = 'AddMenuItem';
		$this->SetOutBackColor();
		$this->SetOutTextColor();
		$this->SetOverBackColor();
		$this->SetOverTextColor();
		$this->MouseOver['Toggle'] = new ClientEvent("_NMnuTglSubItms('{$this->Id}');");
		NolohInternal::SetProperty('TxtLbl', $this->TextLabel->Id, $this);
		$this->TextLabel->ParentId = $this->Id;
		$this->MenuItemsPanel->ParentId = $this->Id;
	}
	/**
	 * @ignore
	 */
	function AddMenuItem($menuItem)
	{
		if(is_string($menuItem))
			$menuItem = new MenuItem($menuItem);
		$menuItem->Layout = Layout::Relative;
		
		/*else
		{
			//$tempImage = new Image(System::ImagePath() . "MenuItemArrow.gif", $menuItem->Width - 5, 3);
			//$this->Controls->Add($tempImage);
			//NolohInternal::SetProperty("HasChildren", "true", $this->TextLabel);
			NolohInternal::SetProperty('ChildrenArray', 'Array()', $this->MenuItemsPanel);
			//AddScript("_N('{$this->TextLabel->Id}').HasChildren = true; _N('{$this->MenuItemsPanel->Id}').ChildrenArray = new Array();");
		}*/
//		else
		if($this->MenuItemsPanel->Controls->Count() <= 0)
		{
//			$this->MenuItemsPanel = new Panel($this->GetRight(), $this->GetTop(), 100, 100);
			//Alert($this->MenuItemsPanel->Id);
			NolohInternal::SetProperty('ItmsPnl', "{$this->MenuItemsPanel->Id}", $this);
			NolohInternal::SetProperty('ChildrenArray', '[]', $this->MenuItemsPanel);
			//$menuItem->SetTop(0);
			//$menuItem->MenuItemsPanel->SetTop($menuItem->GetTop());
		}
		//$menuItem->Layout = Layout::Relative;
		//$menuItem->SetWidth('100%');
		//Alert($this->MenuItemsPanel->GetWidth() . ' | ' . $menuItem->GetWidth());
		if($this->MenuItemsPanel->GetWidth() < ($width = $menuItem->GetWidth()))
		{
			$this->MenuItemsPanel->SetWidth($width);
			//Alert($this->MenuItemsPanel->GetWidth());
			$count = $this->MenuItemsPanel->Controls->Count();
			for($i=0; $i<$count; ++$i)
				$this->MenuItemsPanel->Controls->Elements[$i]->SetWidth($width); 
			$menuItem->MenuItemsPanel->SetLeft($width);
		}
		else
			$menuItem->SetWidth($this->MenuItemsPanel->Width);
		$this->MenuItemsPanel->Height += $menuItem->GetHeight();
		$this->MenuItemsPanel->Controls->Add($menuItem, true);
		$id = $this->MenuItemsPanel->Id;
		$fncStr = '_N(\''.$id .'\').ChildrenArray.splice';
		if(isset($_SESSION['_NFunctionQueue'][$id]) && isset($_SESSION['_NFunctionQueue'][$id][$fncStr]))
			$_SESSION['_NFunctionQueue'][$id][$fncStr][0][] = "'{$menuItem->Id}'";
		else 
			QueueClientFunction($this->MenuItemsPanel, $fncStr, array(-1, 0, "'{$menuItem->Id}'"));

		return $menuItem;
	}
	function GetTextLabel()	{return $this->TextLabel;}
	function SetChecked($bool)
	{
		
	}
	function GetChecked()	{return $this->Checked;}
	function SetTrigger($trigger)
	{
		
	}
	function GetTrigger()	{return $this->Trigger;}
	function GetIcon()		{return $this->Icon;}
	function SetIcon($image)
	{
		$this->Icon = $image;
		$image->Left = 1;
		$image->Top = 1;
		$image->Width = 16;
		$image->Height = 16;
		$this->SetLeft($image->Right);
		$this->Controls->Add($image);
	}
	/**
	 * @ignore
	 */
	function GetText()		{return $this->TextLabel->GetText();}
	/**
	 * @ignore
	 */
	function SetText($text)	{$this->TextLabel->SetText($text);}
	/**
	 * @ignore
	 */
	function GetHeight()	{return $this->TextLabel->GetHeight();}
	/**
	 * @ignore
	 */
	function SetWidth($width)			
	{
		parent::SetWidth($width);
		$parent = $this->GetParent();
		if($parent != null && !($parent instanceof ContextMenu) && $parent instanceof Menu)
		{
			if($this->MenuItemsPanel->GetWidth() < $width)
			{
				$this->MenuItemsPanel->SetWidth($width);
				if(isset($this->MenuItems))
				foreach($this->MenuItems as $menuItem)
					$menuItem->SetWidth($width);
			}
		}
		elseif($this->MenuItemsPanel != null)
			$this->MenuItemsPanel->SetLeft($this->GetRight());
	}
	/**
	 * @ignore
	 */
	function SetBackColor($backColor)		{$this->TextLabel->SetBackColor($backColor);}
	/**
	 * @ignore
	 */
	function GetMouseOver()					{return $this->TextLabel->MouseOver;}
	/**
	 * @ignore
	 */
	function GetMouseOut()					{return $this->TextLabel->MouseOut;}
	/**
	 * @ignore
	 */
	function GetClick()						{return $this->TextLabel->Click;}
	/**
	 * @ignore
	 */
	function SetMouseOver($event)			{$this->TextLabel->SetMouseOver($event);}
	/**
	 * @ignore
	 */
	function SetMouseOut($event)			{$this->TextLabel->SetMouseOut($event);}
	/**
	 * @ignore
	 */
	function SetClick($event)				{$this->TextLabel->SetClick($event);}
	//function SetLayout($Layout)		{$this->TextLabel->SetLayout($Layout);}
	function SetOutTextColor($color='#001E42')	
	{
		$this->TextLabel->SetColor($color);
		NolohInternal::SetProperty('OtTxtClr', "$color", $this->TextLabel);
	}
	function SetOverTextColor($color='#FFFFFF')
	{
		NolohInternal::SetProperty('OvTxtClr', "$color", $this->TextLabel);
	}
	function SetOutBackColor($color='transparent')	
	{
		$this->TextLabel->SetBackColor($color);
		NolohInternal::SetProperty('OtBckClr', "$color", $this->TextLabel);
	}
	function SetOverBackColor($color='#07254A')
	{
		NolohInternal::SetProperty('OvBckClr', "$color", $this->TextLabel);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		AddNolohScriptSrc('MenuItem.js', true);
		parent::Show();	
	}
	/**
	 * @ignore
	 */
	function Hide()
	{
		parent::Hide();
		$this->TextLabel->Hide();
		$this->MenuItemsPanel->Hide();
	}
}

?>