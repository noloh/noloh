function _NFindX(objId, relativeId)
{
	var currLeft = 0, obj = _N(objId);
	if (obj.offsetParent)
//		while (obj.offsetParent)
		while (obj.offsetParent && ((!relativeId) || (obj.id != relativeId)))
		{
			currLeft += obj.offsetLeft;
			obj = obj.offsetParent;
		}
	else if (obj.x)
		currLeft += obj.x;
	return currLeft;
}
function _NFindY(objId, relativeId)
{
	var currTop = 0, obj = _N(objId);
	if (obj.offsetParent)
//		while (obj.offsetParent)
		while (obj.offsetParent && ((!relativeId) || (obj.id != relativeId)))
		{
			currTop += obj.offsetTop;
			obj = obj.offsetParent;
		}
	else if (obj.y)
		currTop += obj.y;
	return currTop;
}