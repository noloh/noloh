function doKeyPress(whatObjectId, maxLength)
{
	var Obj = document.getElementById(whatObjectId);
	var oTR =  document.selection.createRange();
	if(maxLength > -1 && Obj.value.length - oTR.text.length >= maxLength)
		event.returnValue = false;
}
		        
function doPaste(whatObjectId, maxLength)
{
	var Obj = document.getElementById(whatObjectId);
    if(maxLength > -1)
	{
		var oTR =  document.selection.createRange();
		var iInsertLength = maxLength -  Obj.value.length + oTR.text.length;
		var sData = window.clipboardData.getData("Text").substr(0,iInsertLength);
		oTR.text = sData;
		event.returnValue = false;
	}
}

function SetTextAreaText(id, text)
{
	var newText = text.replace(/<Nendl>/g,"\n");
	document.getElementById(id).value = newText;
	SavedControls[id]["value"] = newText;
}