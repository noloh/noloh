function _NByntSta(id, parentId)
{
	var obj = _N(id), parent = _N(parentId);
	//Buoyant should be changed to array {"parentId":parentId, etc.}
	//obj.BuoyantParentId = parentId;
//	obj.BuoyantLeft = parseInt(obj.style.left);
//	obj.BuoyantRight = parseInt(obj.style.right);
//	obj.BuoyantTop = parseInt(obj.style.top);
//	obj.BuoyantBottom = parseInt(obj.style.bottom);
//	obj.BuoyantZIndex = obj.style.zIndex;
	obj.Buoyant = {
		"ParentId":	parentId,
		"Left":	parseInt(obj.style.left),
		"Right":parseInt(obj.style.right),
		"Top": parseInt(obj.style.top),
		"Bottom": parseInt(obj.style.bottom),
		"ZIndex": parseInt(obj.style.zIndex)};
		
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
	obj.style.left = obj.Buoyant["Left"] + "px";
	if(obj.Buoyant["Right"])
		obj.style.right = obj.Buoyant["Right"] + "px";	
	obj.style.top = obj.Buoyant["Top"] + "px";
	if(obj.Buoyant["Bottom"])
		obj.style.bottom = obj.Buoyant["Bottom"] + "px";
	obj.style.zIndex = obj.Buoyant["ZIndex"];
	obj.Buoyant = null;
	//obj.BuoyantParentId = null;
//	obj.BuoyantLeft = null;
//	obj.BuoyantRight = null;
//	obj.BuoyantTop = null;
//	obj.BuoyantBottom = null;
//	obj.BuoyantZIndex = null;
	_NByntFrgt(id, parent);
}
function _NByntMv(id)
{
	var obj = _N(id);
	if(obj)
	{
		var parent = _N(obj.Buoyant.ParentId), xProps, yProps;
//		obj.style.left = _NFindX(obj.BuoyantParentId) + (parseInt(parent.style.borderLeftWidth,10)|0) + obj.BuoyantLeft + "px";
//		obj.style.top = _NFindY(obj.BuoyantParentId) + (parseInt(parent.style.borderTopWidth,10)|0) + obj.BuoyantTop + "px";
		/*Changed | to || and added || for Bouyant Check. Single pipe will continue to evaluate.
		 For ex. try true | alert('nonsense') vs true || alert('nonsense')*/
		xProp = obj.Buoyant["Right"]?['right', 'borderRightWidth', 'Right']:['left', 'borderLeftWidth', 'Left'];
		yProp = obj.Buoyant["Bottom"]?['bottom', 'borderBottomWidth', 'Bottom']:['top', 'borderTopWidth', 'Top'];
		obj.style[xProp[0]] = _NFindX(obj.Buoyant.ParentId) + (parseInt(parent.style[xProp[1]],10)||0) + (obj.Buoyant[xProp[2]]||0) + "px";
		obj.style[yProp[0]] = _NFindY(obj.Buoyant.ParentId) + (parseInt(parent.style[yProp[1]],10)||0) + (obj.Buoyant[yProp[2]]||0) + "px";
//		obj.style.left = _NFindX(obj.Buoyant["ParentId"]) + (parseInt(parent.style.borderLeftWidth,10)||0) + (obj.BuoyantLeft||0) + "px";
//		obj.style.top = _NFindY(obj.Buoyant["ParentId"]) + (parseInt(parent.style.borderTopWidth,10)||0) + (obj.BuoyantTop||0) + "px";
	}
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