function _NStrPnlAdd(pnl, obj)
{
	var panel = _N(pnl);
	var object = _N(obj);
	var bottom = object.offsetTop + object.offsetHeight;
	//console.log(bottom);
	if(bottom > panel.offsetHeight)
		_NSetProperty(pnl, 'style.height', bottom + 'px');
}