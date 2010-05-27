function _NByntSta(id, parentId)
{
	var obj = _N(id), parent = _N(parentId);
	obj.BuoyantParentId = parentId;
	obj.BuoyantLeft = parseInt(obj.style.left);
	obj.BuoyantTop = parseInt(obj.style.top);
	obj.BuoyantZIndex = obj.style.zIndex;
	obj.style.zIndex = 9999;
	do
	{
		if(parent.BuoyantChildren == null)
			parent.BuoyantChildren = [id];
		else
			parent.BuoyantChildren.push(id);
		parent = parent.parentNode;
	}while (parent && parent.id);
	if(!_N.BuoyantStartMoveQueue)
	{
		_N.BuoyantStartMoveQueue = [id];
		setTimeout(function() {_NByntStaQ();}, 500);
	}
	else
		_N.BuoyantStartMoveQueue.push(id);
	
}
function _NByntStp(id)
{
	var obj = _N(id), parent = _N(obj.BuoyantParentId);
	obj.style.left = obj.BuoyantLeft + "px";
	obj.style.top = obj.BuoyantTop + "px";
	obj.style.zIndex = obj.BuoyantZIndex;
	obj.BuoyantParentId = null;
	obj.BuoyantLeft = null;
	obj.BuoyantTop = null;
	obj.BuoyantZIndex = null;
	_NByntFrgt(id, parent);
}
function _NByntMv(id)
{
	var obj = _N(id), parent = _N(obj.BuoyantParentId);
	obj.style.left = _NFindX(obj.BuoyantParentId) + (parseInt(parent.style.borderLeftWidth,10)|0) + obj.BuoyantLeft + "px";
	obj.style.top = _NFindY(obj.BuoyantParentId) + (parseInt(parent.style.borderTopWidth,10)|0) + obj.BuoyantTop + "px";
}
function _NByntMvCh(obj)
{
	var count = obj.BuoyantChildren.length;
	for(var i=0; i<count; ++i)
		_NByntMv(obj.BuoyantChildren[i]);
}
function _NByntFrgt(id, parent)
{
	do
	{
		if(parent.BuoyantChildren)
			for(var i=0, count=parent.BuoyantChildren.length; i<count; ++i)
				if(parent.BuoyantChildren[i] == id)
				{
					parent.BuoyantChildren.splice(i, 1);
					break;
				}
//			parent.BuoyantChildren.splice(parent.BuoyantChildren.indexOf(id), 1);
		parent = parent.parentNode;
	}while (parent && parent.id);
}
function _NByntStaQ()
{
	var count = _N.BuoyantStartMoveQueue.length;
	for(var i=0; i<count; ++i)
		_NByntMv(_N.BuoyantStartMoveQueue[i]);
	_N.BuoyantStartMoveQueue = null;
}