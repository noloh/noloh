function _NCMShow(obj, e)
{
	var evt = event || e;
	_NCMHide();
	var id = _N.ContextMenu = obj.ContextMenu,
		contextMenu = _N(id),
		top = evt.pageY;
	if (contextMenu.alignBottom) {
		top -= $('#' + id).outerHeight();
	}
	contextMenu.style.display = "";
	_NSet(id, "style.left", evt.pageX + "px");
	_NSet(id, "style.top", top + "px");
	_N.EventVars.ContextMenuSource = obj.id;
	_NClickOff(id, _NCMHide, evt);
	_NNoBubble();
}
function _NCMHide()
{
	if(_N.ContextMenu)
	{
		if(!_N.SEQ.length)
			delete _N.EventVars.ContextMenuSource;
		delete _N.ContextMenu;
		delete _N.ClickOffId;
	}
}