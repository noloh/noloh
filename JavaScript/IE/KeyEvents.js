function _NKeyEvntsPress()
{
	_NSave(this.id,'value',this.value);
	if(this.ReturnKey && window.event.keyCode == 13)
		this.ReturnKey();
	if(this.KeyPress)
	{
		_N.EventVars.Key = window.event.keyCode;
		this.KeyPress();
	}
	if(this.TypePause && (window.event.keyCode < 37 || window.event.keyCode > 40))
		_NKeyEvntsTypeTimeout.call(this);
}
function _NKeyEvntsUp()
{
	if(window.event.keyCode == 8 && this.TypePause)
		_NKeyEvntsTypeTimeout.call(this);
}
function _NKeyEvntsTypeTimeout()
{
	clearTimeout(this.TypePauseTimeout);
	this.TypePauseTimeout = setTimeout("var obj = _N('"+this.id+"'); _NSave(obj.id,'value',obj.value); obj.TypePause();", 500);
}