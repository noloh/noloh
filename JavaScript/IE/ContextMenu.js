function _NCMShow(obj)
{
	var contextMenu = _N(obj.ContextMenu);
	contextMenu.style.visibility = "visible";
	contextMenu.style.left = window.event.clientX + document.documentElement.scrollLeft + "px";
	contextMenu.style.top = window.event.clientY + document.documentElement.scrollTop + "px";
	_N.ContextMenuSource = obj;
	document.attachEvent("onclick", _NCMHide);
}

function _NCMHide()
{
	if(_N.ContextMenuSource != null)
	{
		var contextMenu = _N(_N.ContextMenuSource.ContextMenu);
		contextMenu.style.visibility = "hidden";
		_N.ContextMenuSource = null;
		document.detachEvent("onclick", _NCMHide);
	}
}