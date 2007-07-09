function Noloh_UI_ListView_ScrollColumnPanel(listViewId, columnPanelId)
{
	var listView = document.getElementById(listViewId);
	ChangeAndSave(columnPanelId, 'style.left', -(parseInt(listView.scrollLeft)) + "px");
}