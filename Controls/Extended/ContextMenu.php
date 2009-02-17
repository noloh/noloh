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
		$this->SetVisible(System::Vacuous);
	}
	/**
	 * @ignore
	 */
	function AddMenuItem(MenuItem $menuItem)
	{
		$menuItem->Layout = Layout::Relative;
		$menuItem->SetLeft(0);
		//$menuItem->MenuItemsPanel->Buoyant = true;
		if($this->GetWidth() < ($width = $menuItem->GetWidth()))
		{
			$this->SetWidth($width);
			$count = $this->MenuItems->Count();
			
			for($i=0; $i<$count; ++$i)
				$this->MenuItems[$i]->SetWidth($width); 
			$menuItem->MenuItemsPanel->SetLeft($width);
		}
		else
			$menuItem->SetWidth($this->GetWidth());
		$menuItem->MenuItemsPanel->BackColor = '#F1F1ED';
		$this->MenuItems->Add($menuItem, true);
		$this->Height += $menuItem->GetHeight();
		return $menuItem;
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		AddNolohScriptSrc('ContextMenu.js', true);
		AddNolohScriptSrc('ClickOff.js', true);
		parent::Show();
	}
}

?>