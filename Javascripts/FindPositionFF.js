function FindX(objId)
{
	var currLeft = 0, lastBorder = 0;
	var obj = document.getElementById(objId);
	if (obj.offsetParent)
		while (obj.offsetParent)
		{
			currLeft += obj.offsetLeft;
			obj = obj.offsetParent;
			if(obj.style.borderLeftWidth)
				currLeft += lastBorder = parseInt(obj.style.borderLeftWidth,10);
		}
	else if (obj.x)
		currLeft += obj.x;
	return currLeft + lastBorder;
}

function FindY(objId)
{
	var currTop = 0, lastBorder = 0;
	var obj = document.getElementById(objId);
	if (obj.offsetParent)
		while (obj.offsetParent)
		{
			currTop += obj.offsetTop;
			obj = obj.offsetParent;
			if(obj.style.borderTopWidth)
				currTop += lastBorder = parseInt(obj.style.borderTopWidth,10);
		}
	else if (obj.y)
		currTop += obj.y;
	return currTop + lastBorder;
}