function _NTAPress()
{
	var obj = event.target;
	obj.cachedStart = obj.selectionStart;
	obj.cachedRight = obj.value.substr(obj.selectionEnd, obj.value.length - obj.selectionEnd);
}
function _NTAInput(event)
{
	var obj = event.target;
	if(obj.value.length > obj.MaxLength && obj.MaxLength != -1)
	{
		_NTAPress();
		var pos = obj.MaxLength - obj.cachedRight.length;
		obj.value = Obj.value.substr(0, pos) + obj.cachedRight;
		obj.selectionStart = pos;
		obj.selectionEnd = pos;
	}
}
function _NTATxt(id, text)
{
	var obj = _N(id);
	_N.Saved[id]["value"] = obj.value = text.replace(/<Nendl>/g,"\n");
	obj.dispatchEvent(_NCreateEvent('input'));
}