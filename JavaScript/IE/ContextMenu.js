function ShowContextMenu(obj)
{
	var contextMenu = _N(obj.ContextMenu);
	contextMenu.style.visibility = "visible";
	contextMenu.style.left = window.event.clientX + document.documentElement.scrollLeft + "px";
	contextMenu.style.top = window.event.clientY + document.documentElement.scrollTop + "px";
	_NContextMenuSource = obj;
	document.attachEvent("onclick", HideContextMenu);
}

function HideContextMenu()
{
	if(_NContextMenuSource != null)
	{
		var contextMenu = _N(_NContextMenuSource.ContextMenu);
		contextMenu.style.visibility = "hidden";
		_NContextMenuSource = null;
		document.detachEvent("onclick", HideContextMenu);
	}
}