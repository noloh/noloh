function ShowContextMenu(event, obj)
{
	var contextMenu = _N(obj.ContextMenu);
	contextMenu.style.visibility = "visible";
	contextMenu.style.left = event.pageX + "px";
	contextMenu.style.top = event.pageY + "px";
	_NContextMenuSource = obj;
	window.addEventListener("click", HideContextMenu, true);
}

function HideContextMenu(event)
{
	if(_NContextMenuSource != null)
	{
		var contextMenu = _N(_NContextMenuSource.ContextMenu);
		contextMenu.style.visibility = "hidden";
		_NContextMenu = null;
		window.removeEventListener("click", HideContextMenu, true);
	}
}