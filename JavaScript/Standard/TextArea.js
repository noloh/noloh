function _NTAPress(event)
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
		_NTAPress(event);
		var pos = obj.MaxLength - obj.cachedRight.length;
		obj.value = Obj.value.substr(0, pos) + obj.cachedRight;
		obj.selectionStart = pos;
		obj.selectionEnd = pos;
	}
}
function _NTATxt(id, text)
{
	_N.Saved[id]["value"] = _N(id).value = text.replace(/<Nendl>/g,"\n");
}