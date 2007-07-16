SavedControls = new Array();
NOLOHChanges = new Object();
NOLOHKey = null;
NOLOHCaught = new Array();
ConversionArray = new Object();
ConversionArray["style.left"] = "Left";
ConversionArray["style.top"] = "Top";
ConversionArray["style.width"] = "Width";
ConversionArray["style.height"] = "Height";
ConversionArray["style.zIndex"] = "ZIndex";
ConversionArray["style.background"] = "BackColor";
ConversionArray["style.color"] = "Color"; 
ConversionArray["style.opacity"] = "Opacity";
ConversionArray["style.filter"] = "Opacity";
ConversionArray["value"] = "Text";
ConversionArray["newText"] = "Text";
ConversionArray["selectedIndex"] = "SelectedIndex"; 
ConversionArray["selectedTab"] = "SelectedTab"; 
ConversionArray["checked"] = "Checked";
ConversionArray["killlater"] = "KillLater";
ConversionArray["src"] = "Src";
ConversionArray["scrollLeft"] = "ScrollLeft";
ConversionArray["scrollTop"] = "ScrollTop";
ConversionArray["style.visibility"] = "ClientVisible";
ConversionArray["style.display"] = "ClientVisible";
ConversionArray["options"] = "Items";
ConversionArray["selectedIndices"] = "SelectedIndices";
ConversionArray["timer"] = "ServerVisible";
ConversionArray["calViewDate.setMonth"] = "ViewMonth";
ConversionArray["calViewDate.setFullYear"] = "ViewYear";
ConversionArray["calSelectDate.setDate"] = "Date";
ConversionArray["calSelectDate.setMonth"] = "Month";
ConversionArray["calSelectDate.setFullYear"] = "Year";
NOLOHUpload = new Object();
NOLOHUpload.FileUploadObjIds = new Array();
NOLOHVisit = -1;
HighestZIndex = 0;
LowestZIndex = 0; 

function _NInit(loadLblId, loadImgId)
{
	document.body.NOLOHPostingBack = false;
	NOLOHCatchers = Array();
	window.onscroll = BodyScrollState;
	_NLoadLbl = loadLblId;
	_NLoadImg = loadImgId;
	var Graveyard = document.createElement("DIV");
	Graveyard.id = "Graveyard";
	Graveyard.style.visibility = "hidden";
	document.body.appendChild(Graveyard);
	X = setInterval('CheckURL()', 500);
	NURL = location.toString();
}

function CheckURL()
{
	if(NURL != location)
		if(/*document.body.NOLOHPostingBack && */location.toString().indexOf('#')==location.toString().length-1)
			NURL = location.toString();
		else
			location.reload(true);
}

function SaveControl(id)
{
	var temp = document.getElementById(id);
	SavedControls[id] = temp.cloneNode(true);
	SavedControls[id].selectedIndex = temp.selectedIndex;
	SavedControls[id].checked = temp.checked;
	SavedControls[id].SelectedTab = temp.SelectedTab;
	SavedControls[id].selectedIndices = ImplodeSelectedIndices(SavedControls[id].options);
	if(temp.calViewDate != null)
	{
		SavedControls[id].calViewDate = new Object();
		SavedControls[id].calSelectDate = new Object();
		SavedControls[id].calViewDate.setMonth = temp.calViewDate.getMonth();
		SavedControls[id].calViewDate.setYear = temp.calViewDate.getYear();
		SavedControls[id].calSelectDate.setDate = temp.calSelectDate.getDate();
		SavedControls[id].calSelectDate.setMonth = temp.calSelectDate.getMonth();
		SavedControls[id].calSelectDate.setYear = temp.calSelectDate.getYear();
	}
}

function ChangeAndSave(whatDistinctId, propertyString, newValue)
{
	NOLOHChange(whatDistinctId, propertyString, newValue);
	_NSave(whatDistinctId, propertyString, newValue);
}

function NOLOHChangeInit(whatDistinctId, propertyString)
{
	if(NOLOHChanges[whatDistinctId] == null)
		NOLOHChanges[whatDistinctId] = new Object();
	if(NOLOHChanges[whatDistinctId][propertyString] == null)
		NOLOHChanges[whatDistinctId][propertyString] = new Object();	
}

function NOLOHChange(whatDistinctId, propertyString, newValue)
{
	var tempObj;
	if(propertyString != "timer")
		tempObj = document.getElementById(whatDistinctId);
	else
		eval("tempObj = window." + whatDistinctId + ";");
	NOLOHChangeByObj(tempObj, propertyString, newValue);
}

