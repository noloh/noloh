mnuDeactivated = false;

function ToggleSubMenuItems(mnuItmId, txtLblId, sbMenuId, isClk)
{
	if(mnuDeactivated == true)
	{
		mnuDeactivated = false;
		return;
	}
	var menu = document.getElementById(mnuItmId);
	var subMenu =  document.getElementById(sbMenuId);
	var label =  document.getElementById(txtLblId);
	var tmpParent = document.getElementById(label.MenuPanelParentId)
	
	if(label.IsSelected == null || label.IsMainMenu != null)
	{
		if(label.IsMainMenu != null)
		{
			if(isClk == true)
				if(tmpParent.IsClicked != true)
					tmpParent.IsClicked = true;
			if(tmpParent.IsClicked != true)
				return;
			label.setActive();
			label.detachEvent("ondeactivate", HideAllMainMenuChildren);
			label.attachEvent("ondeactivate", HideAllMainMenuChildren);
		}
		if(tmpParent.SelectedMenuItemId != null)
		{
			if(label.IsMainMenu != null)
				HideAllMenuItemChildren(tmpParent.SelectedMenuItemId, false, false);
			else
				HideAllMenuItemChildren(tmpParent.SelectedMenuItemId, false, true);
		}
		tmpParent.SelectedMenuItemId = label.id;
		label.IsSelected = true;
	
		if((document.getElementById(sbMenuId).ChildrenArray) != null)
		{
			label.ChildMenuId = sbMenuId;
			ToggleVisibility(sbMenuId);
		}
	}
}
function HideAllMainMenuChildren()
{
	if(window.event.toElement.id != "")
	{
		var tmp = document.getElementById(window.event.toElement.id);
		if(tmp != null)
		{
			if(tmp.MenuPanelParentId != null)
			{
				var tempGrandParent = document.getElementById(tmp.MenuPanelParentId);
				if(tempGrandParent.IsClicked == true)
					mnuDeactivated = true;
				document.getElementById(window.event.srcElement.MenuPanelParentId).IsClicked = false;
				if(tmp.IsMainMenu == null)
					if(tmp.onclick != null)
						tmp.onclick.call();
			}
			if(tmp.MenuPanelParentId == null)
				document.getElementById(window.event.srcElement.MenuPanelParentId).IsClicked = false;
		}
	}
	else
		document.getElementById(window.event.srcElement.MenuPanelParentId).IsClicked = false;
	crappyglobal = window.event.srcElement.id;
	HideAllMenuItemChildren(crappyglobal, true, true);
}
function HideAllMenuItemChildren(mnuItmId, isClick, changeColor)
{
	var OpenMenuItem = document.getElementById(mnuItmId);
	
	if(OpenMenuItem.ChildMenuId != null)
	{
		var ChildMenu = document.getElementById(OpenMenuItem.ChildMenuId);
		
		for(var i=0; i < ChildMenu.ChildrenArray.length; ++i)
			HideAllMenuItemChildren(ChildMenu.ChildrenArray[i], isClick, changeColor);
		OpenMenuItem.IsSelected = null;
		if(OpenMenuItem.IsMainMenu == null || (OpenMenuItem.IsMainMenu != null && mnuDeactivated != true && changeColor == true))
			OpenMenuItem.onmouseout.call();
		//ChangeMenuOutColors(OpenMenuItem.id, "transparent", "#000000");
		ChangeAndSave(ChildMenu.id, "style.display", "none"); 
	}
	OpenMenuItem.IsSelected = null;
	if(isClick)
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