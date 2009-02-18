function _NFilter(id, filter, flags)
{
	if(event.charCode)
	{
		var newVal = _N(id).value + String.fromCharCode(event.charCode);
		if (event.which != 8 && !newVal.match(new RegExp('^' + filter + '$', flags)))
			event.preventDefault();
	}
}