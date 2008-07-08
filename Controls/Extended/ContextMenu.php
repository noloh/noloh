<?php
/**
 * ContextMenu class
 *
 * A ContextMenu is a Menu that can appear when right-clicking a Control by assigning a ContextMenu object to some existing Control's
 * ContextMenu property.
 * 
 * <pre>
 * // Instantiate a new Button
 * $but = new Button('Click Me');
 * // Instantiate a new ContextMenu and set it as the Button's ContextMenu
 * $but->ContextMenu = new ContextMenu();
 * // Add some MenuItems
 * $but->ContextMenu->MenuItems->Add(new MenuItem('First Item Text'));
 * $but->ContextMenu->MenuItems->Add(new MenuItem('Second Item Text'));
 * </pre>
 * 
 * @package Controls/Extended
 */
class ContextMenu extends Menu
{
	/**
	 * If some Control's ContextMenu has been opened, and a MenuItem has been clicked, launching a ServerEvent, then ContextMenu::$Source will be that Control. This becomes useful when different Controls all have the same ContextMenu and one wishes to know which Control's Menu has been accessed. 
	 * @var Control
	 */
	public static $Source;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends ContextMenu
	 * @return ContextMenu
	 */
	function ContextMenu()
	{
		parent::Menu();
		$this->SetHeight(0);
		$this->SetBorder('1px solid #A0A0A0');
		$this->SetVisible(false);
	}
	/**
	 * @ignore
	 */
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
	/**
	 * @ignore
	 */
	function Show()
	{
		AddNolohScriptSrc('ContextMenu.js', true);
		parent::Show();
	}
}

?>