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
			//alert("I'm a MainMenu");
			if(IsClick == true)
				if(tempParent.IsClicked != true)
					tempParent.IsClicked = true;
//			if(tempParent.IsClicked != true)
//				alert("Not Clicked");
//			else
//				alert("Is Clicked");
			if(tempParent.IsClicked != true)
				return;
			MainMenuItemPanelGlobal = thisMenu.id;
			document.body.addEventListener("click", HideAllMainMenuChildren, true);
		}
//		alert(tempParent.id + 'is the parentId');
//		alert(tempParent.SelectedMenuItemId + 'is not null');
		if(tempParent.SelectedMenuItemId != null)
		{
			//alert('I do go here');
			if(thisMenu.IsMainMenu != null)
			{
				if(tempParent.SelectedMenuItemId != whatMenuItemId)
				{
					var tmpBool = menuWasDeactivated;
					menuWasDeactivated = false;
					HideAllMenuItemChildren(tempParent.SelectedMenuItemId, false, true);
					menuWasDeactiveted = tmpBool;
				}
				else
					HideAllMenuItemChildren(tempParent.SelectedMenuItemId, false, false);
			}
			else
				HideAllMenuItemChildren(tempParent.SelectedMenuItemId, false, true);
		}
		tempParent.SelectedMenuItemId = whatMenuItemId;
		//alert(tempParent.SelectedMenuItemId + " is the selected MenuItemId");
		thisMenu.IsSelected = true;
	
		if((document.getElementById(whatSubMenuItemsId).ChildrenArray) != null)
		{
			thisMenu.ChildMenuId = whatSubMenuItemsId;
			//alert("I got you, you snake in the grass");
			ToggleVisibility(whatSubMenuItemsId);
		}
	}
}
function HideAllMainMenuChildren(event)
{
	if(event.target.id != null)
	{
		var temp = document.getElementById(event.target.id);
		if(temp != null)
		{
			if(temp.MenuPanelParentId != null)
			{
				var tempGrandParent = document.getElementById(temp.MenuPanelParentId);
				if(tempGrandParent.IsClicked == true)
					menuWasDeactivated = true;
				document.getElementById(document.getElementById(MainMenuItemPanelGlobal).MenuPanelParentId).IsClicked = false;
			}
			if(temp.MenuPanelParentId == null)
				document.getElementById(document.getElementById(MainMenuItemPanelGlobal).MenuPanelParentId).IsClicked = false;
		}
		else
			document.getElementById(document.getElementById(MainMenuItemPanelGlobal).MenuPanelParentId).IsClicked = false;	
	}
	else
		document.getElementById(document.getElementById(MainMenuItemPanelGlobal).MenuPanelParentId).IsClicked = false;
		
	HideAllMenuItemChildren(MainMenuItemPanelGlobal, true, true);
	document.body.removeEventListener("click", HideAllMainMenuChildren, true);
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
			document.body.removeEventListener("click", HideAllMainMenuChildren, true);
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