function KillWindowLater(id)
{
	obj = document.getElementById(id);
	ChangeAndSave(id, "killlater", true);
	ChangeAndSave(id, "style.display", "none");
	if(obj.ShiftsWith != null)
		document.getElementById(obj.ShiftsWith).style.display = "none";
}
function SwapWindowPanelShade(winPnlId, winTtlId)
{
	var winPnl = document.getElementById(winPnlId);
	if(winPnl.ShdOpn == null || winPnl.ShdOpn)
	{
		var tmpHgt = parseInt(winPnl.style.height, 10);
		if(tmpHgt != winPnl.WinHgt)
			winPnl.WinHgt = tmpHgt;
		winPnl.style.height =  document.getElementById(winTtlId).style.height;
		winPnl.ShdOpn = false;
	}
	else
	{
		winPnl.style.height = winPnl.WinHgt + 'px';
		winPnl.ShdOpn = true;
	}
}