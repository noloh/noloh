function _NCMShow(obj)
{
	var contextMenu = _N(obj.ContextMenu);
	contextMenu.style.visibility = "visible";
	contextMenu.style.left = window.event.clientX + document.documentElement.scrollLeft + "px";
	contextMenu.style.top = window.event.clientY + document.documentElement.scrollTop + "px";
	_N.EventVars.ContextMenuSource = obj.id;
	document.attachEvent("onclick", _NCMHide);
}
function _NCMHide()
{
	if(_N.EventVars.ContextMenuSource)
	{
		var contextMenu = _N(_N(_N.EventVars.ContextMenuSource).ContextMenu);
		contextMenu.style.visibility = "hidden";
		if(!_N.SEQ.length)
			delete _N.EventVars.ContextMenuSource;
		document.detachEvent("onclick", _NCMHide);
	}
}