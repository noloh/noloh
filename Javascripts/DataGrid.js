function Noloh_UI_DataGrid_ScrollColumnPanel(whatTableId, whatColumnPanelId)
{
	var tmpTable = document.getElementById(whatTableId);
	var tmpColumnPanel = document.getElementById(whatColumnPanelId);
	
	if(tmpTable.ServerScroll != null)
	{
		tmpTable.ServerScroll = null;
		return;
	}
	else
	{
		if(tmpTable.TempScroll == null)
			tmpTable.TempScroll = 0;
		
		ChangeAndSave(whatColumnPanelId, "style.left", (parseInt(tmpColumnPanel.style.left, 10) - (tmpTable.scrollLeft - tmpTable.TempScroll)) + "px");
		//tmpColumnPanel.style.left = (parseInt(tmpColumnPanel.style.left, 10) - (tmpTable.scrollLeft - tmpTable.TempScroll)) + "px";//  + "px";
		ChangeAndSave(whatColumnPanelId, "style.width", (parseInt(tmpColumnPanel.style.width, 10) + (tmpTable.scrollLeft - tmpTable.TempScroll)) + "px");
	
		//tmpColumnPanel.style.width = (parseInt(tmpColumnPanel.style.width, 10) + (tmpTable.scrollLeft - tmpTable.TempScroll)) + "px"
		tmpTable.TempScroll = tmpTable.scrollLeft;
	}
}