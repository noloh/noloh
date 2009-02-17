function _NCMShow(obj)
{
	_NCMHide();
	var id = _N.ContextMenu = obj.ContextMenu, contextMenu = _N(id);
	contextMenu.style.display = "";
	_NSetProperty(id, "style.left", event.pageX + "px");
	_NSetProperty(id, "style.top", event.pageY + "px");
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