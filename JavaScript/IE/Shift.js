_N.Catchers = [];
function _NShftSta(objArray)
{
	if(_N.ClickOffId)
		_NClickOffClick();
	_N.Shifts = objArray;
	_N.Shifts.Ghosts = [];
	_N.Shifts.ObjsWithStart = [];
	_N.Shifts.ObjsWithStop = [];
	_N.Shifts.ActualCount = [];
	_N.Shifts.HasMoved = false;
	_N.Shifts.CursorX = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
	_N.Shifts.CursorY = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
	document.attachEvent("onmousemove", _NShftFirstGo);
	document.attachEvent("onmouseup", _NShftStp);
	window.event.cancelBubble = true;
	window.event.returnValue = false;
}
function _NShftFirstGo()
{
	var id, obj, deltaZIndex, count = _N.Shifts.length;
	if(count > 0)
		deltaZIndex = ++_N.HighestZ + count - _N(_N.Shifts[0][0]).style.zIndex;
	for(var i=0; i<count; ++i)
	{
		obj = _N(id = _N.Shifts[i][0]);
		if(obj.ShiftStart && !_N.Shifts.ObjsWithStart[id])
		{
			obj.ShiftStart.call(obj);
			_N.Shifts.ObjsWithStart[id] = true;
		}
		if(obj.ShiftStop && !_N.Shifts.ObjsWithStop[id])
			_N.Shifts.ObjsWithStop[id] = obj;
		if(_N.Shifts[i][4])
		{
			obj = obj.cloneNode(true);
			obj.style.position = "absolute";
			obj.style.left = _NFindX(id) + "px";
			obj.style.top = _NFindY(id) + "px";
			obj.style.filter = "alpha(opacity=50)";
			_N.Shifts[i][0] = id = obj.id = id + "_Ghost";
			_N("N1").appendChild(obj);
			_N.Shifts.Ghosts.push(i);
		}
		_N.Shifts.ActualCount[_N.Shifts[i][7] = _N.Shifts[i][1] + id] = parseInt(_N.Shifts[i][1]==1?obj.style.width: _N.Shifts[i][1]==2?obj.style.height: _N.Shifts[i][1]==4?obj.style.left: obj.style.top);
		_NSetProperty(id, "style.zIndex", parseInt(obj.style.zIndex) + deltaZIndex);
	}
	delete _N.Shifts.ObjsWithStart;
	_N.Shifts.HasMoved = true;
	document.detachEvent("onmousemove", _NShftFirstGo);
	document.attachEvent("onmousemove", _NShftGo);
}
function _NShftGo()
{
	var xPos = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
	var yPos = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
	var deltaX = xPos - _N.Shifts.CursorX;
	var deltaY = yPos - _N.Shifts.CursorY;
	_N.Shifts.CursorX = xPos;
	_N.Shifts.CursorY = yPos;
	_NShftObjs(_N.Shifts, deltaX, deltaY);
	window.event.cancelBubble = true;
	window.event.returnValue = false;
}
function _NShftObjs(objects, deltaX, deltaY)
{
	var obj, tmp, count = objects.length;
	for(var i=0; i<count; ++i)
	{
		obj = _N(objects[i][0]);
		if(objects[i][1] == 1)
			_NShftProcObj(obj, objects[i], 1, "style.width", 1, (tmp=obj.style.width)?tmp:obj.offsetWidth, deltaX, (!objects[i][7]||objects[i][3])?null:(tmp=obj.style.left)?tmp:obj.offsetLeft);
		else if(objects[i][1] == 2)
			_NShftProcObj(obj, objects[i], 2, "style.height", 0, (tmp=obj.style.height)?tmp:obj.offsetHeight, deltaY, (!objects[i][7]||objects[i][3])?null:(tmp=obj.style.top)?tmp:obj.offsetTop);
		else if(objects[i][1] == 4)
			_NShftProcObj(obj, objects[i], 4, "style.left", 1, (tmp=obj.style.left)?tmp:obj.offsetLeft, deltaX, objects[i][3]?null:(tmp=obj.style.width)?tmp:obj.offsetWidth);
		else
			_NShftProcObj(obj, objects[i], 5, "style.top", 0, (tmp=obj.style.top)?tmp:tmp.offsetTop, deltaY, objects[i][3]?null:(tmp=obj.style.height)?tmp:obj.offsetHeight);
	}
}
function _NShftProcObj(obj, info, propNum, propStr, axis, startPx, delta, opposite)
{
	if(delta)
	{
		startPx = parseInt(startPx);
		var maxBound, minBound = info[2]==null?(startPx<0?null:0):(info[2]=="N"?null:info[2]), shiftsWith, tmp;
		if(opposite)
		{
			var parent = obj.parentNode;
			maxBound = (axis ? (parent.id == "N1" ? parent.Width : (tmp=parent.style.width)?parseInt(tmp):(parent.offsetWidth-(isNaN(tmp=parseInt(parent.style.borderLeftWidth))?0:(2*tmp))))
							 : (parent.id == "N1" ? parent.Height : (tmp=parent.style.height)?parseInt(tmp):(parent.offsetHeight-(isNaN(tmp=parseInt(parent.style.borderTopWidth))?0:(2*tmp))))) - parseInt(opposite);
			if(startPx > maxBound)
				maxBound = null;
		}
		else
			maxBound = info[3] == "N" ? null : info[3];
		if(info[7])
			delta = _NShftObj(info[0], propStr, startPx, delta, minBound, maxBound, info[5], info[6], _N.Shifts.ActualCount, info[7]);
		else
		{
			if(obj._NApparentCount[propNum] != startPx)
				obj._NApparentCount[propNum] = obj._NActualCount[propNum] = startPx;
			delta = _NShftObj(info[0], propStr, startPx, delta, minBound, maxBound, info[5], info[6], obj._NActualCount, propNum);
			obj._NApparentCount[propNum] += delta;
		}
		if(tmp = _N.ChangeForReflect)
		{
			var finalCoord = parseInt(propNum == 1 ? obj.style.left : obj.style.top) + (tmp=="D" ? (-delta) : tmp);
			_NSetProperty(info[0], propNum == 1 ? "style.left" : "style.top", finalCoord + "px");
			delete _N.ChangeForReflect;
		}
		if(delta)
		{
			if((shiftsWith=obj.ShiftsWith) && shiftsWith[propNum])
				_NShftObjs(shiftsWith[propNum], delta, delta);
			if(obj.ShiftStep)
				obj.ShiftStep.call(obj);
		}
	}
}
function _NShftObj(id, property, start, delta, minBound, maxBound, ratio, grid, arr, idx)
{
	var change = ratio ? (delta * ratio) : delta;
	var finalCoord = arr[idx] += change;
	finalCoord = grid ? _NRound(finalCoord, grid, start) : Math.round(finalCoord);
	if(minBound != null)
	{
		if(minBound == "R")
		{
			var last = _NRound(arr[idx]-change, grid, start);
			if(finalCoord<0 && last>=0)
				_N.ChangeForReflect = finalCoord;
			else if(finalCoord>=0 && last<0)
				_N.ChangeForReflect = start;
			else if(finalCoord < 0)
				_N.ChangeForReflect = 'D';
			finalCoord = Math.abs(finalCoord);
		}
		else if(finalCoord < minBound)
			finalCoord = minBound;
	}
	if(maxBound != null && finalCoord > maxBound)
		finalCoord = maxBound;
	_NSetProperty(id, property, finalCoord + "px");
	return finalCoord - start;
}
function _NShftStp()
{
	var count, obj;
	if((count = _N.Catchers.length) && _N.Shifts.HasMoved)
	{
		var catcher, catcherLeft, catcherTop, droppedX, droppedY, j, id, tmp, caught = [];
		_N.EventVars.Caught = [];
		droppedX = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
		droppedY = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
		for(var i=0; i<count; ++i)
			if(_NAvail(_N.Catchers[i]))
			{
				catcher = _N(_N.Catchers[i]);
				catcherX = _NFindX(_N.Catchers[i]);
				catcherY = _NFindY(_N.Catchers[i]);
				if(droppedX >= catcherX && droppedX < catcherX + ((tmp=catcher.style.width)==""?((tmp=catcher.offsetWidth)?tmp:80):parseInt(tmp)) && droppedY >= catcherY && droppedY < catcherY + ((tmp=catcher.style.height)==""?((tmp=catcher.offsetHeight)?tmp:20):parseInt(tmp)))
				{
					for(j=0; j<_N.Shifts.length; ++j)
						if(4 <= _N.Shifts[j][1] && _N.Shifts[j][1] <= 6 && _N.Catchers[i]!=(id=_N.Shifts[j][0].replace("_Ghost","")) && !caught[id])
							_N.EventVars.Caught.push(caught[id] = id);
					if(_N.EventVars.Caught.length)
					{
						catcher.DragCatch.call(catcher);
						_N.EventVars.Caught = [];
					}
				}
			}
		delete _N.EventVars.Caught;
	}
	count = _N.Shifts.Ghosts.length;
	for(i=0; i<count; ++i)
	{
		j = _N.Shifts.Ghosts[i];
		_N("N1").removeChild(_N(_N.Shifts[j][0]));
		_N.Shifts[j][0] = _N.Shifts[j][0].replace("_Ghost", "");
	}
	if(_N.Shifts.HasMoved)
	{
		for(var id in _N.Shifts.ObjsWithStop)
		{
			obj = _N.Shifts.ObjsWithStop[id];
			obj.ShiftStop.call(obj);
		}
		document.detachEvent("onmousemove", _NShftGo);
		window.event.returnValue = false;
		_N.DisableClicks = true;
		window.setTimeout(function() {delete _N.DisableClicks;}, 0);
	}
	else
	{
		count = _N.Shifts.length;
		for(i=0; i<count; ++i)
		{
			obj = _N(_N.Shifts[i][0]);
			if(obj.onclick)
				obj.onclick.call(obj);
		}
		document.detachEvent("onmousemove", _NShftFirstGo);
	}
	_N.Shifts = null;
	document.detachEvent("onmouseup", _NShftStp);
}
function _NShftWth(id)
{
	var obj = _N(id), innerObj, count = arguments.length, i=0;
	if(obj.ShiftsWith == null)
		obj.ShiftsWith = [];
	while(++i<count)
	{
		if(obj.ShiftsWith[arguments[i]] == null)
			obj.ShiftsWith[arguments[i]] = [arguments[++i]];
		else
			obj.ShiftsWith[arguments[i]].push(arguments[++i]);
		if((innerObj = _N(arguments[i][0])) && !innerObj._NActualCount)
		{
			innerObj._NActualCount = [];
			innerObj._NApparentCount = [];
		}
	}
}
function _NShftGC()
{
	for(var i=0, count=arguments.length; i < count; ++i)
	{
		var shiftsWith = _N(arguments[i]).ShiftsWith, type;
		for(type in shiftsWith)
			for(var j=0, innerCount = shiftsWith[type].length; j < innerCount; ++j)
				if(!_N(shiftsWith[type][j][0]))
				{
					shiftsWith[type].splice(j--, 1);
					--innerCount;
				}
	}
}
function _NRound(number, toTheNearest, modLeft)
{
	var mod = (modLeft?(number-modLeft):number) % toTheNearest;
	if(mod < 0)
		mod += toTheNearest;
	return (mod >= toTheNearest/2) ? (number + toTheNearest - mod) : (number - mod);
}