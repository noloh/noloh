_NShiftObjArray = null;
function ShiftStart(event, objArray)
{
	_NShiftObjArray = objArray;
	_NShiftObjArray.Ghosts = [];
	_NShiftObjArray.ObjsWithStop = [];
	_NShiftObjArray.ActualCount = [];
	_NShiftObjArray.HasMoved = false;
	_NShiftObjArray.CursorX = event.clientX + window.pageXOffset;
	_NShiftObjArray.CursorY = event.clientY + window.pageYOffset;
	
	var obj, deltaZIndex, tmpCount = _NShiftObjArray.length;
	if(tmpCount > 0)
		deltaZIndex = ++HighestZIndex + tmpCount - _N(_NShiftObjArray[0][0]).style.zIndex;
	for(var i=0; i<tmpCount; ++i)
	{
		obj = _N(_NShiftObjArray[i][0]);
		if(obj.ShiftStart != null)
			obj.ShiftStart.call();
		if(obj.ShiftStop != null)
			_NShiftObjArray.ObjsWithStop.push(obj);
		if(_NShiftObjArray[i][4])
		{
			obj = _N(_NShiftObjArray[i][0]).cloneNode(true);
			obj.style.position = "absolute";
			obj.style.left = FindX(_NShiftObjArray[i][0]) + "px";
			obj.style.top = FindY(_NShiftObjArray[i][0]) + "px";
			obj.style.opacity = ".5";
			_NShiftObjArray[i][0] = obj.id = _NShiftObjArray[i][0] + "_Ghost";
			document.body.appendChild(obj);
			_NShiftObjArray.Ghosts.push(i);
		}
		_NShiftObjArray.ActualCount[_NShiftObjArray[i][7] = _NShiftObjArray[i][1] + _NShiftObjArray[i][0]] = parseInt(_NShiftObjArray[i][1]==1?obj.style.width: _NShiftObjArray[i][1]==2?obj.style.height: _NShiftObjArray[i][1]==4?obj.style.left: obj.style.top);
		_NSetProperty(obj.id, "style.zIndex", parseInt(obj.style.zIndex) + deltaZIndex);
	}
	document.addEventListener("mousemove", ShiftGo, true);
    document.addEventListener("mouseup", ShiftStop, true);
    event.preventDefault();
}

