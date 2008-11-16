function _NCMShow(event, obj)
{
	var contextMenu = _N(obj.ContextMenu);
	contextMenu.style.visibility = "visible";
	contextMenu.style.left = event.pageX + "px";
	contextMenu.style.top = event.pageY + "px";
	_N.ContextMenuSource = obj;
	window.addEventListener("click", _NCMHide, true);
}
function _NCMHide(event)
{
	if(_N.ContextMenuSource)
	{
		var contextMenu = _N(_N.ContextMenuSource.ContextMenu);
		contextMenu.style.visibility = "hidden";
		_NContextMenu = null;
		window.removeEventListener("click", _NCMHide, true);
	}
}