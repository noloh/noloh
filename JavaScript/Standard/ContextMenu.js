function _NCMShow(event, obj)
{
	var contextMenu = _N(obj.ContextMenu);
	contextMenu.style.visibility = "visible";
	contextMenu.style.left = event.pageX + "px";
	contextMenu.style.top = event.pageY + "px";
	_N.EventVars.ContextMenuSource = obj.id;
	document.addEventListener("click", _NCMHide, false);
}
function _NCMHide(event)
{
	if(_N.EventVars.ContextMenuSource)
	{
		var contextMenu = _N(_N(_N.EventVars.ContextMenuSource).ContextMenu);
		contextMenu.style.visibility = "hidden";
		if(!_N.SEQ.length)
			delete _N.EventVars.ContextMenuSource;
		window.removeEventListener("click", _NCMHide, true);
	}
}