function ShiftGo(event)
{
	var xPos = event.clientX + window.pageXOffset;
	var yPos = event.clientY + window.pageYOffset;
	var deltaX = xPos - _NShiftObjArray.CursorX;
	var deltaY = yPos - _NShiftObjArray.CursorY;
	_NShiftObjArray.CursorX = xPos;
	_NShiftObjArray.CursorY = yPos;
	_NShiftObjArray.HasMoved = true;
	ShiftObjects(_NShiftObjArray, deltaX, deltaY);
	event.preventDefault();
}
function ShiftObjects(objects, deltaX, deltaY)
{
	var obj, count = objects.length;
	for(var i=0; i<count; ++i)
	{
		obj = _N(objects[i][0]);
		if(objects[i][1] == 1)
			ProcessShiftObject(objects[i], 1, "style.width", 1, obj.style.width, deltaX, obj.ShiftsWith, 1, (!objects[i][7]||objects[i][3])?null:(obj.style.left?obj.style.left:obj.offsetLeft));
		else if(objects[i][1] == 2)
			ProcessShiftObject(objects[i], 2, "style.height", 0, obj.style.height, deltaY, obj.ShiftsWith, 1, (!objects[i][7]||objects[i][3])?null:(obj.style.top?obj.style.top:obj.offsetTop));
		else if(objects[i][1] == 4)
			ProcessShiftObject(objects[i], 4, "style.left", 1, obj.style.left, deltaX, obj.ShiftsWith, 0, objects[i][3]?null:(obj.style.width?obj.style.width:obj.offsetWidth));
		else
			ProcessShiftObject(objects[i], 5, "style.top", 0, obj.style.top, deltaY, obj.ShiftsWith, 0, objects[i][3]?null:(obj.style.height?obj.style.height:obj.offsetHeight));
	}
}
function ProcessShiftObject(info, propNum, propStr, axis, startPx, delta, shiftsWith, defaultMin, opposite)
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
		if((delta = ShiftObject(info[0], propStr, parseInt(startPx), delta, info[2]?info[2]:defaultMin, maxBound, info[5], info[6], info[7]))
			&& shiftsWith && shiftsWith[propNum])
				ShiftObjects(shiftsWith[propNum], delta, delta);
	}
}
function ShiftObject(id, property, start, delta, minBound, maxBound, ratio, grid, actualIdx)
{
	var change = ratio ? (delta * ratio) : delta;
	var finalCoord = actualIdx ? (_NShiftObjArray.ActualCount[actualIdx] += change) : (start + change);
	if(grid)
		finalCoord = Round(finalCoord, grid);
	if(minBound != null && finalCoord < minBound)
		finalCoord = minBound;
	else if(maxBound != null && finalCoord >= maxBound)
		finalCoord = maxBound;
	_NSetProperty(id, property, Math.round(finalCoord) + "px");
	return finalCoord - start;
}
function ShiftStop(event)
{
	var tmpCount;
	if((tmpCount = NOLOHCatchers.length) && _NShiftObjArray.HasMoved)
	{
		var Catcher, CatcherLeft, CatcherTop, DroppedX, DroppedY, j;
		DroppedX = event.clientX + window.pageXOffset;
		DroppedY = event.clientY + window.pageYOffset;
		for(var i=0; i<tmpCount; ++i)
			if(IsAvailable(NOLOHCatchers[i]))
			{
				Catcher = _N(NOLOHCatchers[i]);
				CatcherX = FindX(NOLOHCatchers[i]);
				CatcherY = FindY(NOLOHCatchers[i]);
				if(DroppedX >= CatcherX && DroppedX < CatcherX + (Catcher.style.width==""?80:parseInt(Catcher.style.width,10)) && DroppedY >= CatcherY && DroppedY < CatcherY + (Catcher.style.height==""?20:parseInt(Catcher.style.height,10)))
					for(j=0; j<_NShiftObjArray.length; ++j)
						if(4 <= _NShiftObjArray[j][1] && _NShiftObjArray[j][1] <= 6 && NOLOHCatchers[i]!=_NShiftObjArray[j][0].replace("_Ghost",""))
							NOLOHCaught.push(_NShiftObjArray[j][0].replace("_Ghost",""));
				if(NOLOHCaught.length != 0)
				{
					Catcher.DragCatch();
					NOLOHCaught = [];
				}
			}
	}
	tmpCount = _NShiftObjArray.Ghosts.length;
	for(i=0; i<tmpCount; ++i)
	{
		j = _NShiftObjArray.Ghosts[i];
		document.body.removeChild(_N(_NShiftObjArray[j][0]));
		_NShiftObjArray[j][0] = _NShiftObjArray[j][0].replace("_Ghost", "");
	}
	if(!_NShiftObjArray.HasMoved)
	{
		var obj;
		tmpCount = _NShiftObjArray.length;
		for(i=0; i<tmpCount; ++i)
		{
			obj = _N(_NShiftObjArray[i][0]);
			if(obj.onclick)
				obj.onclick.call(obj, event);
		}
	}
	tmpCount = _NShiftObjArray.ObjsWithStop.length;
	for(i=0; i<tmpCount; ++i)
		_NShiftObjArray.ObjsWithStop[i].ShiftStop();
	_NShiftObjArray = null;
	document.removeEventListener("mousemove", ShiftGo, true);
	document.removeEventListener("mouseup", ShiftStop, true);
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
function Round(number, toTheNearest)
{
	var mod = number % toTheNearest;
	return (mod >= toTheNearest/2) ? (number - mod + toTheNearest) : (number - mod);
}