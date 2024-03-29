function _NFindX(objId, relativeId, scroll)
{
	var
		currLeft = 0,
		border = 0,
		scrollLeft,
		obj = _N(objId);

	if (obj) {
		if (obj.offsetParent) {
			while (obj.offsetParent && ((!relativeId) || (obj.id != relativeId))) {
				currLeft += obj.offsetLeft;
				obj = obj.offsetParent;
				if (!isNaN(border = parseInt(obj.style.borderLeftWidth))) {
					currLeft += border;
				}
				if (scroll === false && !isNaN(scrollLeft = parseInt(obj.scrollLeft))) {
					currLeft -= scrollLeft;
				}
			}
		}
		else if (obj.x) {
			currLeft += obj.x;
		}
	}

	return currLeft;
}
function _NFindY(objId, relativeId, scroll)
{
	var currTop = 0,
		border = 0,
		scrollTop,
		obj = _N(objId);

	if (obj) {
		if (obj.offsetParent) {
			while (obj.offsetParent && ((!relativeId) || (obj.id != relativeId))) {
				currTop += obj.offsetTop;
				obj = obj.offsetParent;
				if (!isNaN(border = parseInt(obj.style.borderTopWidth))) {
					currTop += border;
				}
				if (scroll === false && !isNaN(scrollTop = parseInt(obj.scrollTop))) {
					currTop -= scrollTop;
				}
			}
		}
		else if (obj.y) {
			currTop += obj.y;
		}
	}

	return currTop;
}