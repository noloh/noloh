function _NFilter(event, id, filter, flags)
{
	var newVal = _N(id).value + String.fromCharCode(event.which);
	if (event.which != 8 && !newVal.match(new RegExp('^' + filter + '$', flags)))
		event.preventDefault();
}