function StartBuoyant(id, parentId)
{
	var obj = _N(id);
	var parent = _N(parentId);
	obj.BuoyantParentId = parentId;
	obj.BuoyantLeft = parseInt(obj.style.left);
	obj.BuoyantTop = parseInt(obj.style.top);
	obj.BuoyantZIndex = obj.style.zIndex;
	obj.style.zIndex = 9999;
	do
	{
		if(parent.BuoyantChildren == null)
			parent.BuoyantChildren = [];
		parent.BuoyantChildren.push(id);
		parent = parent.parentNode;
	}while (parent && parent.id);
	setTimeout(function() {MoveBuoyant(id);}, 500);
}
function StopBuoyant(id)
{
	var obj = _N(id);
	var parent = _N(obj.BuoyantParentId);
	obj.style.left = obj.BuoyantLeft + "px";
	obj.style.top = obj.BuoyantTop + "px";
	obj.style.zIndex = obj.BuoyantZIndex;
	obj.BuoyantParentId = null;
	obj.BuoyantLeft = null;
	obj.BuoyantTop = null;
	obj.BuoyantZIndex = null;
	do
	{
		if(parent.BuoyantChildren)
			parent.BuoyantChildren.splice(parent.BuoyantChildren.indexOf(id), 1);
		parent = parent.parentNode;
	}while (parent && parent.id);
}
function MoveBuoyant(id)
{
	var obj = _N(id);
	var parent = _N(obj.BuoyantParentId);
	obj.style.left = FindX(obj.BuoyantParentId) + (parseInt(parent.style.borderLeftWidth,10)|0) + obj.BuoyantLeft + "px";
	obj.style.top = FindY(obj.BuoyantParentId) + (parseInt(parent.style.borderTopWidth,10)|0) + obj.BuoyantTop + "px";
}