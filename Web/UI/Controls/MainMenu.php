<?php
/**
 * @package UI
 * @subpackage Controls
 */
class MainMenu extends Panel
{
	public $MenuItems;
	public $MenuType = "Click";
	
	function MainMenu($left = 0, $top = 0, $width = 0)
	{
		parent::Panel($left, $top, $width, 18, $this);
		$this->BackColor = "#f1f1ed";
		$this->MenuItems = &$this->Controls;
		$this->MenuItems->AddFunctionName = "AddMenuItem";
		$this->Scrolling = System::Full;
	}
	function AddMenuItem(MenuItem $menuItem)
	{
		$tempCount = $this->MenuItems->Count();
		if($tempCount > 0)
			$menuItem->SetLeft($this->MenuItems->Item[$tempCount -1]->GetLeft() + $this->MenuItems->Item[$tempCount -1]->GetWidth() + 5);
		$menuItem->MainMenuPanel->SetLeft($menuItem->GetLeft());
		$menuItem->MainMenuPanel->SetTop($menuItem->GetTop() + $menuItem->GetHeight());
		if($this->MenuType == "Click")
		{
			$menuItem->Click = new ClientEvent("ToggleSubMenuItems('{$menuItem->TextLabel->Id}','{$menuItem->MainMenuPanel->Id}', true);");
			$menuItem->MouseOver = new ClientEvent("ChangeMenuOutColors('{$menuItem->TextLabel->Id}','#316AC5', '#FFFFFF');");
			$menuItem->MouseOver[] = new ClientEvent("ToggleSubMenuItems('{$menuItem->TextLabel->Id}','{$menuItem->MainMenuPanel->Id}'), false;");
			$menuItem->MouseOut = new ClientEvent("ChangeMenuOutColors('{$menuItem->TextLabel->Id}','{$menuItem->OutBackColor}', '{$menuItem->OutTextColor}');");
		}
		$this->MenuItems->Add($menuItem, true, true);
		NolohInternal::SetProperty("IsMainMenu","true", $menuItem->TextLabel);
		NolohInternal::SetProperty("MenuPanelParentId", $this->Id, $menuItem->TextLabel);
		//QueueClientFunction($this, "document.getElementById('{$menuItem->TextLabel->Id}').IsMainMenu = true;");
		//AddScript("document.getElementById('{$menuItem->TextLabel->Id}').IsMainMenu = true; document.getElementById('{$menuItem->TextLabel->Id}').MenuPanelParentId = '{$this->Id}';");
	}
	function Clear()
	{
		$this->Controls->Clear();
	}
}
?>