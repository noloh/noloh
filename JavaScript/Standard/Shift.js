_NShiftObjArray = null;
function ShiftStart(event, objArray)
{
	var xPos, yPos, obj, deltaZIndex, tempBorder;
	_NShiftObjArray = objArray;
	_NShiftObjArray.Ghosts = [];
	_NShiftObjArray.HasMoved = false;
	
	xPos = event.clientX + window.pageXOffset;
	yPos = event.clientY + window.pageYOffset;
	_NShiftObjArray.CursorStartX = xPos;
	_NShiftObjArray.CursorStartY = yPos;
	
	var tmpCount = _NShiftObjArray.length;
	if(tmpCount > 0)
		deltaZIndex = ++HighestZIndex + tmpCount - _N(_NShiftObjArray[0][0]).style.zIndex;
	for(var i=0; i<tmpCount; ++i)
	{
		if(_NShiftObjArray[i][2] == 1)
		{
			obj = _N(_NShiftObjArray[i][0]).cloneNode(true);
			obj.style.position = "absolute";
			obj.style.left = FindX(_NShiftObjArray[i][0]) + "px";
			obj.style.top = FindY(_NShiftObjArray[i][0]) + "px";
			obj.style.opacity = ".5";
			_NShiftObjArray[i][0] = obj.id = _NShiftObjArray[i][0] + "_Ghost";
			document.body.appendChild(obj);
			_NShiftObjArray.Ghosts[_NShiftObjArray.Ghosts.length] = i;
		}
		else
			obj = _N(_NShiftObjArray[i][0]);
		_NShiftInitObject(_NShiftObjArray[i], obj);
		SetShiftWithInitials(obj);
		_NSetProperty(obj.id, "style.zIndex", parseInt(obj.style.zIndex) + deltaZIndex);
	}
	document.addEventListener("mousemove", ShiftGo, true);
    document.addEventListener("mouseup", ShiftStop, true);
    event.preventDefault();
}
function _NShiftInitObject(info, obj)
{
	info.StartWidth = parseInt(obj.style.width);
	info.StartHeight = parseInt(obj.style.height);
	info.StartLeft = parseInt(obj.style.left);
	info.StartTop = parseInt(obj.style.top);
	
	if(isNaN(info.StartHeight))
		info.StartHeight = 20;
	if(isNaN(info.StartTop))
		info.StartTop = obj.offsetLeft;
		//info.StartTop = yPos;
}
function SetShiftWithInitials(obj)
{	
	if(obj.ShiftsWith != null)
	{
		var tmpCount = obj.ShiftsWith.length;
		var subObject;
		for(var j=0; j<tmpCount; ++j)
		{
			subObject = _N(obj.ShiftsWith[j][0]);
			_NShiftInitObject(obj.ShiftsWith[j], subObject);
			SetShiftWithInitials(subObject);
		}
	}
}
function ShiftGo(event)
{
	var xPos, yPos;
	var deltaX, deltaY;

	xPos = event.clientX + window.pageXOffset;
    yPos = event.clientY + window.pageYOffset;
	deltaX = xPos - _NShiftObjArray.CursorStartX;
	deltaY = yPos - _NShiftObjArray.CursorStartY;
	
	_NShiftObjArray.HasMoved = true;
	ShiftObjects(_NShiftObjArray, deltaX, deltaY);
	event.preventDefault();
}
function ShiftObjects(objects, deltaX, deltaY, lastShift)
{
	var tmpObj, tmpCount = objects.length;
	for(var i=0; i<tmpCount; ++i)
	{
		if(lastShift==null || objects[i][8]==null || lastShift==objects[i][8])
		{
			if(objects[i][1] > 3)
			{
				if(objects[i][1] != 4 && deltaY != null)
					ShiftObject(objects[i][0], "style.top", objects[i].StartTop, deltaY, objects[i][3], objects[i][6], objects[i][7]);
				if(objects[i][1] != 5 && deltaX != null)
					ShiftObject(objects[i][0], "style.left", objects[i].StartLeft, deltaX, objects[i][3], objects[i][4], objects[i][5]);
			}
			else
			{
				if(objects[i][1] != 1 && deltaY != null)
					ShiftObject(objects[i][0], "style.height", objects[i].StartHeight, deltaY, objects[i][3], objects[i][6], objects[i][7]);
				if(objects[i][1] != 2 && deltaX != null)
					ShiftObject(objects[i][0], "style.width", objects[i].StartWidth, deltaX, objects[i][3], objects[i][4], objects[i][5]);
			}
			tmpObj = _N(objects[i][0]);
			if(tmpObj.ShiftsWith != null)
				ShiftObjects(tmpObj.ShiftsWith, deltaX, deltaY, objects[i][1]);
		}
	}
}
function ShiftObject(id, property, start, delta, ratio, minBound, maxBound)
{
	var finalCoord = Math.round(start + delta * ratio);
	_NSetProperty(id, property, (minBound != null && finalCoord <= minBound ? minBound : (maxBound != null && finalCoord >= maxBound ? maxBound : finalCoord))+"px");
}
function ShiftStop(event)
{
	var tmpCount;
	if(_NShiftObjArray.HasMoved)
	{
		var Catcher, CatcherLeft, CatcherTop, DroppedX, DroppedY, j;
		DroppedX = event.clientX + window.pageXOffset;
		DroppedY = event.clientY + window.pageYOffset;
		tmpCount = NOLOHCatchers.length;
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
					Catcher.DragCatch.call();
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
			if(obj.onclick != null)
				obj.onclick.call(obj, event);
		}
	}
	_NShiftObjArray = null;
	document.removeEventListener("mousemove", ShiftGo, true);
	document.removeEventListener("mouseup", ShiftStop, true);
}
function AddShiftWith(objectId, info)
{
	var tmpObj = _N(objectId);
	if(tmpObj.ShiftsWith == null)
		tmpObj.ShiftsWith = [info];
	else	
		tmpObj.ShiftsWith.push(info);
}
function ChangeShiftType(objectId, index, newType)
{
	_N(objectId).Shifts[index][1] = newType;
}