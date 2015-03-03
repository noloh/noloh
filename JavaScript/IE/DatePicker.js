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
function _NDPTglOn(calId)
{
	var obj=_N(calId);
	if(obj.style.display == 'none')
	{
		obj.style.display = '';
		_N.CalOpened = calId;
		document.attachEvent("onclick", _NDPTglOff);
		window.event.cancelBubble = true;
	}
	document.body.focus();
}
function _NDPTglOff()
{
	_N(_N.CalOpened).style.display = 'none';
	_N.CalOpened = null;
	document.detachEvent("onclick", _NDPTglOff);
}