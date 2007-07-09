menuWasDeactivated = false;

function ToggleSubMenuItems(whatMenuItemId, whatSubMenuItemsId, IsClick)
{
	if(menuWasDeactivated == true)
	{
		menuWasDeactivated = false;
		return;
	}
	var thisMenu = document.getElementById(whatMenuItemId);
	var tempParent = document.getElementById(thisMenu.MenuPanelParentId);
	
	if(thisMenu.IsSelected == null || thisMenu.IsMainMenu != null)
	{
		if(thisMenu.IsMainMenu != null)
		{
			if(IsClick == true)
				if(tempParent.IsClicked != true)
					tempParent.IsClicked = true;
			if(tempParent.IsClicked != true)
				return;
			thisMenu.setActive();
			thisMenu.detachEvent("ondeactivate", HideAllMainMenuChildren);
			thisMenu.attachEvent("ondeactivate", HideAllMainMenuChildren);
		}
		if(tempParent.SelectedMenuItemId != null)
		{
			if(thisMenu.IsMainMenu != null)
				HideAllMenuItemChildren(tempParent.SelectedMenuItemId, false, false);
			else
				HideAllMenuItemChildren(tempParent.SelectedMenuItemId, false, true);
		}
		tempParent.SelectedMenuItemId = whatMenuItemId;
		
		thisMenu.IsSelected = true;
	
		if((document.getElementById(whatSubMenuItemsId).ChildrenArray) != null)
		{
			thisMenu.ChildMenuId = whatSubMenuItemsId;
			//alert("I got you, you snake in the grass");
			ToggleVisibility(whatSubMenuItemsId);
		}
	}
}
function HideAllMainMenuChildren()
{
	if(window.event.toElement.id != "")
	{
		var temp = document.getElementById(window.event.toElement.id);
		if(temp != null)
		{
			if(temp.MenuPanelParentId != null)
			{
				var tempGrandParent = document.getElementById(temp.MenuPanelParentId);
				if(tempGrandParent.IsClicked == true)
					menuWasDeactivated = true;
				document.getElementById(window.event.srcElement.MenuPanelParentId).IsClicked = false;
				if(temp.IsMainMenu == null)
					if(temp.onclick != null)
						temp.onclick.call();
			}
			if(temp.MenuPanelParentId == null)
				document.getElementById(window.event.srcElement.MenuPanelParentId).IsClicked = false;
		}
	}
	else
		document.getElementById(window.event.srcElement.MenuPanelParentId).IsClicked = false;
	crappyglobal = window.event.srcElement.id;
	HideAllMenuItemChildren(crappyglobal, true, true);
}
function HideAllMenuItemChildren(whatMenuItemId, IsClick, changeColor)
{
	var OpenMenuItem = document.getElementById(whatMenuItemId);
	
	if(OpenMenuItem.ChildMenuId != null)
	{
		var ChildMenu = document.getElementById(OpenMenuItem.ChildMenuId);
		
		for(var i=0; i < ChildMenu.ChildrenArray.length; ++i)
			HideAllMenuItemChildren(ChildMenu.ChildrenArray[i], IsClick, changeColor);
		OpenMenuItem.IsSelected = null;
		if(OpenMenuItem.IsMainMenu == null || (OpenMenuItem.IsMainMenu != null && menuWasDeactivated != true && changeColor == true))
			ChangeMenuOutColors(OpenMenuItem.id, "transparent", "#000000");
		ChangeAndSave(ChildMenu.id, "style.visibility", "hidden");
	}
	OpenMenuItem.IsSelected = null;
	
	if(IsClick == true)
		if(OpenMenuItem.IsMainMenu != null)
			document.getElementById(OpenMenuItem.MenuPanelParentId).detachEvent("ondeactivate", HideAllMainMenuChildren);
	if(window.event != null)
		window.event.returnValue = false;
}
function ChangeMenuOutColors(menuItemId, outBackColor, outTextColor)
{
	var tmpMenuItem = document.getElementById(menuItemId);
	if(tmpMenuItem.IsSelected == null)
	{
		ChangeAndSave(tmpMenuItem.id, "style.background", outBackColor);
		ChangeAndSave(tmpMenuItem.id, "style.color", outTextColor);
	}
}