function NOLOHChangeByObj(obj, propertyString, newValue)
{
	if(obj == null)
		return;
	switch(propertyString)
	{
		case "calViewDate.setMonth":
		case "calViewDate.setFullYear":
		case "calSelectDate.setDate":
		case "calSelectDate.setMonth":
		case "calSelectDate.setFullYear":
			eval("obj." + propertyString + "(newValue);");
			break;
		case "KeyPress":
		case "ReturnKey":
		case "TypePause":
			obj.onkeypress = function(event)
			{
				_NSave(obj.id,'value',obj.value);
				if(obj.ReturnKey != null && event.keyCode == 13)
					obj.ReturnKey.call(this, event);
				if(obj.KeyPress != null)
				{
					NOLOHKey = Math.max(event.keyCode, event.charCode);
					obj.KeyPress.call(this, event);
				}
				if(obj.TypePause != null && (event.keyCode < 37 || event.keyCode > 40))
				{
					clearTimeout(obj.TypePauseTimeout);
					obj.TypePauseTimeout = setTimeout("var obj = document.getElementById('"+obj.id+"'); _NSave(obj.id,'value',obj.value); obj.TypePause.call();", 500);
				}
			}
		case "onblur":
		case "onchange":
		case "onclick":
		case "ondblclick":
		case "onelapsed":
		case "oninput":
		case "onmouseout":
		case "onmouseover":
		case "onmouseup":
		case "onload":
		case "onpaste":
		case "onscroll":
			eval("obj." + propertyString + " = function(event) {" + newValue + ";}");
			break;
		case "oncontextmenu":
			eval("obj.oncontextmenu = function(event) {" + newValue + "; return false;}");
			break;
		case "onmousedown":
			eval("obj.onmousedown = function(event) {if(obj.Shifts!=null) ShiftStart(event, obj.Shifts);" + newValue + ";}");
			break;
		case "DragCatch":
			if(newValue == "")
			{
				for(var i=0; i<NOLOHCatchers.length; i++)
					if(NOLOHCatchers[i] == obj.id)
					{
						NOLOHCatchers.splice(i, 1);
						break;
					}
			}
			else
				NOLOHCatchers.push(obj.id);
			eval("obj.DragCatch = function(event) {" + newValue + ";}");
			break;
		case "Shifts":
		case "ChildrenArray":
			eval("obj." + propertyString + " = " + newValue + ";");
			break;
		case "style.zIndex":
			if(newValue > HighestZIndex)
				HighestZIndex = newValue;
			if(newValue < LowestZIndex)
				LowestZIndex = newValue;
		default:
			eval("obj." + propertyString + " = newValue;");
	}
}

function _NSave(whatDistinctId, propertyString, newValue)
{
	if(whatDistinctId.indexOf("_") >= 0)
		return;
	NOLOHChangeInit(whatDistinctId, propertyString);
	var tempObj;
	var propertyStringLower = propertyString.toLowerCase();
	if(propertyString != "timer")
		tempObj = document.getElementById(whatDistinctId);
	else
		eval("tempObj = window." + whatDistinctId + ";");
	if(typeof newValue == "undefined")
		eval("newValue = tempObj."+propertyString+";");
	switch(propertyString)
	{
		case "style.left":
		case "style.top":
		case "style.width":
		case "style.height":
		case "style.zIndex":
			NOLOHChanges[whatDistinctId][propertyString][0] = parseInt(newValue);
			break;
		case "style.visibility":
			NOLOHChanges[whatDistinctId][propertyString][0] = (newValue == "visible");
			break;
		case "style.display":
			NOLOHChanges[whatDistinctId][propertyString][0] = (newValue == "");
			break;
		default:
			NOLOHChanges[whatDistinctId][propertyString][0] = newValue;
	}
}

function ScrollState(id)
{
	var obj = document.getElementById(id);
	ChangeAndSave(id,"scrollLeft",obj.scrollLeft);
	ChangeAndSave(id,"scrollTop",obj.scrollTop);
}

function BodyScrollState()
{
	var X = Math.max(document.body.scrollLeft, document.documentElement.scrollLeft)+1;
	var Y = Math.max(document.body.scrollTop, document.documentElement.scrollTop)+1;
	var loadImg = document.getElementById(_NLoadImg);
	loadImg.style.left = X+"px";
	loadImg.style.top = Y+"px";	
	var loadLbl = document.getElementById(_NLoadLbl);
	loadLbl.style.left = X+30+"px";
	loadLbl.style.top = Y+3+"px";
}

function AddOptionAndSave(id, option)
{
	var tempObj = document.getElementById(id);
	tempObj.options.add(option);
	_NSave(id, "options", ImplodeOptions(tempObj.options));
}

function RemoveOptionAndSave(id, index)
{
	var tempObj = document.getElementById(id);
	tempObj.remove(index);
	_NSave(id, "options", ImplodeOptions(tempObj.options));
}

function RadioButtonSave(id)
{
	var radio = document.getElementById(id);
	var radioGroup = document.getElementsByName(radio.name);
	for(var i=0; i < radioGroup.length; i++)
		_NSave(radioGroup[i].id, "checked", radioGroup[i].id == id);
}

function _NSetP(id, nameValuePairs)
{
	var i = 0;
	var obj = document.getElementById(id);
	while(i<nameValuePairs.length)
		NOLOHChangeByObj(obj, nameValuePairs[i++], nameValuePairs[i++]);
}

