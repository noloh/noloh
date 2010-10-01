<?php 
/**
 * MenuItem class
 *
 * MenuItem represents an option in a Menu.
 * 
 * @package Controls/Auxiliary
 */
class MenuItem extends Panel
{
	/**
	 * The Panel containing the MenuItem's SubItems.
	 * @var Panel
	 */
	public $MenuItemsPanel;
	/**
	 * An ArrayList of MenuItems that will be Shown when added, provided the Menu has also been Shown.
	 * 
	 * MenuItems are an ArrayList and can be added, removed, or inserted. See ArrayList for more information.
	 * 
	 * $menu = new Menu();
	 * $add = $menu->MenuItems->Add('Add');
	 * We can now $add MenuItems directly to the MenuItem
	 * <pre>
	 * //Adding a MenuItem through a string
	 * $add->MenuItems->Add('Boiler');
	 * //Adding multiple MenuItems through strings
	 * $add->MenuItems->Add->AddRange('Sink', 'Door');
	 * //Adding a MenuItem to MenuItems:
	 * $add->MenuItems->Add->Add(new MenuItem('Sink'));
	 * //Adding multiple MenuItems through AddRange()
	 * $add->MenuItems->Add->AddRange(new MenuItem('Sink'), new MenuItem('Door'));
	 * </pre>
	 * @var ArrayList
	 */
	public $MenuItems;
	private $TextLabel;
	private $Icon;
	private $Arrow;
	
	//TODO: Click to hide concept, show delay, hide delay, auto display sub menu on over, or click
	/**
	 * Constructor
	 * 
	 * @param string|Control $textOrControl
	 * @param integer $height The Height of this element
	 */
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
			NolohInternal::SetProperty('ItmsPnl', "{$this->MenuItemsPanel->Id}", $this);
			NolohInternal::SetProperty('ChildrenArray', '[]', $this->MenuItemsPanel);
		}
		if($this->MenuItemsPanel->GetWidth() < ($width = $menuItem->GetWidth()))
		{
			$this->MenuItemsPanel->SetWidth($width);
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
			ClientScript::Queue($this->MenuItemsPanel, $fncStr, array(-1, 0, $menuItem->GetId()));
		return $menuItem;
	}
	/**
	 * Returns the Element in which the Text for this MenuItem is displayed. Label by default.
	 * @return Label
	 */
	function GetElement()	{return $this->TextLabel;}
	/*
	 * Returns the Label in which the Text for this MenuItem is displayed.
	 * @deprecated Use Element instead
	 * @return Label
	 */
	function GetTextLabel()	{return $this->TextLabel;}
	
	/**
	 * @ignore
	 */
	function SetTrigger($trigger)
	{
		
	}
	/**
	 * @ignore
	 */
	function GetTrigger()	{return $this->Trigger;}
	/**
	 * @ignore
	 */
	function GetIcon()		{return $this->Icon;}
	/**
	 * @ignore
	 */
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
	/**
	 * Sets the color of the MenuItem's text under normal circumstances
	 * 
	 * @param string $color
	 */
	function SetOutTextColor($color='#001E42')	
	{
		$this->TextLabel->SetColor($color);
		NolohInternal::SetProperty('OtTxtClr', $color, $this->TextLabel);
	}
	/**
	 * Sets the color of the MenuItem's text when the mouse cursor is over it
	 * 
	 * @param string $color
	 */
	function SetOverTextColor($color='#FFFFFF')
	{
		NolohInternal::SetProperty('OvTxtClr', $color, $this->TextLabel);
	}
	/**
	 * Sets the background color of the MenuItem under normal circumstances
	 * 
	 * @param string $color
	 */
	function SetOutBackColor($color='transparent')	
	{
		$this->TextLabel->SetBackColor($color);
		NolohInternal::SetProperty('OtBckClr', $color, $this->TextLabel);
	}
	/**
	 * Sets the background color of the MenuItem when the mouse cursor is over it
	 * 
	 * @param string $color
	 */
	function SetOverBackColor($color='#07254A')
	{
		NolohInternal::SetProperty('OvBckClr', $color, $this->TextLabel);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		ClientScript::AddNOLOHSource('MenuItem.js', true);
		parent::Show();	
	}
}

?>