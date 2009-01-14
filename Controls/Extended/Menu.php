<?php
/**
 * Menu class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Extended
 */
class Menu extends Panel
{
	const Click = 'Click', MouseOver = 'Over';
	public $MenuItems;
	public $Type = 'Click';
	
	function Menu($left = 0, $top = 0, $width = 0, $behavior=self::MouseOver)
	{
		parent::Panel($left, $top, $width, 18, $this);
		$this->BackColor = '#F1F1ED';
		$this->MenuItems = &$this->Controls;
		$this->MenuItems->AddFunctionName = 'AddMenuItem';
		$this->Scrolling = System::Full;
	}
	/**
	 * @ignore
	 */
	function AddMenuItem($menuItem)
	{
		if(is_string($menuItem))
			$menuItem = new MenuItem($menuItem);

		$left = (($count = $this->MenuItems->Count()) > 0)?$this->MenuItems->Elements[$count -1]->GetRight():0;
		$menuItem->SetLeft($left);
		
		$menuItem->MenuItemsPanel->SetLeft(0);
		$menuItem->MenuItemsPanel->SetTop($menuItem->GetBottom());
		$menuItem->CSSTextAlign = 'center';
		$menuItem->MenuItemsPanel->Buoyant = true;
		$this->MenuItems->Add($menuItem, true);
		$menuItem->SetWidth($menuItem->GetWidth());
		
		NolohInternal::SetProperty('IsMnu','true', $menuItem);
		return $menuItem;
	}
	function SetTrigger($trigger, $showDelay=0)
	{
		
	}
	function SetChecked($bool)
	{
	}
	/**
	 * Sets the orientation of the Menu. This determines whether the menu will be stacked horizontally or vertically.
	 *
	 * @param mixed $orientation
	 */
	function SetOrientation($orientation)
	{
		
	}
}
?>