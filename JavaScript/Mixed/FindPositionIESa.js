function _NFindX(objId, relativeId)
{
	var currLeft = 0, border = 0, obj = _N(objId);
	if (obj.offsetParent)
//		while (obj.offsetParent)
		while (obj.offsetParent && ((!relativeId) || (obj.id != relativeId)))
		{
			currLeft += obj.offsetLeft;
			obj = obj.offsetParent;
			if(!isNaN(border = parseInt(obj.style.borderLeftWidth)))
				currLeft += border;
		}
	else if (obj.x)
		currLeft += obj.x;
	return currLeft;
//	return relativeId?currLeft - border:currLeft;
}
function _NFindY(objId, relativeId)
{
	var currTop = 0, border = 0, obj = _N(objId);
	if (obj.offsetParent)
//		while (obj.offsetParent)
		while (obj.offsetParent && ((!relativeId) || (obj.id != relativeId)))
		{
			currTop += obj.offsetTop;
			obj = obj.offsetParent;
			if(!isNaN(border = parseInt(obj.style.borderTopWidth)))
				currTop += border;
		}
	else if (obj.y)
		currTop += obj.y;
	return currTop;
//	return relativeId?currTop - border:currTop;
}