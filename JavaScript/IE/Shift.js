_N.Catchers = [];
function _NShftSta(objArray)
{
	_N.ShiftObjArray = objArray;
	_N.ShiftObjArray.Ghosts = [];
	_N.ShiftObjArray.ObjsWithStop = [];
	_N.ShiftObjArray.ActualCount = [];
	_N.ShiftObjArray.HasMoved = false;
	_N.ShiftObjArray.CursorX = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
	_N.ShiftObjArray.CursorY = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
	
	var obj, deltaZIndex, tmpCount = _N.ShiftObjArray.length;
	if(tmpCount > 0)
		deltaZIndex = ++_N.HighestZ + tmpCount - _N(_N.ShiftObjArray[0][0]).style.zIndex;
	for(var i=0; i<tmpCount; ++i)
	{
		obj = _N(_N.ShiftObjArray[i][0]);
		if(obj.ShiftStart)
			obj.ShiftStart();
		if(obj.ShiftStop)
			_N.ShiftObjArray.ObjsWithStop.push(obj);
		if(_N.ShiftObjArray[i][4])
		{
			obj = _N(_N.ShiftObjArray[i][0]).cloneNode(true);
			obj.style.position = "absolute";
			obj.style.left = _NFindX(_N.ShiftObjArray[i][0]) + "px";
			obj.style.top = _NFindY(_N.ShiftObjArray[i][0]) + "px";
			obj.style.filter = "alpha(opacity=50)";
			_N.ShiftObjArray[i][0] = obj.id = _N.ShiftObjArray[i][0] + "_Ghost";
			_N("N1").appendChild(obj);
			_N.ShiftObjArray.Ghosts.push(i);
		}
		_N.ShiftObjArray.ActualCount[_N.ShiftObjArray[i][7] = _N.ShiftObjArray[i][1] + _N.ShiftObjArray[i][0]] = parseInt(_N.ShiftObjArray[i][1]==1?obj.style.width: _N.ShiftObjArray[i][1]==2?obj.style.height: _N.ShiftObjArray[i][1]==4?obj.style.left: obj.style.top);
		_NSetProperty(obj.id, "style.zIndex", parseInt(obj.style.zIndex) + deltaZIndex);
	}
	document.attachEvent("onmousemove", _NShftGo);
	document.attachEvent("onmouseup", _NShftStp);
	window.event.cancelBubble = true;
	window.event.returnValue = false;
}
function _NShftGo()
{
	var xPos = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
	var yPos = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
	var deltaX = xPos - _N.ShiftObjArray.CursorX;
	var deltaY = yPos - _N.ShiftObjArray.CursorY;
	_N.ShiftObjArray.CursorX = xPos;
	_N.ShiftObjArray.CursorY = yPos;
	_N.ShiftObjArray.HasMoved = true;
	_NShftObjs(_N.ShiftObjArray, deltaX, deltaY);
	window.event.cancelBubble = true;
	window.event.returnValue = false;
}
function _NShftObjs(objects, deltaX, deltaY)
{
	var obj, count = objects.length;
	for(var i=0; i<count; ++i)
	{
		obj = _N(objects[i][0]);
		if(objects[i][1] == 1)
			_NShftProcObj(objects[i], 1, "style.width", 1, obj.style.width, deltaX, obj.ShiftsWith, 1, (!objects[i][7]||objects[i][3])?null:obj.style.left?obj.style.left:obj.offsetLeft);
		else if(objects[i][1] == 2)
			_NShftProcObj(objects[i], 2, "style.height", 0, obj.style.height, deltaY, obj.ShiftsWith, 1, (!objects[i][7]||objects[i][3])?null:obj.style.top?obj.style.top:obj.offsetTop);
		else if(objects[i][1] == 4)
			_NShftProcObj(objects[i], 4, "style.left", 1, obj.style.left, deltaX, obj.ShiftsWith, 0, objects[i][3]?null:obj.style.width?obj.style.width:obj.offsetWidth);
		else
			_NShftProcObj(objects[i], 5, "style.top", 0, obj.style.top, deltaY, obj.ShiftsWith, 0, objects[i][3]?null:obj.style.height?obj.style.height:obj.offsetHeight);
	}
}
function _NShftProcObj(info, propNum, propStr, axis, startPx, delta, shiftsWith, defaultMin, opposite)
{
	if(delta)
	{
		var maxBound;
		if(opposite)
		{
			var parent = _N(info[0]).parentNode;
			maxBound = Math.max((axis ? (parent.id == "N1" ? parent.Width : (parent.style.width?parseInt(parent.style.width):(parent.offsetWidth-(!isNaN(parseInt(parent.style.borderLeftWidth))?2*parseInt(parent.style.borderLeftWidth):0)))) : (parent.id == "N1" ? parent.Height : (parent.style.height?parseInt(parent.style.height):(parent.offsetHeight-(!isNaN(parseInt(parent.style.borderTopWidth))?2*parseInt(parent.style.borderTopWidth):0))))) - parseInt(opposite), defaultMin);
		}
		else
			maxBound = info[3];
		if((delta = _NShftObj(info[0], propStr, parseInt(startPx), delta, info[2]?info[2]:defaultMin, maxBound, info[5], info[6], info[7]))
			&& shiftsWith && shiftsWith[propNum])
				_NShftObjs(shiftsWith[propNum], delta, delta);
	}
}
function _NShftObj(id, property, start, delta, minBound, maxBound, ratio, grid, actualIdx)
{
	var change = ratio ? (delta * ratio) : delta;
	var finalCoord = actualIdx ? (_N.ShiftObjArray.ActualCount[actualIdx] += change) : (start + change);
	if(grid)
		finalCoord = _NRound(finalCoord, grid);
	if(minBound != null && finalCoord < minBound)
		finalCoord = minBound;
	else if(maxBound != null && finalCoord >= maxBound)
		finalCoord = maxBound;
	_NSetProperty(id, property, Math.round(finalCoord) + "px");
	return finalCoord - start;
}
function _NShftStp()
{
	var tmpCount;
	if((tmpCount = _N.Catchers.length) && _N.ShiftObjArray.HasMoved)
	{
		var Catcher, CatcherLeft, CatcherTop, DroppedX, DroppedY, j;
		DroppedX = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
		DroppedY = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
		for(var i=0; i<tmpCount; ++i)
			if(_NAvail(_N.Catchers[i]))
			{
				Catcher = _N(_N.Catchers[i]);
				CatcherX = _NFindX(_N.Catchers[i]);
				CatcherY = _NFindY(_N.Catchers[i]);
				if(DroppedX >= CatcherX && DroppedX < CatcherX + (Catcher.style.width==""?80:parseInt(Catcher.style.width,10)) && DroppedY >= CatcherY && DroppedY < CatcherY + (Catcher.style.height==""?20:parseInt(Catcher.style.height,10)))
					for(j=0; j<_N.ShiftObjArray.length; ++j)
						if(4 <= _N.ShiftObjArray[j][1] && _N.ShiftObjArray[j][1] <= 6 && _N.Catchers[i]!=_N.ShiftObjArray[j][0].replace("_Ghost",""))
							_N.Caught.push(_N.ShiftObjArray[j][0].replace("_Ghost",""));
				if(_N.Caught.length != 0)
				{
					Catcher.DragCatch();
					_N.Caught = [];
				}
			}
	}
	tmpCount = _N.ShiftObjArray.Ghosts.length;
	for(i=0; i<tmpCount; ++i)
	{
		j = _N.ShiftObjArray.Ghosts[i];
		_N("N1").removeChild(_N(_N.ShiftObjArray[j][0]));
		_N.ShiftObjArray[j][0] = _N.ShiftObjArray[j][0].replace("_Ghost", "");
	}
	
	if(!_N.ShiftObjArray.HasMoved)
	{
		var obj;
		tmpCount = _N.ShiftObjArray.length;
		for(i=0; i<tmpCount; ++i)
		{
			obj = _N(_N.ShiftObjArray[i][0]);
			if(obj.onclick)
				obj.onclick.call(obj, event);
		}
	}
	tmpCount = _N.ShiftObjArray.ObjsWithStop.length;
	for(i=0; i<tmpCount; ++i)
		_N.ShiftObjArray.ObjsWithStop[i].ShiftStop();
	_N.ShiftObjArray = null;
	document.detachEvent("onmousemove", _NShftGo);
	document.detachEvent("onmouseup", _NShftStp);
}
function _NShftWth(objectId)
{
	var tmpObj = _N(objectId), count = arguments.length, i=0;
	if(tmpObj.ShiftsWith == null)
		tmpObj.ShiftsWith = [];
	while(++i<count)
		if(tmpObj.ShiftsWith[arguments[i]] == null)
			tmpObj.ShiftsWith[arguments[i]] = [arguments[++i]];
		else
			tmpObj.ShiftsWith[arguments[i]].push(arguments[++i]);
}
function _NRound(number, toTheNearest)
{
	var mod = number % toTheNearest;
	return (mod >= toTheNearest/2) ? (number - mod + toTheNearest) : (number - mod);
}