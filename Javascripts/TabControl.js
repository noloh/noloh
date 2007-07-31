function SetTabPage(whatTabControlId, whatTabId, whatTabPageId)
{
	var tmpTabControl = document.getElementById(whatTabControlId);
	var tmpTab = document.getElementById(whatTabId);
	var tmpTabPage = document.getElementById(whatTabPageId);
	if(tmpTabControl.SelectedTabPage == tmpTabPage.id)
		return;
	else if(tmpTabControl.SelectedTabPage != null)
	{
		var tmpPreviousTab = document.getElementById(tmpTabControl.SelectedTab);
		//tmpPreviousTab.CurrentTab = tmpPreviousTab.SelectedTabId; 
		tmpPreviousTab.Selected = false;
		ChangeRolloverTab(tmpPreviousTab.id, tmpPreviousTab.OutTabId);
		document.getElementById(tmpTabControl.SelectedTabPage).style.display = "none";
		//ChangeAndSave(tmpTabControl.SelectedTabPage, "style.display", "none");
	}
	document.getElementById(tmpTabControl.id).SelectedTab = tmpTab.id;
//	//ChangeAndSave(tmpTabControl.id, "SelectedTab", tmpTab.id); //Not Working
	tmpTabControl.SelectedTabPage = tmpTabPage.id;
	ChangeRolloverTab(tmpTab.id, tmpTab.SelectedTabId);
	tmpTab.Selected = true;
	document.getElementById(tmpTabPage.id).style.display = "";
	//ChangeAndSave(tmpTabPage.id, "style.display", "");
	if(tmpTabControl.onchange != null)
		tmpTabControl.onchange.call();
	/*if(tmpTabControl.onChange != null)
		eval(tmpTabControl.onChange);*/
}