function SetTabPage(tabControlId, tabId, tabPageId, server)
{
	var tmpTabControl = document.getElementById(tabControlId);
	var tmpTab = document.getElementById(tabId);
	var tmpTabPage = document.getElementById(tabPageId);
	if(tmpTabControl.SelectedTabPage == tmpTabPage.id)
		return;
	else if(tmpTabControl.SelectedTabPage != null)
	{
		var tmpPreviousTab = document.getElementById(tmpTabControl.SelectedTab);
		tmpPreviousTab.Selected = false;
		ChangeRolloverTab(tmpPreviousTab.id, tmpPreviousTab.OutTabId);
		ChangeAndSave(tmpTabControl.SelectedTabPage, 'style.display', 'none');
	}
	ChangeAndSave(tmpTabControl.id, "SelectedTab", tmpTab.id); //Not Working
	tmpTabControl.SelectedTabPage = tmpTabPage.id;
	ChangeRolloverTab(tmpTab.id, tmpTab.SelectedTabId);
	tmpTab.Selected = true;
	ChangeAndSave(tmpTabPage.id, 'style.display', '');
	if(server == 1)
		if(tmpTabControl.onchange != null)
			tmpTabControl.onchange.call();
}