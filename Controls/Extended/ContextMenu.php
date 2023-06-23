<?php
/**
 * ContextMenu class
 *
 * A ContextMenu is a Menu that can appear when a user right-clicks some Control. It is typically
 * used for giving the user a choice of actions that can be performed, or options that can be set,
 * on that particular Control.
 * 
 * It is used by assigning an instance of the ContextMenu class to some Control's
 * ContextMenu property, as follows:
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
 * In addition, the same instance of ContextMenu can be given to several different Controls, if those
 * Controls have the same list of options associated with them. One can then retrieve which Control
 * was the Source of the Event by using the ContextMenu::$Source static variable, as follows:
 * 
 * <pre>
 * // Instantiate a ContextMenu and give it a MenuItem
 * $menu = new ContextMenu();
 * $menu->MenuItems->Add($menuItem new MenuItem('Perform action'));
 * $menuItem->Click = new ServerEvent($this, 'Action');
 * // Use this ContextMenu for some two Buttons, which we assume are already defined
 * $but1->ContextMenu = $menu;
 * $but2->ContextMenu = $menu;
 * // ... 
 * // Define the function which gets called for the Click Event
 * function Action()
 * {
 * 	// Displays some basic debug information about which Button the action was performed on
 * 	System::Log(ContextMenu::$Source);
 * }
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
	function __construct()
	{
		parent::__construct();
		$this->Buoyant = true;
		$this->SetHeight(0);
		$this->SetBorder('1px solid #A0A0A0');
		$this->SetVisible(System::Vacuous);
	}
	function SetAlignBottom()
	{
		NolohInternal::SetProperty('alignBottom', true, $this);
	}
	/**
	 * @ignore
	 */
	function AddMenuItem($menuItem)
	{
		if(!is_object($menuItem))
		{
			$menuItem = new MenuItem($menuItem);
		}
			
		$menuItem->Layout = Layout::Relative;
		$menuItem->SetLeft(0);
		if($this->GetWidth() <= ($width = $menuItem->GetWidth()))
		{
			$this->SetWidth($width + 10);
			$count = $this->MenuItems->Count();
			
			for($i=0; $i<$count; ++$i)
			{
				$this->MenuItems[$i]->SetWidth($width);
			}

			$menuItem->MenuItemsPanel->SetLeft($width);
		}
		else
		{
			$menuItem->SetWidth($this->GetWidth());
		}

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