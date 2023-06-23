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
		this.TypePauseTimeout = setTimeout("var obj = _N('"+this.id+"'); _NSave(obj.id,'value',obj.value); obj.TypePause();", 500);
	}
}
function _NKeyEvntsMoTimeout(id, refocus)
{
	var btn = document.createElement('input');
	btn.type = 'button';
	btn.id = '_NTPBut';
	btn.style.visibility = 'hidden';
	btn.onclick = function(){
		_N(id).blur();
	    if(refocus)
		_N(id).focus();
		btn.parentNode.removeChild(btn);
		delete btn};
	document.body.appendChild(btn);
	//setTimeout(btn.onclick, 500);
	setTimeout("_N('_NTPBut').click();", 500);
}