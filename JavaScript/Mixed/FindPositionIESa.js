function _NFindX(objId)
{
	var currLeft = 0, tmpBorder;
	var obj = _N(objId);
	if (obj.offsetParent)
		while (obj.offsetParent)
		{
			currLeft += obj.offsetLeft;
			obj = obj.offsetParent;
			if(!isNaN(tmpBorder = parseInt(obj.style.borderLeftWidth)))
				currLeft += tmpBorder;
		}
	else if (obj.x)
		currLeft += obj.x;
	return currLeft;
}

function _NFindY(objId)
{
	var currTop = 0;
	var obj = _N(objId);
	if (obj.offsetParent)
		while (obj.offsetParent)
		{
			currTop += obj.offsetTop;
			obj = obj.offsetParent;
			if(!isNaN(tmpBorder = parseInt(obj.style.borderTopWidth)))
				currTop += tmpBorder;
		}
	else if (obj.y)
		currTop += obj.y;
	return currTop;
}