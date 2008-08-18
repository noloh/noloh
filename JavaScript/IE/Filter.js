function _NFilter(id, filter, flags)
{
	var newVal = _N(id).value + String.fromCharCode(event.keyCode);
	if (!newVal.match(new RegExp('^' + filter + '$', flags)))
		event.returnValue = false;
}