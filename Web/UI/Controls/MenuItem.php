<?php 

class MenuItem extends Container
{
	//public $RolloverImage;
	//public $Checked;
	public $OverBackColor = '#316AC5';
	public $OutBackColor = 'transparent';
	public $OverTextColor = '#FFFFFF';
	public $OutTextColor = '#000000';
	public $DefaultItem;
	public $MainMenuPanel;
	public $MenuItems;
	private $TextLabel;
	private $Icon;
	
	function MenuItem($textOrControl, $icon=null, $checked=false)
	{
		parent::Container();
		if($textOrControl instanceof Control)
			$this->TextLabel = $textOrControl;
		else
			$this->TextLabel = new Label($textOrControl, 0,0, System::Auto, 18);
		//$this->TextLabel->Font = "11px Tahoma";
		$this->TextLabel->Cursor = Cursor::Arrow;
		$this->MainMenuPanel = new Panel($this->TextLabel->GetRight(), $this->TextLabel->GetTop(), 0, 0, $this);
		$this->MainMenuPanel->Scrolling = System::Full;
		$this->MainMenuPanel->Border = "1px solid black";
		$this->MainMenuPanel->BackColor = "white";
		$this->MainMenuPanel->ClientVisible = false;
		$this->MenuItems = &$this->MainMenuPanel->Controls;
		$this->MenuItems->AddFunctionName = "AddMenuItem";
		
		//New Way
		$this->TextLabel->MouseOver[] = new ClientEvent("ToggleSubMenuItems('{$this->TextLabel->DistinctId}','{$this->MainMenuPanel->DistinctId}', false); document.getElementById('{$this->TextLabel->DistinctId}').style.background = '{$this->OverBackColor}'; document.getElementById('{$this->TextLabel->DistinctId}').style.color = '{$this->OverTextColor}';");
		//$this->MouseOut = new ClientEvent("ChangeMenuOutColors('{$this->DistinctId}','{$this->OutBackColor}', '{$this->OutTextColor}')");
		$this->TextLabel->MouseOut[] = new ClientEvent("document.getElementById('{$this->TextLabel->DistinctId}').style.background = '{$this->OutBackColor}'; document.getElementById('{$this->TextLabel->DistinctId}').style.color = '{$this->OutTextColor}';");
		$this->Controls->AddRange($this->TextLabel, $this->MainMenuPanel);
	}
	function AddMenuItem(MenuItem $menuItem)
	{
		if($this->MainMenuPanel->Controls->Count() > 0)
		{
			$menuItem->Top = $this->MainMenuPanel->Controls->Item[$this->MainMenuPanel->Controls->Count()-1]->Top + $this->MainMenuPanel->Controls->Item[$this->MainMenuPanel->Controls->Count()-1]->Height;
			$menuItem->MainMenuPanel->SetTop($menuItem->GetTop());
		}
		else
		{
			//$tempImage = new Image(NOLOHConfig::GetNOLOHPath()."Web/UI/Controls/Images/MenuItemArrow.gif", $menuItem->Width - 5, 3);
			//$this->Controls->Add($tempImage);
			//NolohInternal::SetProperty("HasChildren", "true", $this->TextLabel);
			NolohInternal::SetProperty("ChildrenArray", "Array()", $this->MainMenuPanel);
			//AddScript("document.getElementById('{$this->TextLabel->DistinctId}').HasChildren = true; document.getElementById('{$this->MainMenuPanel->DistinctId}').ChildrenArray = new Array();");
		}
		if($this->MainMenuPanel->GetWidth() < $menuItem->GetWidth())
		{
			$this->MainMenuPanel->Width = $menuItem->Width;
			$tmpCount = $this->MainMenuPanel->Controls->Count();
			for($i=0; $i<$tmpCount;$i++)
				$this->MainMenuPanel->Controls->Item[$i]->Width = $menuItem->Width; 
		}
		else
			$menuItem->Width = $this->MainMenuPanel->Width;
		$this->MainMenuPanel->Height += $menuItem->Height;
		$this->MainMenuPanel->Controls->Add($menuItem, true, true);
		//QueueClientFunction($this, "document.getElementById('{$this->MainMenuPanel->DistinctId}').ChildrenArray.push", array("'{$menuItem->TextLabel->DistinctId}'"));
		$tmpId = $this->MainMenuPanel->DistinctId;
		$fncStr = "document.getElementById('$tmpId').ChildrenArray.splice";
		if(isset($_SESSION['NOLOHFunctionQueue'][$tmpId]) && isset($_SESSION['NOLOHFunctionQueue'][$tmpId][$fncStr]))
			$_SESSION['NOLOHFunctionQueue'][$tmpId][$fncStr][0][] = "'{$menuItem->TextLabel->DistinctId}'";
		else 
			QueueClientFunction($this->MainMenuPanel, $fncStr, array(-1, 0, "'{$menuItem->TextLabel->DistinctId}'"));
		NolohInternal::SetProperty("MenuPanelParentId", $tmpId, $menuItem->TextLabel);
		//AddScript("document.getElementById('{$this->MainMenuPanel->DistinctId}').ChildrenArray.push('{$menuItem->TextLabel->DistinctId}'); document.getElementById('{$menuItem->TextLabel->DistinctId}').MenuPanelParentId = '{$this->MainMenuPanel->DistinctId}';");
		if(!$this->Parent instanceof MainMenu)
			$this->TextLabel->MouseOut->Enabled = false;
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
		$this->Controls->Add($whatImage);
	}
	function GetText()		{return $this->TextLabel->Text;}
	function SetText($text)	{$this->TextLabel->Text = $text;}
	function GetLeft()		{return $this->TextLabel->Left;}
	function SetLeft($left)
	{
		$this->TextLabel->SetLeft($left);
		if(!($this->Parent instanceof MainMenu))
			$this->MainMenuPanel->SetLeft($this->GetLeft() + $this->GetWidth());
	}
	function GetTop()		{return $this->TextLabel->GetTop();}
	function SetTop($top)	{$this->TextLabel->SetTop($top);}
	function GetHeight()	{return $this->TextLabel->GetHeight();}
	function GetWidth()		{return $this->TextLabel->GetWidth();}
	function SetWidth($newWidth)			{$this->TextLabel->SetWidth($newWidth);}
	function SetBackColor($backColor)		{$this->TextLabel->SetBackColor($this->OutTextColor);}
	function SetOutTextColor($outTextColor)	{$this->TextLabel->SetColor($this->OutTextColor);}
	function SetOutOverTextColor($overTextColor){$this->TextLabel->SetColor($this->OutTextColor);}
	function GetMouseOver()	{return $this->TextLabel->MouseOver;}
	function GetMouseOut()	{return $this->TextLabel->MouseOut;}
	function GetClick()		{return $this->TextLabel->Click;}
	function SetMouseOver($event)	{$this->TextLabel->SetMouseOver($event);}
	function SetMouseOut($event)	{$this->TextLabel->SetMouseOut($event);}
	function SetClick($event)		{$this->TextLabel->SetClick($event);}
	
	function Show()
	{
		if(GetBrowser() == "ie")
			AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/IEMenuItem.js");
		else
			AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/MozillaMenuItem.js");	
		parent::Show();	
	}
}
?>