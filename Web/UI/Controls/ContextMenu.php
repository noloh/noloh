<?php

class ContextMenu extends Menu
{
	public static $Source;
	
	function ContextMenu()
	{
		parent::Menu();
		$this->SetHeight(0);
		$this->SetBorder('1px solid #A0A0A0');
		$this->SetVisible(false);
	}
	function AddMenuItem(MenuItem $menuItem)
	{
		$tmpCount = $this->MenuItems->Count();
		if($tmpCount > 0)
			$menuItem->SetTop($this->MenuItems->Elements[$tmpCount -1]->GetBottom());
		else
			$menuItem->SetTop(0);
		$menuItem->SetLeft(0);
		//$menuItem->MenuItemsPanel->Buoyant = true;
		if($this->GetWidth() < ($tmpWidth = $menuItem->GetWidth()))
		{
			$this->SetWidth($tmpWidth);
			$tmpCount = $this->MenuItems->Count();
			for($i=0; $i<$tmpCount; ++$i)
				$this->MenuItems->Elements[$i]->SetWidth($menuItem->Width); 
			$menuItem->MenuItemsPanel->SetLeft($this->GetWidth());
		}
		else
			$menuItem->SetWidth($this->Width);
		$this->MenuItems->Add($menuItem, true, true);
		$this->Height += $menuItem->GetHeight();
		return $menuItem;
	}
	function Show()
	{
		AddNolohScriptSrc('ContextMenu.js', true);
		parent::Show();
	}
}

?>