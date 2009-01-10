_N.Catchers = [];
function _NShftSta(event, objArray)
{
	_N.ShiftObjArray = objArray;
	_N.ShiftObjArray.Ghosts = [];
	_N.ShiftObjArray.ObjsWithStart = [];
	_N.ShiftObjArray.ObjsWithStop = [];
	_N.ShiftObjArray.ActualCount = [];
	_N.ShiftObjArray.HasMoved = false;
	_N.ShiftObjArray.CursorX = event.clientX + window.pageXOffset;
	_N.ShiftObjArray.CursorY = event.clientY + window.pageYOffset;
	document.addEventListener("mousemove", _NShftFirstGo, true);
    document.addEventListener("mouseup", _NShftStp, true);
    event.preventDefault();
}
function _NShftFirstGo()
{
	var id, obj, deltaZIndex, count = _N.ShiftObjArray.length;
	if(count > 0)
		deltaZIndex = ++_N.HighestZ + count - _N(_N.ShiftObjArray[0][0]).style.zIndex;
	for(var i=0; i<count; ++i)
	{
		obj = _N(id = _N.ShiftObjArray[i][0]);
		if(obj.ShiftStart && !_N.ShiftObjArray.ObjsWithStart[id])
		{
			obj.ShiftStart();
			_N.ShiftObjArray.ObjsWithStart[id] = true;
		}
		if(obj.ShiftStop && !_N.ShiftObjArray.ObjsWithStop[id])
			_N.ShiftObjArray.ObjsWithStop[id] = obj;
		if(_N.ShiftObjArray[i][4])
		{
			obj = obj.cloneNode(true);
			obj.style.position = "absolute";
			obj.style.left = _NFindX(id) + "px";
			obj.style.top = _NFindY(id) + "px";
			obj.style.opacity = ".5";
			_N.ShiftObjArray[i][0] = id = obj.id = id + "_Ghost";
			document.body.appendChild(obj);
			_N.ShiftObjArray.Ghosts.push(i);
		}
		_N.ShiftObjArray.ActualCount[_N.ShiftObjArray[i][7] = _N.ShiftObjArray[i][1] + id] = parseInt(_N.ShiftObjArray[i][1]==1?obj.style.width: _N.ShiftObjArray[i][1]==2?obj.style.height: _N.ShiftObjArray[i][1]==4?obj.style.left: obj.style.top);
		_NSetProperty(id, "style.zIndex", parseInt(obj.style.zIndex) + deltaZIndex);
	}
	delete _N.ShiftObjArray.ObjsWithStart;
	_N.ShiftObjArray.HasMoved = true;
	document.removeEventListener("mousemove", _NShftFirstGo, true);
	document.addEventListener("mousemove", _NShftGo, true);
}
function _NShftGo(event)
{
	var xPos = event.clientX + window.pageXOffset;
	var yPos = event.clientY + window.pageYOffset;
	var deltaX = xPos - _N.ShiftObjArray.CursorX;
	var deltaY = yPos - _N.ShiftObjArray.CursorY;
	_N.ShiftObjArray.CursorX = xPos;
	_N.ShiftObjArray.CursorY = yPos;
	_NShftObjs(_N.ShiftObjArray, deltaX, deltaY);
	event.preventDefault();
}
function _NShftObjs(objects, deltaX, deltaY)
{
	var obj, tmp, count = objects.length;
	for(var i=0; i<count; ++i)
	{
		obj = _N(objects[i][0]);
		if(objects[i][1] == 1)
			_NShftProcObj(objects[i], 1, "style.width", 1, (tmp=obj.style.width)?tmp:obj.offsetWidth, deltaX, obj.ShiftsWith, (!objects[i][7]||objects[i][3])?null:(tmp=obj.style.left)?tmp:obj.offsetLeft);
		else if(objects[i][1] == 2)
			_NShftProcObj(objects[i], 2, "style.height", 0, (tmp=obj.style.height)?tmp:obj.offsetHeight, deltaY, obj.ShiftsWith, (!objects[i][7]||objects[i][3])?null:(tmp=obj.style.top)?tmp:obj.offsetTop);
		else if(objects[i][1] == 4)
			_NShftProcObj(objects[i], 4, "style.left", 1, (tmp=obj.style.left)?tmp:obj.offsetLeft, deltaX, obj.ShiftsWith, objects[i][3]?null:(tmp=obj.style.width)?tmp:obj.offsetWidth);
		else
			_NShftProcObj(objects[i], 5, "style.top", 0, (tmp=obj.style.top)?tmp:tmp.offsetTop, deltaY, obj.ShiftsWith, objects[i][3]?null:(tmp=obj.style.height)?tmp:obj.offsetHeight);
	}
}
function _NShftProcObj(info, propNum, propStr, axis, startPx, delta, shiftsWith, opposite)
{
	if(delta)
	{
		startPx = parseInt(startPx);
		var maxBound, minBound = info[2]?info[2]:startPx<0?null:0;
		if(opposite)
		{
			var tmp, parent = _N(info[0]).parentNode;
			maxBound = (axis ? (parent.id == "N1" ? parent.Width : (tmp=parent.style.width)?parseInt(tmp):(parent.offsetWidth-(isNaN(tmp=parseInt(parent.style.borderLeftWidth))?0:(2*tmp))))
							 : (parent.id == "N1" ? parent.Height : (tmp=parent.style.height)?parseInt(tmp):(parent.offsetHeight-(isNaN(tmp=parseInt(parent.style.borderTopWidth))?0:(2*tmp))))) - parseInt(opposite);
			if(startPx > maxBound)
				maxBound = null;
		}
		else
			maxBound = info[3];
		if((delta = _NShftObj(info[0], propStr, startPx, delta, minBound, maxBound, info[5], info[6], info[7]))
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
function _NShftStp(event)
{
	var count;
	if((count = _N.Catchers.length) && _N.ShiftObjArray.HasMoved)
	{
		var Catcher, CatcherLeft, CatcherTop, DroppedX, DroppedY, j;
		_N.EventVars.Caught = [];
		DroppedX = event.clientX + window.pageXOffset;
		DroppedY = event.clientY + window.pageYOffset;
		for(var i=0; i<count; ++i)
			if(_NAvail(_N.Catchers[i]))
			{
				Catcher = _N(_N.Catchers[i]);
				CatcherX = _NFindX(_N.Catchers[i]);
				CatcherY = _NFindY(_N.Catchers[i]);
				if(DroppedX >= CatcherX && DroppedX < CatcherX + (Catcher.style.width==""?80:parseInt(Catcher.style.width,10)) && DroppedY >= CatcherY && DroppedY < CatcherY + (Catcher.style.height==""?20:parseInt(Catcher.style.height,10)))
					for(j=0; j<_N.ShiftObjArray.length; ++j)
						if(4 <= _N.ShiftObjArray[j][1] && _N.ShiftObjArray[j][1] <= 6 && _N.Catchers[i]!=_N.ShiftObjArray[j][0].replace("_Ghost",""))
							_N.EventVars.Caught.push(_N.ShiftObjArray[j][0].replace("_Ghost",""));
				if(_N.EventVars.Caught.length)
				{
					Catcher.DragCatch();
					delete _N.EventVars.Caught;
				}
			}
	}
	count = _N.ShiftObjArray.Ghosts.length;
	for(i=0; i<count; ++i)
	{
		j = _N.ShiftObjArray.Ghosts[i];
		document.body.removeChild(_N(_N.ShiftObjArray[j][0]));
		_N.ShiftObjArray[j][0] = _N.ShiftObjArray[j][0].replace("_Ghost", "");
	}
	if(_N.ShiftObjArray.HasMoved)
	{
		for(var id in _N.ShiftObjArray.ObjsWithStop)
			_N.ShiftObjArray.ObjsWithStop[id].ShiftStop();
		document.removeEventListener("mousemove", _NShftGo, true);
	}
	else
	{
		var obj;
		count = _N.ShiftObjArray.length;
		for(i=0; i<count; ++i)
		{
			obj = _N(_N.ShiftObjArray[i][0]);
			if(obj.onclick)
				obj.onclick.call(obj, event);
		}
		document.removeEventListener("mousemove", _NShftFirstGo, true);
	}
	_N.ShiftObjArray = null;
	document.removeEventListener("mouseup", _NShftStp, true);
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