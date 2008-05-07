<?php 
/**
 * @package Web.UI.Controls
 */
class MenuItem extends Panel
{
	//public $RolloverImage;
	//public $Checked;
	public $DefaultItem;
	public $MenuItemsPanel;
	public $MenuItems;
	private $TextLabel;
	private $Icon;
	private $Arrow;
	
	function MenuItem($textOrControl, $height=18)
	{
		parent::Panel(null, null, null, $height);
		if($textOrControl instanceof Control)
			$this->TextLabel = $textOrControl;
		else
		{
			$this->TextLabel = new Label($textOrControl, 0,0, System::Auto, 20);
			$this->TextLabel->CSSClass = 'NMnuItm';
		}
		$this->SetWidth($this->TextLabel->GetWidth() + 15);
		$this->TextLabel->SetWidth('100%');
		$this->TextLabel->Cursor = Cursor::Arrow;
		$this->MenuItemsPanel = new Panel($this->GetRight(), 0, 0, 0, $this);
//		$this->MenuItemsPanel->Buoyant = true;
		$this->MenuItemsPanel->Scrolling = System::Full;
		//$this->MenuItemsPanel->Border = '1px solid black';
		$this->MenuItemsPanel->Border = '1px solid #B3B9C7';
		$this->MenuItemsPanel->BackColor = 'white';
		$this->MenuItemsPanel->Visible = System::Vacuous;
		//Alert($this->MenuItemsPanel->Id);
		$this->MenuItems = &$this->MenuItemsPanel->Controls;
		$this->MenuItems->AddFunctionName = 'AddMenuItem';
		$this->SetOutBackColor();
		$this->SetOutTextColor();
		$this->SetOverBackColor();
		$this->SetOverTextColor();
		//New Way
//		$this->TextLabel->MouseOver[] = new ClientEvent("ToggleSubMenuItems('{$this->Id}', '{$this->TextLabel->Id}','{$this->MenuItemsPanel->Id}', false);");
//		$this->TextLabel->MouseOver['Toggle'] = new ClientEvent("_NTglSubMnuItms('{$this->Id}', false);");
		$this->MouseOver['Toggle'] = new ClientEvent("_NTglSubMnuItms('{$this->Id}', false);");
		NolohInternal::SetProperty('TxtLbl', "{$this->TextLabel->Id}", $this);
		$this->TextLabel->ParentId = $this->Id;
		$this->MenuItemsPanel->ParentId = $this->Id;
		//$this->LayoutType = 1;
		//$this->Controls->AddRange($this->TextLabel, $this->MenuItemsPanel);
	}
	function AddMenuItem(MenuItem $menuItem)
	{
		//Alert($menuItem->Text);
		//$menuItem->Buoyant = true;
		//$menuItem->MouseOver[] = new ClientEvent("alert('test');");
		
		if(($tmpCount = $this->MenuItemsPanel->Controls->Count()) > 0)
		{
			//Alert($tmpCount);
			$menuItem->SetTop($this->MenuItemsPanel->Controls->Elements[$tmpCount-1]->GetBottom());
			//$menuItem->MenuItemsPanel->SetTop($menuItem->GetTop());
		}
		/*else
		{
			//$tempImage = new Image(NOLOHConfig::GetNOLOHPath()."Images/MenuItemArrow.gif", $menuItem->Width - 5, 3);
			//$this->Controls->Add($tempImage);
			//NolohInternal::SetProperty("HasChildren", "true", $this->TextLabel);
			NolohInternal::SetProperty('ChildrenArray', 'Array()', $this->MenuItemsPanel);
			//AddScript("document.getElementById('{$this->TextLabel->Id}').HasChildren = true; document.getElementById('{$this->MenuItemsPanel->Id}').ChildrenArray = new Array();");
		}*/
		else
		{
//			$this->MenuItemsPanel = new Panel($this->GetRight(), $this->GetTop(), 100, 100);
			//Alert($this->MenuItemsPanel->Id);
			NolohInternal::SetProperty('ItmsPnl', "{$this->MenuItemsPanel->Id}", $this);
//			NolohInternal::SetProperty('ItmsPnl', 'test', $this);
//			Alert($this->Id . 'la');
			NolohInternal::SetProperty('ChildrenArray', 'Array()', $this->MenuItemsPanel);
			$menuItem->SetTop(0);
			//$menuItem->MenuItemsPanel->SetTop($menuItem->GetTop());
		}
		//$menuItem->LayoutType = Layout::Relative;
		//$menuItem->SetWidth('100%');
		//Alert($this->MenuItemsPanel->GetWidth() . ' | ' . $menuItem->GetWidth());
		if($this->MenuItemsPanel->GetWidth() < ($tmpWidth = $menuItem->GetWidth()))
		{
			$this->MenuItemsPanel->SetWidth($tmpWidth);
			//Alert($this->MenuItemsPanel->GetWidth());
			$tmpCount = $this->MenuItemsPanel->Controls->Count();
			for($i=0; $i<$tmpCount; ++$i)
				$this->MenuItemsPanel->Controls->Elements[$i]->SetWidth($menuItem->Width); 
			$menuItem->MenuItemsPanel->SetLeft($menuItem->GetWidth());
		}
		else
			$menuItem->SetWidth($this->MenuItemsPanel->Width);
		$this->MenuItemsPanel->Height += $menuItem->GetHeight();
		$this->MenuItemsPanel->Controls->Add($menuItem, true, true);
		$tmpId = $this->MenuItemsPanel->Id;
		$fncStr = 'document.getElementById(\''.$tmpId .'\').ChildrenArray.splice';
		if(isset($_SESSION['_NFunctionQueue'][$tmpId]) && isset($_SESSION['_NFunctionQueue'][$tmpId][$fncStr]))
			$_SESSION['_NFunctionQueue'][$tmpId][$fncStr][0][] = "'{$menuItem->Id}'";
		else 
			QueueClientFunction($this->MenuItemsPanel, $fncStr, array(-1, 0, "'{$menuItem->Id}'"));
		//NolohInternal::SetProperty('ItmsPnl', $tmpId, $menuItem->TextLabel);
//		if(!$this->Parent instanceof MainMenu)
//			$this->TextLabel->MouseOut->Enabled = false;
		return $menuItem;
	}
	function GetTextLabel()	{return $this->TextLabel;}
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
	function GetText()		{return $this->TextLabel->GetText();}
	function SetText($text)	{$this->TextLabel->SetText($text);}
	function GetHeight()	{return $this->TextLabel->GetHeight();}
	function SetWidth($width)			
	{
		parent::SetWidth($width);
		$tmpParent = $this->GetParent();
		if($tmpParent != null && $tmpParent instanceof Menu)
		{
			if($this->MenuItemsPanel->GetWidth() < $width)
			{
				$this->MenuItemsPanel->SetWidth($width);
				foreach($this->MenuItems as $menuItem)
					$menuItem->SetWidth($width);
			}
		}
		elseif($this->MenuItemsPanel != null)
			$this->MenuItemsPanel->SetLeft($this->GetRight());
	}
	function SetBackColor($backColor)		{$this->TextLabel->SetBackColor($backColor);}
	function GetMouseOver()					{return $this->TextLabel->MouseOver;}
	function GetMouseOut()					{return $this->TextLabel->MouseOut;}
	function GetClick()						{return $this->TextLabel->Click;}
	function SetMouseOver($event)			{$this->TextLabel->SetMouseOver($event);}
	function SetMouseOut($event)			{$this->TextLabel->SetMouseOut($event);}
	function SetClick($event)				{$this->TextLabel->SetClick($event);}
	//function SetLayoutType($layoutType)		{$this->TextLabel->SetLayoutType($layoutType);}
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
	function Show()
	{
		AddNolohScriptSrc('MenuItem.js', true);
		parent::Show();	
	}
	function Hide()
	{
		parent::Hide();
		$this->TextLabel->Hide();
		$this->MenuItemsPanel->Hide();
	}
}
?>