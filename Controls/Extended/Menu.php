<?php
/**
 * Menu class
 *
 * A Menu is a drop down list of MenuItems. Each MenuItem itself can also contain MenuItems, thus allowing for extensive cascading Menus.
 * 
 * @package Controls/Extended
 */
class Menu extends Panel
{
	/**
	 * @ignore
	 */
	const Click = 'Click';
	/**
	 * @ignore
	 */
	const MouseOver = 'Over';
	/**
	* An ArrayList of MenuItems that will be Shown when added, provided the Menu has also been Shown.
	* 
	* MenuItems are an ArrayList and can be added, removed, or inserted. See ArrayList for more information.
	* 
	* <pre>
	* //Adding an MenuItem through a string
	* $menu->MenuItems->Add('Option 1');
	* //Adding multiple MenuItems through strings
	* $menu->MenuItems->Add->AddRange('Option 1', 'Option 2');
	* //Adding a MenuItem to MenuItems:
	* $menu->MenuItems->Add->Add(new MenuItem('Option 1'));
	* //Adding multiple MenuItems through AddRange()
	* $menu->MenuItems->Add->AddRange(new MenuItem('Option 1'), new MenuItem('Section 2'));
	* </pre>
	* @var ArrayList
	*/
	public $MenuItems;
	/**
	 * @ignore
	 */
	public $Type = 'Click';
	/**
	 * Constructor
	 * 
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 */
	function Menu($left = 0, $top = 0, $width = 0/*, $behavior=self::MouseOver*/)
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
	/**
	 * @ignore
	 */
	function SetTrigger($trigger, $showDelay=0)
	{
		
	}
	/**
	 * @ignore
	 */
	function SetChecked($bool)
	{
	}
	 /* Sets the orientation of the Menu. This determines whether the menu will be stacked horizontally or vertically.
	 *
	 * @param mixed $orientation
	 */
	 /**
	 * @ignore
	 */
	function SetOrientation($orientation)
	{
		
	}
}
?>