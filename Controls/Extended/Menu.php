<?php
/**
 * @package Controls/Extended
 */
/**
 * Menu class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 */
class Menu extends Panel
{
	const Click = 'Click', MouseOver = 'Over';
	public $MenuItems;
	public $Type = 'Click';
	
	function Menu($left = 0, $top = 0, $width = 0)
	{
		parent::Panel($left, $top, $width, 18, $this);
		$this->BackColor = '#F1F1ED';
		$this->MenuItems = &$this->Controls;
		$this->MenuItems->AddFunctionName = 'AddMenuItem';
		$this->Scrolling = System::Full;
	}
	function AddMenuItem(MenuItem $menuItem)
	{
		$tmpCount = $this->MenuItems->Count();
		if($tmpCount > 0)
			$menuItem->SetLeft($this->MenuItems->Elements[$tmpCount -1]->GetRight());
		else
			$menuItem->SetLeft(0);
		$menuItem->MenuItemsPanel->SetLeft(0);
		$menuItem->MenuItemsPanel->SetTop($menuItem->GetBottom());
		$menuItem->CSSText_Align = 'center';
		$menuItem->MenuItemsPanel->Buoyant = true;
		$this->MenuItems->Add($menuItem, true, true);
		$menuItem->SetWidth($menuItem->GetWidth());
		
		NolohInternal::SetProperty('IsMnu','true', $menuItem);
		return $menuItem;
	}
}
?>