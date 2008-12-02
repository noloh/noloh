function _NCMShow(event, obj)
{
	var contextMenu = _N(obj.ContextMenu);
	contextMenu.style.visibility = "visible";
	contextMenu.style.left = event.pageX + "px";
	contextMenu.style.top = event.pageY + "px";
	_N.EventVars.ContextMenuSource = obj.id;
	window.addEventListener("click", _NCMHide, true);
}
function _NCMHide(event)
{
	if(_N.EventVars.ContextMenuSource)
	{
		var contextMenu = _N(_N(_N.EventVars.ContextMenuSource).ContextMenu);
		contextMenu.style.visibility = "hidden";
		_N.EventVars.ContextMenuSource = null;
		window.removeEventListener("click", _NCMHide, true);
	}
}