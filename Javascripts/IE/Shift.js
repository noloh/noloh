function ShiftStart(objArray)
{
	var xPos, yPos, Obj;
	thisObjArray = objArray;
	thisObjArray.Ghosts = Array();
	thisObjArray.HasMoved = false;

	xPos = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
	yPos = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;

	thisObjArray.CursorStartX = xPos;
	thisObjArray.CursorStartY = yPos;

	for(var i=0; i<thisObjArray.length; i++)
	{
		if(thisObjArray[i][2] == 1)
		{
			Obj = document.getElementById(thisObjArray[i][0]).cloneNode(true);
			Obj.style.position = "absolute";
			Obj.style.left = FindX(thisObjArray[i][0]) + "px";
			Obj.style.top = FindY(thisObjArray[i][0]) + "px";
			Obj.style.filter = "alpha(opacity=50)";
			thisObjArray[i][0] = Obj.id = thisObjArray[i][0] + "_Ghost";
			document.body.appendChild(Obj);
			thisObjArray.Ghosts[thisObjArray.Ghosts.length] = i;
		}
		else
			Obj = document.getElementById(thisObjArray[i][0]);
		thisObjArray[i].StartWidth = parseInt(Obj.style.width, 10);
		thisObjArray[i].StartHeight = parseInt(Obj.style.height,  10);
		thisObjArray[i].StartLeft = parseInt(Obj.style.left, 10);
		thisObjArray[i].StartTop = parseInt(Obj.style.top,  10);
		if(isNaN(thisObjArray[i].StartHeight))
			thisObjArray[i].StartHeight = 20;
		if(isNaN(thisObjArray[i].StartTop))
			thisObjArray[i].StartTop = yPos;
		//Combine Above with SetShiftWithInitials - Asher
		if(Obj.ShiftsWith != null)
			SetShiftWithInitials(Obj.ShiftsWith);
		BringToFront(thisObjArray[i][0]);
	}
	document.attachEvent("onmousemove", ShiftObj);
	document.attachEvent("onmouseup",   ShiftStop);
	window.event.cancelBubble = true;
	window.event.returnValue = false;
}

