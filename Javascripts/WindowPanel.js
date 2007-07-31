function KillWindowLater(id)
{
	obj = document.getElementById(id);
	ChangeAndSave(id, "killlater", true);
	ChangeAndSave(id, "style.display", "none");
	if(obj.ShiftsWith != null)
		document.getElementById(obj.ShiftsWith).style.display = "none";
		//ChangeAndSave(obj.ShiftsWith, "style.display", "none");
}

function SwapWindowPanelShade(windowPanelId, windowPanelTitleBarId)
{
	tmpWindowPanel = document.getElementById(windowPanelId);
	tmpTitleBar = document.getElementById(windowPanelTitleBarId);
}