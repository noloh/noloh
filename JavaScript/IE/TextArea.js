function _NTAPress(id, maxLength)
{
	var obj = _N(id);
	var oTR =  document.selection.createRange();
	if(maxLength > -1 && obj.value.length - oTR.text.length >= maxLength)
		event.returnValue = false;
}
function _NTAPaste(id, maxLength)
{
	var obj = _N(id);
    if(maxLength > -1)
	{
		var oTR =  document.selection.createRange();
		var iInsertLength = maxLength -  obj.value.length + oTR.text.length;
		var sData = window.clipboardData.getData("Text").substr(0,iInsertLength);
		oTR.text = sData;
		event.returnValue = false;
	}
}
function _NTATxt(id, text)
{
	var obj = _N(id);
	_N.Saved[id]["value"] = obj.value = text.replace(/<Nendl>/g,"\n");
	obj.dispatchEvent(_NCreateEvent('input'));
}