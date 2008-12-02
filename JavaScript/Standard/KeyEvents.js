function _NKeyEvntsPress(event)
{
	_NSave(this.id,'value',this.value);
	if(this.ReturnKey && event.keyCode == 13)
		this.ReturnKey.call(this, event);
	if(this.KeyPress)
	{
		_N.EventVars.Key = Math.max(event.keyCode, event.charCode);
		this.KeyPress.call(this, event);
	}
	if(this.TypePause && (event.keyCode < 37 || event.keyCode > 40))
	{
		clearTimeout(this.TypePauseTimeout);
		this.TypePauseTimeout = setTimeout("var obj = _N('"+this.id+"'); _NSave(obj.id,'value',obj.value); obj.TypePause();", 500);
	}
}