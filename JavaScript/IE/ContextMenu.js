function _NCMShow(obj)
{
	_NCMHide();
	var id = _N.ContextMenu = obj.ContextMenu, contextMenu = _N(id);
	contextMenu.style.display = "";
	_NSet(id, "style.left", window.event.clientX + document.documentElement.scrollLeft + "px");
	_NSet(id, "style.top", window.event.clientY + document.documentElement.scrollTop + "px");
	_N.EventVars.ContextMenuSource = obj.id;
	_NClickOff(id, _NCMHide);
	_NNoBubble();
}
function _NCMHide()
{
	if(_N.ContextMenu)
	{
		if(!_N.SEQ.length)
			delete _N.EventVars.ContextMenuSource;
		delete _N.ContextMenu;
	}
}