function FindX(objId)
{
	var currLeft = 0;
	var obj = document.getElementById(objId);
	if (obj.offsetParent)
		while (obj.offsetParent)
		{
			currLeft += obj.offsetLeft;
			obj = obj.offsetParent;
		}
	else if (obj.x)
		currLeft += obj.x;
	return currLeft;
}

function FindY(objId)
{
	var currTop = 0;
	var obj = document.getElementById(objId);
	if (obj.offsetParent)
		while (obj.offsetParent)
		{
			currTop += obj.offsetTop;
			obj = obj.offsetParent;
		}
	else if (obj.y)
		currTop += obj.y;
	return currTop;
}