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
	//ChangeAndSave(subMenu.id, 'style.top', menu.offsetTop + 'px');
	
//	var tmpParent = 
//	var label = document.getElementById(txtLblId);
//	var tmpParent = document.getElementById(label.MenuPanelParentId);
	if(label.IsSelected == null || label.IsMainMenu != null)
	{
		if(label.IsMainMenu != null)
		{
			if(isClk == true)
				if(tmpParent.IsClicked != true)
					tmpParent.IsClicked = true;
			if(tmpParent.IsClicked != true)
				return;
			MainMenuItemPanelGlobal = label.id;
			document.body.addEventListener("click", HideAllMainMenuChildren, true);
		}
		if(tmpParent.SelectedMenuItemId != null)
		{
			if(label.IsMainMenu != null)
			{
				if(tmpParent.SelectedMenuItemId != label.id)
				{
					var tmpBool = mnuDeactivated;
					mnuDeactivated = false;
					HideAllMenuItemChildren(tmpParent, SelectedMenuItemId, false, true);
					menuWasDeactiveted = tmpBool;
				}
				else
					HideAllMenuItemChildren(tmpParent.SelectedMenuItemId, false, false);
			}
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
function HideAllMainMenuChildren(event)
{
	//return;
	if(event.target.id != null)
	{
		var tmp = document.getElementById(event.target.id);
		if(tmp != null)
		{
			if(tmp.MenuPanelParentId != null)
			{
				var tempGrandParent = document.getElementById(tmp.MenuPanelParentId);
				if(tempGrandParent.IsClicked == true)
					mnuDeactivated = true;
				document.getElementById(document.getElementById(MainMenuItemPanelGlobal).MenuPanelParentId).IsClicked = false;
			}
			if(tmp.MenuPanelParentId == null)
			{
//				alert(MainMenuItemPanelGlobal);
//				document.getElementById(document.getElementById(MainMenuItemPanelGlobal).MenuPanelParentId).IsClicked = false;
//				alert(document.getElementById(document.getElementById(MainMenuItemPanelGlobal).MenuPanelParentId));//.IsClicked = false;
			}
		}
		else
			document.getElementById(document.getElementById(MainMenuItemPanelGlobal).MenuPanelParentId).IsClicked = false;	
	}
	else
		document.getElementById(document.getElementById(MainMenuItemPanelGlobal).MenuPanelParentId).IsClicked = false;
		
	HideAllMenuItemChildren(MainMenuItemPanelGlobal, true, true);
	document.body.removeEventListener("click", HideAllMainMenuChildren, true);
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