function _NAdd(addTo, tag, nameValuePairs)
{
	var elt = document.createElement(tag);
	elt.style.position = "absolute";
	var i = 0;
	while(i<nameValuePairs.length)
		NOLOHChangeByObj(elt, nameValuePairs[i++], nameValuePairs[i++]);
	document.getElementById(addTo).appendChild(elt);
	SaveControl(elt.id);
}

function _NRem(id)
{
	var ele = document.getElementById(id);
	ele.parentNode.removeChild(ele);
	document.getElementById("Graveyard").appendChild(ele);
}

function _NRes(id, parentId)
{
	var ele = document.getElementById(id);
	document.getElementById("Graveyard").removeChild(ele);
	document.getElementById(parentId).appendChild(ele);
}

function _NAsc(id)
{
	var ele = document.getElementById(id);
	ele.parentNode.removeChild(ele);
}

function GetChanges()
{
	var changes = "", distinctId, property;
	for(distinctId in NOLOHChanges)
		for(property in NOLOHChanges[distinctId])
			if(NOLOHChanges[distinctId][property][0] != SavedControls[distinctId][property])
			{
				SavedControls[distinctId][property] = NOLOHChanges[distinctId][property][0];
				changes += distinctId + "~d1~" + ConversionArray[property] + "~d1~" + NOLOHChanges[distinctId][property][0] + "~d0~";
				delete NOLOHChanges[distinctId][property];
			}
	return changes.substring(0,changes.length-4);
}

function processReqChange()
{
	var ready=req.readyState;
	var data=null;
	if (ready==4)
	{
   		var response = req.responseText.split("/*~NScript~*/", 2);
   		if(response[0] != "")
   		{
	   		var s = document.createElement("SCRIPT");
			s.type = "text/javascript";
			s.innerHTML = response[0];
			document.getElementsByTagName('head')[0].appendChild(s);
			eval(response[0]);
   		}
		eval(response[1]);
		document.getElementById(_NLoadImg).style.visibility = "hidden";
		document.getElementById(_NLoadLbl).style.visibility = "hidden";
		document.body.NOLOHPostingBack = false;
	}
}

function PostBack(EventType, ID, event)
{
	if(!document.body.NOLOHPostingBack)
	{
		document.body.NOLOHPostingBack = true;
		var str = "NOLOHClientChanges="+GetChanges()+"&NOLOHServerEvent="+EventType+"@"+ID+"&NOLOHVisit="+ ++NOLOHVisit;
		if(event != null)
			str += "&NOLOHMouseX="+event.pageX+"&NOLOHMouseY="+event.pageY;
		if(NOLOHKey != null)
		{
			str += "&NOLOHKey="+NOLOHKey;
			NOLOHKey = null;
		}
		if(NOLOHCaught.length != 0)
			str += "&NOLOHCaught="+NOLOHCaught.join(",");
	    req = new XMLHttpRequest();
		document.getElementById(_NLoadImg).style.visibility = "visible";
		document.getElementById(_NLoadLbl).style.visibility = "visible";
	    req.onreadystatechange = processReqChange;
	    req.open("POST", window.location.href, true);
	    req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	    req.send(str);
	}
}

function ReadyBox(id)
{
	if(NOLOHUpload.FileUploadObjIds.length > 0)
	{
		document.getElementById(id).UploadComplete = true;
		for(var i=0; i<NOLOHUpload.FileUploadObjIds.length; i++)
			if(document.getElementById(NOLOHUpload.FileUploadObjIds[i]).UploadComplete == false)
				return;
		PostBack(NOLOHUpload.EventType, NOLOHUpload.ID, NOLOHUpload.event);
	}
}

function PostBackWithUpload(EventType, ID, FileUploadObjIds, event)
{
	NOLOHUpload.EventType = EventType;
	NOLOHUpload.ID = ID;
	NOLOHUpload.FileUploadObjIds = FileUploadObjIds;
	NOLOHUpload.event = event;
	for(var i=0; i<FileUploadObjIds.length; i++)
	{
		iFrame = document.getElementById(FileUploadObjIds[i]);
		iFrame.UploadComplete = false;
		iFrame.contentWindow.document.getElementById("frm").submit();
	}
}

function ImplodeOptions(OptionsArray)
{
	var tempString ="";
	for(var i=0; i<OptionsArray.length; i++)
	{
		tempString += OptionsArray[i].value + "~d2~";
		tempString += OptionsArray[i].text + "~d3~";
	}
	
    tempString = tempString.substring(0,tempString.length-4);
	return tempString;
}

function ImplodeSelectedIndices(OptionsArray)
{
	var retString = "";
	if(OptionsArray != null)
		for(var i=0; i < OptionsArray.length; i++)
			if(OptionsArray[i].selected)
				retString += i + "~d2~";
	retString = retString.substring(0,retString.length-4);
	return retString;
}