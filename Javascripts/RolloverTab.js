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
	if(tempRolloverTab.Selected)
		return;
	if(document.getElementById(tempRolloverTab.CurrentTab) != null)
	{	
		document.getElementById(tempRolloverTab.CurrentTab).style.visibility = "hidden";
		document.getElementById(whatTab).style.visibility = "inherit";
		tempRolloverTab.CurrentTab = whatTab;
	}
}