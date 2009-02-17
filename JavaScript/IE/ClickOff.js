function _NClickOff(id, func)
{
	if(_N.ClickOffId)
		_NClickOffClick();
	_N.ClickOffId = id;
	if(typeof func == "undefined")
		_N.ClickOffFunc = func;
	document.attachEvent("onmousedown", _NClickOffDown);
	document.attachEvent("onclick", _NClickOffClick);
}
function _NClickOffDown()
{
	var to = event.srcElement;
	while(to && to.id) 
	{
		if(to.id == _N.ClickOffId) 
			return; 
		to = to.parentNode;
	}
	_NClickOffClick();
}
function _NClickOffClick()
{
	if(_N.ClickOffId)
	{
		_NSetProperty(_N.ClickOffId, "style.display", "none");
		if(_N.ClickOffFunc)
			_N.ClickOffFunc();
		document.detachEvent("onmousedown", _NClickOffDown);
		document.detachEvent("onclick", _NClickOffClick);
		delete _N.ClickOffId;
		if(_N.ClickOffFunc)
			delete _N.ClickOffFunc;
	}
}