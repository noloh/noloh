function _NKeyEvntsPress()
{
	_NSave(this.id,'value');
	if(this.ReturnKey && event.keyCode == 13)
		this.ReturnKey();
	if(this.KeyPress)
	{
		_N.EventVars.Key = Math.max(event.keyCode, event.charCode);
		this.KeyPress();
	}
	if(this.TypePause && (event.keyCode < 37 || event.keyCode > 40))
	{
		clearTimeout(this.TypePauseTimeout);
		var _id = this.id;
		this.TypePauseTimeout = setTimeout(function() { var obj = _N(_id); _NSave(obj.id, 'value', obj.value); obj.TypePause(); }, 500);
	}
}