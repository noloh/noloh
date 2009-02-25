function _NChkCtrl(id, bool)
{
	var div = _N(id), inp = _N(id + "I");
	inp.checked = bool;
	if(div.onchange)
		div.onchange();
}
function _NChkCtrlTgl(id, tgl)
{
	_NSetProperty(id, "Selected", tgl ? !_N(id).Selected : true);
}