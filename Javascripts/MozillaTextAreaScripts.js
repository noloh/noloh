function doKeyPress(event)
{
	Obj = event.target;
	Obj.cachedStart = Obj.selectionStart;
	Obj.cachedRight = Obj.value.substr(Obj.selectionEnd, Obj.value.length - Obj.selectionEnd);
}

function doInput(event)
{
	Obj = event.target;
	if(Obj.value.length > Obj.MaxLength && Obj.MaxLength != -1)
	{
		doKeyPress(event);
		var pos = Obj.MaxLength - Obj.cachedRight.length;
		Obj.value = Obj.value.substr(0, pos) + Obj.cachedRight;
		Obj.selectionStart = pos;
		Obj.selectionEnd = pos;
	}
}

function SetTextAreaText(id, text)
{
	var newText = text.replace(/<Nendl>/g,"\n");
	document.getElementById(id).value = newText;
	SavedControls[id]["value"] = newText;
}