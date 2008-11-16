function _NDPSlctDt(calid, comboid, format)
{
    _NDPShow(calid, comboid, format);
	_N(calid).style.display = 'none';
}
function _NDPShow(calid, comboid, format)
{
	var ds = _NCalDtStr(calid,format);
	_N(comboid).options[0] = new Option(ds,ds);
}
function _NDPTglOn(calId, comboId, event)
{
	var obj=_N(calId);
	if(obj.style.display == 'none')
	{
		obj.style.display = '';
		_N.CalOpened = calId;
		window.addEventListener("click", _NDPTglOff, false);
		event.stopPropagation();
	}
	_N(comboId).blur();
}
function _NDPTglOff()
{
	_N(_N.CalOpened).style.display = 'none';
	_N.CalOpened = null;
	window.removeEventListener("click", _NDPTglOff, false);
}