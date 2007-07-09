function SetRolloverTabInitialProperties(whatRolloverTab, whatOutTabId, whatSelectedTabId)
{
	var tmpTab = document.getElementById(whatRolloverTab);
	tmpTab.CurrentTab = whatOutTabId;
	tmpTab.OutTabId = whatOutTabId;
	tmpTab.SelectedTabId = whatSelectedTabId;
}
function ChangeRolloverTab(whatRolloverTab, whatTab)
{
	var tempRolloverTab = document.getElementById(whatRolloverTab);
	if(tempRolloverTab.Selected == true)
		return;
	if(document.getElementById(tempRolloverTab.CurrentTab) != null)
	{	
		document.getElementById(tempRolloverTab.CurrentTab).style.visibility = "hidden";
		document.getElementById(whatTab).style.visibility = "inherit";
		//The following is commented out so the SelectedIndex sets the tab, not the viewstate
		//ChangeAndSave(tempRolloverTab.CurrentTab, "style.visibility", "hidden");
		//ChangeAndSave(whatTab, "style.visibility", "visible");
		tempRolloverTab.CurrentTab = whatTab;
	}
}