function FindX(objId)
{
	var currLeft = 0, lastBorder = 0, tmpBorder;
	var obj = _N(objId);
	if (obj.offsetParent)
		while (obj.offsetParent)
		{
			currLeft += obj.offsetLeft;
			obj = obj.offsetParent;
			if(!isNaN(tmpBorder = parseInt(obj.style.borderLeftWidth,10)))
				currLeft += lastBorder = tmpBorder;
		}
	else if (obj.x)
		currLeft += obj.x;
	return currLeft + lastBorder;
}

function FindY(objId)
{
	var currTop = 0, lastBorder = 0;
	var obj = _N(objId);
	if (obj.offsetParent)
		while (obj.offsetParent)
		{
			currTop += obj.offsetTop;
			obj = obj.offsetParent;
			if(!isNaN(tmpBorder = parseInt(obj.style.borderTopWidth,10)))
				currTop += lastBorder = tmpBorder;
		}
	else if (obj.y)
		currTop += obj.y;
	return currTop + lastBorder;
}