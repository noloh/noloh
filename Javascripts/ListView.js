function Noloh_UI_ListView_ScrollColumnPanel(listViewId, columnPanelId)
{
	var listView = document.getElementById(listViewId);
	var columnPanel = document.getElementById(columnPanelId);
	ChangeAndSave(columnPanelId, 'style.left', -(parseInt(listView.scrollLeft)) + "px");
	ChangeAndSave(columnPanelId, 'style.width', (parseInt(columnPanel.style.width) + (parseInt(listView.scrollLeft))) + "px");
}