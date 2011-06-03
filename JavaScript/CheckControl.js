function _NChkCtrl(id, bool, over)
{
	var div = _N(id), inp = _N(id + "I");
	inp.checked = bool;
	if(div.onchange && !over)
		div.onchange();
}
function _NChkCtrlTgl(id, tgl)
{
	_NSet(id, "Selected", tgl ? !_N(id).Selected : true);
}