function ShiftObj()
{
	var xPos, yPos;
	var deltaX, deltaY;

	xPos = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
	yPos = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
	deltaX = xPos - thisObjArray.CursorStartX;
	deltaY = yPos - thisObjArray.CursorStartY;
	
	thisObjArray.HasMoved = true;
	ShiftObjects(thisObjArray, deltaX, deltaY);
	window.event.cancelBubble = true;
	window.event.returnValue = false;
}
function ShiftObjects(objects, deltaX, deltaY)
{
	var tempFinalHor;
	var tempFinalVer;

	for(var i=0; i<objects.length; i++)
	{
		if(objects[i][1] == 1 || objects[i][1] == 3)
		{
			tempFinalHor = Math.round(objects[i].StartWidth + deltaX * objects[i][7]);
			
			if(objects[i][3] != null && objects[i][4] != null)
			{
				if(tempFinalHor >= objects[i][3] && tempFinalHor <= objects[i][4])
					ChangeAndSave(objects[i][0], "style.width", tempFinalHor  + "px");
				else 
					ChangeAndSave(objects[i][0], "style.width", (tempFinalHor >= objects[i][3]) ? objects[i][4] + "px" : objects[i][3] + "px");
			}
			else if(objects[i][3] != null)
				ChangeAndSave(objects[i][0], "style.width", (tempFinalHor >= objects[i][3]) ? tempFinalHor  + "px" : objects[i][3] + "px");
			else 
				ChangeAndSave(objects[i][0], "style.width",(tempFinalHor <= objects[i][4]) ? tempFinalHor  + "px" : objects[i][4] + "px");
		}
		if(objects[i][1] == 2 || objects[i][1] == 3)
		{
			tempFinalVer = Math.round(objects[i].StartHeight + deltaY * objects[i][7]);
			
			if(objects[i][5] != null && objects[i][6] != null)
			{
				if(tempFinalVer >= objects[i][5] && tempFinalVer <= objects[i][6])
					ChangeAndSave(objects[i][0], "style.height", tempFinalVer+"px");
				else 
					ChangeAndSave(objects[i][0], "style.height", (tempFinalVer >= objects[i][5]) ? objects[i][6] + "px" : objects[i][5] + "px");
			}
			else if(objects[i][5] != null)
				ChangeAndSave(objects[i][0], "style.height", (tempFinalVer >= objects[i][5]) ? tempFinalVer  + "px" : objects[i][5] + "px");
			else
				ChangeAndSave(objects[i][0], "style.height", (tempFinalVer <= objects[i][6]) ? tempFinalVer  + "px" : objects[i][6] + "px");		
		}
		if(objects[i][1] == 4 || objects[i][1] == 6)
		{
			tempFinalHor = Math.round(objects[i].StartLeft + (deltaX * objects[i][7]));
			if(objects[i][3] != null && objects[i][4] != null)
			{
				if(tempFinalHor >= objects[i][3] && tempFinalHor <= objects[i][4])
					ChangeAndSave(objects[i][0], "style.left", tempFinalHor + "px");
				else 
					ChangeAndSave(objects[i][0], "style.left", (tempFinalHor >= objects[i][3]) ? objects[i][4] + "px" : objects[i][3] + "px");
			}
			else if(objects[i][3] == null && objects[i][4] == null)
				ChangeAndSave(objects[i][0], "style.left", tempFinalHor + "px");
			else if(objects[i][3] != null)
				ChangeAndSave(objects[i][0], "style.left", (tempFinalHor >= objects[i][3]) ? tempFinalHor  + "px" : objects[i][3] + "px");
			else 
				ChangeAndSave(objects[i][0], "style.left", (tempFinalHor <= objects[i][4]) ? tempFinalHor  + "px" : objects[i][4] + "px");					
		}
		if(objects[i][1] == 5 || objects[i][1] == 6)
		{
			tempFinalVer = Math.round(objects[i].StartTop + deltaY * objects[i][7]);
			
			if(objects[i][5] != null && objects[i][6] != null)
			{
				if(tempFinalVer >= objects[i][5] && tempFinalVer <= objects[i][6])
					ChangeAndSave(objects[i][0], "style.top", tempFinalVer + "px");
				else 
					ChangeAndSave(objects[i][0], "style.top", (tempFinalVer >= objects[i][5]) ? objects[i][6] + "px" : objects[i][5] + "px");
			}
			else if(objects[i][5] == null && objects[i][6] == null)
				ChangeAndSave(objects[i][0], "style.top", tempFinalVer  + "px");
			else if(objects[i][5] != null)
				ChangeAndSave(objects[i][0], "style.top", (tempFinalVer >= objects[i][5]) ? tempFinalVer  + "px" : objects[i][5] + "px");
			else 
				ChangeAndSave(objects[i][0], "style.top", (tempFinalVer <= objects[i][6]) ? tempFinalVer  + "px" : objects[i][6] + "px");
		}
		var tmpObj = document.getElementById(objects[i][0]);
		if(tmpObj.ShiftsWith != null)
			ShiftObjects(tmpObj.ShiftsWith, deltaX, deltaY);     
	}
}
function SetShiftWithInitials(objects)
{
	for(var j=0; j<objects.length; j++)
	{
		var Obj = document.getElementById(objects[j][0]);
		objects[j].StartWidth = parseInt(Obj.style.width, 10);
		objects[j].StartHeight = parseInt(Obj.style.height,  10);
		objects[j].StartLeft = parseInt(Obj.style.left, 10);
		objects[j].StartTop = parseInt(Obj.style.top,  10);
		if(isNaN(objects[j][j].StartHeight))
			objects[j].StartHeight = 20;
		if(isNaN(objects[j].StartTop))
			objects[j].StartTop = yPos;
		if(Obj.ShiftsWith != null)
			SetShiftWithInitials(Obj.ShiftsWith);
	}
}
function ShiftStop()
{
	if(thisObjArray.HasMoved)
	{
		var Catcher, CatcherLeft, CatcherTop, DroppedX, DroppedY, j;
		DroppedX = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
		DroppedY = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
		for(var i=0; i<NOLOHCatchers.length; i++)
			if(IsAvailable(NOLOHCatchers[i]))
			{
				Catcher = document.getElementById(NOLOHCatchers[i]);
				CatcherX = FindX(NOLOHCatchers[i]);
				CatcherY = FindY(NOLOHCatchers[i]);
				if(DroppedX >= CatcherX && DroppedX < CatcherX + (Catcher.style.width==""?80:parseInt(Catcher.style.width,10)) && DroppedY >= CatcherY && DroppedY < CatcherY + (Catcher.style.height==""?20:parseInt(Catcher.style.height,10)))
					for(j=0; j<thisObjArray.length; j++)
						if(4 <= thisObjArray[j][1] && thisObjArray[j][1] <= 6 && NOLOHCatchers[i]!=thisObjArray[j][0].replace("_Ghost",""))
							NOLOHCaught.push(thisObjArray[j][0].replace("_Ghost",""));
				if(NOLOHCaught.length != 0)
				{
					Catcher.DragCatch.call();
					NOLOHCaught = Array();
				}
			}
	}
	for(i=0; i<thisObjArray.Ghosts.length; i++)
	{
		j = thisObjArray.Ghosts[i];
		document.body.removeChild(document.getElementById(thisObjArray[j][0]));
		thisObjArray[j][0] = thisObjArray[j][0].replace("_Ghost", "");
	}
	if(!thisObjArray.HasMoved)
	{
		var obj;
		for(i=0; i<thisObjArray.length; i++)
		{
			obj = document.getElementById(thisObjArray[i][0]);
			if(obj.onclick != null)
				obj.onclick.call(obj, event);
		}
	}
	thisObjArray = null;
	document.detachEvent("onmousemove", ShiftObj);
	document.detachEvent("onmouseup", ShiftStop);
}
function AddShiftWith(objectId, info)
{
	var tmpObj = document.getElementById(objectId);
	if(tmpObj.ShiftsWith == null)
		tmpObj.ShiftsWith = new Array(info);
	else	
		tmpObj.ShiftsWith[tmpObj.ShiftsWith.length] = info;
}
function ChangeShiftType(objectId, index, newType)
{
	document.getElementById(objectId).Shifts[index][1] = newType;
}