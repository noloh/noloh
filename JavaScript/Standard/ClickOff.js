function _NClickOff(id, func, e)
{
	var evt = event || e;
	if(_N.ClickOffId)
		_NClickOffClick(evt);
	_N.ClickOffId = id;
	if(func)
		_N.ClickOffFunc = func;
	document.addEventListener("mousedown", _NClickOffDown, false);
	document.addEventListener("click", _NClickOffClick, false);
}
function _NClickOffDown(e)
{
	var to = e.target;
	while(to && to.id) 
	{
		if(to.id == _N.ClickOffId) 
			return; 
		to = to.parentNode;
	}
	_NClickOffClick(e);
}
function _NClickOffClick(e)
{
	if(_N.ClickOffId)
	{
		_NSet(_N.ClickOffId, "style.display", "none");
		event = e;
		if(_N.ClickOffFunc)
			_N.ClickOffFunc();
		document.removeEventListener("mousedown", _NClickOffDown, false);
		document.removeEventListener("click", _NClickOffClick, false);
		delete _N.ClickOffId;
		if(_N.ClickOffFunc)
			delete _N.ClickOffFunc;
		if(!_N.EventDepth)
			event = null;
	}
}