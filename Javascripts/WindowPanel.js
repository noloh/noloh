function KillWindowLater(id)
{
	obj = _N(id);
	_NSetProperty(id, "killlater", true);
	_NSetProperty(id, "style.display", "none");
	if(obj.ShiftsWith != null)
		_N(obj.ShiftsWith).style.display = "none";
}
function SwapWindowPanelShade(winPnlId, winTtlId)
{
	var winPnl = _N(winPnlId);
	if(winPnl.ShdOpn == null || winPnl.ShdOpn)
	{
		var tmpHgt = parseInt(winPnl.style.height, 10);
		if(tmpHgt != winPnl.WinHgt)
			winPnl.WinHgt = tmpHgt;
		winPnl.style.height =  _N(winTtlId).style.height;
		winPnl.ShdOpn = false;
	}
	else
	{
		winPnl.style.height = winPnl.WinHgt + 'px';
		winPnl.ShdOpn = true;
	}
}