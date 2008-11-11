/*! Copyright (c) 2005 - 2008 NOLOH, LLC. All rights reserved */

SavedControls = [];
NOLOHChanges = new Object();
NOLOHKey = null;
NOLOHCaught = [];
_NFocus = null;
_NContextMenuSource = null;
_NFlashArgs = null;
_NURLTokenLink = null;
_NLookup = 
{
	"style.left": "Left",
	"style.top": "Top",
	"style.width": "Width",
	"style.height": "Height",
	"style.zIndex": "ZIndex",
	"style.background": "BackColor",
	"style.color": "Color",
	value: "_NText",
	newText: "_NText",
	innerHTML: "_NText",
	selectedIndex: "SelectedIndex",
	selectedTab: "SelectedTab",
	checked: "Checked",
	className: "CSSClass",
	src: "Src",
	scrollLeft: "ScrollLeft",
	scrollTop: "ScrollTop",
	selectedIndices: "_NSelectedIndices",
	"calViewDate.setMonth": "ViewMonth",
	"calViewDate.setFullYear": "ViewYear",
	"calSelectDate.setDate": "Date",
	"calSelectDate.setMonth": "Month",
	"calSelectDate.setFullYear": "Year"
}
NOLOHUpload = new Object();
NOLOHUpload.FileUploadObjIds = [];
_NHistoryLength = history.length;
NOLOHVisit = -1;
HighestZIndex = 0;
LowestZIndex = 0;

function _NInit(loadLblId, loadImgId)
{
	document.body.NOLOHPostingBack = false;
	NOLOHCatchers = [];
	window.onscroll = BodyScrollState;
	window.onresize = BodySizeState;
	_NTitle = document.title;
	_NLoadLbl = loadLblId;
	_NLoadImg = loadImgId;
	_NSetProperty("N1", "Width", document.documentElement.clientWidth);
	_NSetProperty("N1", "Height", document.documentElement.clientHeight);
	var Graveyard = document.createElement("DIV");
	Graveyard.id = "Graveyard";
	Graveyard.style.display = "none";
	document.body.appendChild(Graveyard);
	if(location.hash=="")
		location = location + "#/";
	_NHash = location.hash;
	_NURL = location.href;
	try
	{
		var d=_N('NBackButton').contentWindow.document;
	}
	catch(e)
	{
		location.reload(true);
		return;
	}
	finally
	{
		d.open();
		//d.write('<HTML><HEAD><TITLE>'+_NTitle+'</TITLE></HEAD><BODY>'+location.href+'</BODY></HTML>');
		d.write(location.href);
		d.close();
		//d.title = _NTitle;
		_NURLCheck = setInterval('CheckURL()', 500);
	}
}

function CheckURL()
{
	var inner = _N('NBackButton').contentWindow.document.body.innerText;
	if((_NHash != location.hash && _NHash.charAt(1)=="/" && location.hash.charAt(1)=="/") || (_NURL != inner))
	{
		clearInterval(_NURLCheck);
		var str = "NOLOHVisit="+ ++NOLOHVisit + "&NApp=" + _NApp + "&NoSkeleton=true";
		req = new ActiveXObject("Microsoft.XMLHTTP");
		_N(_NLoadImg).style.visibility = "visible";
		_N(_NLoadLbl).style.visibility = "visible";
		req.onreadystatechange = ProcessReqChange;
		if(_NHistoryLength+1==history.length)
			var targetURL = inner;
		else
		{
			var targetURL = location.href;
			var d=_N('NBackButton').contentWindow.document;
			d.open();
			d.write(location.href);
			//d.write('<HTML><HEAD><TITLE>'+_NTitle+'</TITLE></HEAD><BODY>'+location.href+'</BODY></HTML>');
			d.close();
			//d.title = _NTitle;
			_NHistoryLength = history.length;
		}
		req.open("POST", (targetURL.indexOf('#/')==-1 ? targetURL.replace(_NHash,'')+(targetURL.indexOf('?')==-1?'?':'&') : targetURL.replace('#/',targetURL.indexOf('?')==-1?'?':'&')+'&')
           	+ 'NOLOHVisit=0&NApp' + _NApp + '&NWidth=' + document.documentElement.clientWidth + '&NHeight=' + document.documentElement.clientHeight, true);
		location = targetURL;
        _NHash = location.hash;
		_NURL = location.href;
		req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		req.setRequestHeader('Remote-Scripting', 'NOLOH-Postback');
		req.send(str);
		_N("N1").innerHTML = "";
	}
}

function _NSetURL(hash, id)
{
	if(id != null)
		_NURLTokenLink = id;
	location = document.URL.split('#',1)[0] + "#/" + hash;
	_NHash = location.hash;
	_NURL=location.href;
	var d=_N('NBackButton').contentWindow.document;
	d.open();
	//d.write('<HTML><HEAD><TITLE>'+_NTitle+'</TITLE></HEAD><BODY>'+location.href+'</BODY></HTML>');
	d.write(location.href);
	d.close();
	//d.title = _NTitle;
	_NHistoryLength = history.length;
	setTimeout(function() {document.title = _NTitle;}, 2000);
	if(window.Tracker != null)
		eval(window.Tracker);
}

function _NSetTitle(title)
{
	document.title = _NTitle = title;
}

function SaveControl(id)
{
	var temp = _N(id);
	SavedControls[id] = temp.cloneNode(false);
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

function _NSetProperty(id, property, value)
{
	NOLOHChange(id, property, value);
	_NSave(id, property, value);
}

function NOLOHChangeInit(id, propertyString)
{
	if(NOLOHChanges[id] == null)
		NOLOHChanges[id] = new Object();
	if(NOLOHChanges[id][propertyString] == null)
		NOLOHChanges[id][propertyString] = new Object();
}

function NOLOHChange(id, propertyString, newValue)
{
	var obj;
	obj = _N(id);
	if(!obj)
		obj = window[id];
	NOLOHChangeByObj(obj, propertyString, newValue);
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
				if(obj.ReturnKey != null && window.event.keyCode == 13)
					obj.ReturnKey.call();
				if(obj.KeyPress != null)
				{
					NOLOHKey = window.event.keyCode;
					obj.KeyPress.call();
				}
				if(obj.TypePause != null && (window.event.keyCode < 37 || window.event.keyCode > 40))
				{
					clearTimeout(obj.TypePauseTimeout);
					obj.TypePauseTimeout = setTimeout("var obj = _N('"+obj.id+"'); _NSave(obj.id,'value',obj.value); obj.TypePause.call();", 500);
				}
			}
			obj.onkeyup = function(event)
			{
				if(window.event.keyCode == 8 && obj.TypePause != null)
				{
					clearTimeout(obj.TypePauseTimeout);
					obj.TypePauseTimeout = setTimeout("var obj = _N('"+obj.id+"'); _NSave(obj.id,'value',obj.value); obj.TypePause.call();", 500);
				}
			}
		case "onblur":
		case "onchange":
		case "onclick":
		case "ondblclick":
		case "onfocus":
		case "onelapsed":
		case "oninput":
		case "onmouseout":
		case "onmouseover":
		case "onmouseup":
		case "onload":
		case "onpaste":
		case "onscroll":
        case "onunload":
			eval("obj." + propertyString + " = function(event) {" + newValue + ";}");
			break;
		case "oncontextmenu":
			eval("obj.oncontextmenu = function(event) {" + newValue + "; if(obj.ContextMenu!=null) ShowContextMenu(obj); return false;}");
			break;
		case "onmousedown":
			eval("obj.onmousedown = function(event) {" + newValue + "; if(obj.Shifts!=null && obj.Shifts.length!=0) ShiftStart(obj.Shifts);}");
			break;
		case "DragCatch":
			if(newValue == "")
			{
				for(var i=0; i<NOLOHCatchers.length; ++i)
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
		case "href":
			obj.href = newValue=="#" ? "javascript:void(0);" : newValue;
			break;
		case "checked":
			NOLOHChangeByObj(obj, "Selected", newValue);
			obj.checked = newValue;
			break;
		case "Shifts":
			if(obj.onmousedown == null)
				NOLOHChangeByObj(obj, "onmousedown", "");
		case "ChildrenArray":
			eval("obj." + propertyString + " = " + newValue + ";");
			break;
		case "Group":
			obj.Group = window[newValue];
			if(newValue)
				obj.Group.Elements.push(obj.id);
			break;
		case "Selected":
			if(obj.Selected==true != newValue)
			{
				if(obj.Group)
					obj.Group.PrevSelectedElement = obj.Group.GetSelectedElement();
				_NSave(obj.id,'Selected',obj.Selected=newValue);
				if(!document.body.NOLOHPostingBack)
				{
					if(newValue)
					{
						if(obj.Select!=null)
							obj.Select.call();
					}
					else
						if(obj.Deselect!=null)
							obj.Deselect.call();
					if(obj.Group && obj.Group.onchange)
						obj.Group.onchange.call();
				}
			}
			break;
		case "style.zIndex":
			if(newValue > HighestZIndex)
				HighestZIndex = newValue;
			if(newValue < LowestZIndex)
				LowestZIndex = newValue;
			obj.style.zIndex = obj.BuoyantParentId == null ? newValue : newValue + 9999;
			break;
		case "style.left":
			if(obj.BuoyantParentId == null)
			{
				obj.style.left = newValue;
				if(obj.BuoyantChildren != null)
					for(var i=0; i<obj.BuoyantChildren.length; ++i)
						MoveBuoyant(obj.BuoyantChildren[i]);
			}
			else
			{
				obj.BuoyantLeft = parseInt(newValue);
				MoveBuoyant(obj.id);
			}
			break;
		case "style.top":
			if(obj.BuoyantParentId == null)
			{
				obj.style.top = newValue;
				if(obj.BuoyantChildren != null)
					for(var i=0; i<obj.BuoyantChildren.length; ++i)
						MoveBuoyant(obj.BuoyantChildren[i]);
			}
			else
			{
				obj.BuoyantTop = parseInt(newValue);
				MoveBuoyant(obj.id);
			}
			break;
		case "ContextMenu":
			if(obj.oncontextmenu == null)
				NOLOHChangeByObj(obj, "oncontextmenu", "");
		default:
			eval("obj." + propertyString + " = newValue;");
	}
}

function _NSave(id, propertyString, newValue)
{
	if(id.indexOf("_") >= 0)
		return;
	NOLOHChangeInit(id, propertyString);
	var tempObj;
	if(propertyString != "timer")
		tempObj = _N(id);
	else
		eval("tempObj = window." + id + ";");
	if(typeof newValue == "undefined")
		eval("newValue = tempObj."+propertyString+";");
	switch(propertyString)
	{
		case "value":
			NOLOHChangeInit(id, "value");
			NOLOHChanges[id][propertyString][0] = (typeof newValue == "string" ? newValue.replace(/&/g, "~da~").replace(/\+/g, "~dp~") : newValue);
			break;
		case "style.left":
		case "style.top":
		case "style.width":
		case "style.height":
			NOLOHChangeInit(id, propertyString);
			NOLOHChanges[id][propertyString][0] = parseInt(newValue);
			break;
		case "style.visibility":
		case "style.display":
			NOLOHChangeInit(id, "Visible");
			var obj = _N(id);
			NOLOHChanges[id]["Visible"][0] = obj.style.display=="none" ? "null" : (obj.style.visibility == "inherit");
			break;
		case "style.filter":
			NOLOHChangeInit(id, "Opacity");
			NOLOHChanges[id]["Opacity"][0] = parseInt(newValue.substring(14));
			break;
		case "checked":
			NOLOHChange(id, "Selected", newValue);
		default:
			NOLOHChangeInit(id, propertyString);
			NOLOHChanges[id][propertyString][0] = typeof newValue == "boolean" ? (newValue ? 1 : 0) : newValue;
	}
}

function ScrollState(id)
{
	var obj = _N(id);
	_NSetProperty(id,"scrollLeft",obj.scrollLeft);
	_NSetProperty(id,"scrollTop",obj.scrollTop);
}

function BodyScrollState()
{
	var X = document.documentElement.scrollLeft+1;
	var Y = document.documentElement.scrollTop+1;
	var loadImg = _N(_NLoadImg);
	loadImg.style.left = X+"px";
	loadImg.style.top = Y+"px";	
	var loadLbl = _N(_NLoadLbl);
	loadLbl.style.left = X+30+"px";
	loadLbl.style.top = Y+6+"px";
}

function BodySizeState()
{
	var body = _N("N1");
	if(body.ShiftsWith)
	{
		var deltaX = document.documentElement.clientWidth - body.Width;
		var deltaY = document.documentElement.clientHeight - body.Height;
		for(var i in body.ShiftsWith)
			ShiftObjects(body.ShiftsWith[i], deltaX, deltaY);
	}
	if(body.BuoyantChildren != null)
	{
		var buoyantCount = body.BuoyantChildren.length;
		for(var i=0; i<buoyantCount; ++i)
			MoveBuoyant(body.BuoyantChildren[i]);
	}
	_NSetProperty("N1", "Width", document.documentElement.clientWidth);
	_NSetProperty("N1", "Height", document.documentElement.clientHeight);
}

function _NSetP(id, nameValuePairs)
{
	var i = 0;
	var obj = _N(id);
	while(i<nameValuePairs.length)
	{
		NOLOHChangeByObj(obj, nameValuePairs[i], nameValuePairs[i+1]);
		SavedControls[id][nameValuePairs[i++]] = nameValuePairs[i++];
	}
}

function _NAdd(addTo, tag, nameValuePairs, beforeId)
{
	var elt = document.createElement(tag);
	elt.style.position = "absolute";
	var i = 0;
	while(i<nameValuePairs.length)
		NOLOHChangeByObj(elt, nameValuePairs[i++], nameValuePairs[i++]);
	addTo = _N(addTo);
	if(typeof beforeId == "undefined")
		addTo.appendChild(elt);
	else
	{
		var before = _N(beforeId);
		if(before && before.parentNode == addTo)
			addTo.insertBefore(elt, before);
		else
			addTo.appendChild(elt);
	}
	SaveControl(elt.id);
}

function _NAdopt(id, parentId)
{
    var ele = _N(id);
    ele.parentNode.removeChild(ele);
    _N(parentId).appendChild(ele);
}

function _NRem(id)
{
	var ele = _N(id);
	ele.parentNode.removeChild(ele);
	_N("Graveyard").appendChild(ele);
    if(ele.BuoyantChildren != null)
    	for(var i=0; i<ele.BuoyantChildren.length; ++i)
			_NRem(ele.BuoyantChildren[i]);
}

function _NRes(id, parentId)
{
	var ele = _N(id);
	_N("Graveyard").removeChild(ele);
	_N(parentId).appendChild(ele);
    if(ele.BuoyantChildren != null)
    	for(var i=0; i<ele.BuoyantChildren.length; ++i)
			_NRes(ele.BuoyantChildren[i], parentId);
}

function _NAsc(id)
{
	var ele = _N(id);
	if(ele)
    {
        if(ele.BuoyantChildren != null)
        	for(var i=0; i<ele.BuoyantChildren.length; ++i)
        	{
        		_NAsc(ele.BuoyantChildren[i]);
        		var parent = ele.parentNode;
        		do
				{
					if(parent.BuoyantChildren != null)
						parent.BuoyantChildren.splice(parent.BuoyantChildren.indexOf(ele.BuoyantChildren[i]), 1);
					parent = parent.parentNode;
				}while (parent && parent.id);
        	}
        ele.parentNode.removeChild(ele);
    }
}

function _NGCAsc(idArr)
{
	var arrLength = idArr.length;
	for(var i=0; i<arrLength; ++i)
		_NAsc(idArr[i]);
}

function GetChanges()
{
	var changes = "", distinctId, property;
	for(distinctId in NOLOHChanges)
	{
		changes += distinctId;
		for(property in NOLOHChanges[distinctId])
			if(NOLOHChanges[distinctId][property][0] != SavedControls[distinctId][property])
			{
				changes += "~d1~";
				SavedControls[distinctId][property] = NOLOHChanges[distinctId][property][0];
				changes += (_NLookup[property] ? _NLookup[property] : property) + "~d1~" + NOLOHChanges[distinctId][property][0];
			}
		changes += "~d0~";
	}
	NOLOHChanges = new Object();
	return changes.substring(0,changes.length-4);
}

function ExecReqResponse(response)
{
	if(response[0] != "")
	{
		var s = document.createElement("SCRIPT");
		s.type = "text/javascript";
		s.text = response[0];
		document.getElementsByTagName('head')[0].appendChild(s);
	}
	if(_NDebugMode == "Full")
	{
		var r = response[1].match(/((?:[^'";]|'.*?'|".*?")*?);/mg);
		for(var i=0; i<r.length; ++i)
		try
		{
			eval(r[i]);
		}
		catch(err)
		{
			alert("A javascript error has occurred:\n\n" + err.name + "\n" + err.description + "\nProcessing statement: " + r[i]);
			i=r.length;
		}
	}
	else
		eval(response[1]);
}

function CompleteReqResponse()
{
	_N(_NLoadImg).style.visibility = "hidden";
	_N(_NLoadLbl).style.visibility = "hidden";
	document.body.NOLOHPostingBack = false;
	_NURLCheck = setInterval('CheckURL()', 500);
}

function ProcessReqChange()
{
	var ready=req.readyState;
	var data=null;
	if (ready==4)
	{
   		var response = req.responseText.split("/*~NScript~*/", 2);
   		if(typeof _NDebugMode == "undefined")
		{
			ExecReqResponse(response);
			CompleteReqResponse();
		}
		else
		{
	   		try
	   		{
				ExecReqResponse(response);
	   		}
	   		catch(err)
	   		{
				alert(_NDebugMode ? "A javascript error has occurred:\n\n" + err.name + "\n" + err.description : "An application error has occurred.");
	   		}
	        finally
	        {
				CompleteReqResponse();
	        }
		}
	}
}

function PostBack(EventType, ID)
{
	if(!document.body.NOLOHPostingBack)
	{
		clearInterval(_NURLCheck);
		document.body.NOLOHPostingBack = true;
		var str = "NOLOHClientChanges="+GetChanges()+"&NOLOHServerEvent="+EventType+"@"+ID+"&NOLOHVisit="+ ++NOLOHVisit + "&NApp=" + _NApp;
		if(window.event != null)
			str += "&NOLOHMouseX="+(window.event.clientX+document.documentElement.scrollLeft)+
				"&NOLOHMouseY="+(window.event.clientY+document.documentElement.scrollTop);
		if(NOLOHKey != null)
		{
			str += "&NOLOHKey="+NOLOHKey;
			NOLOHKey = null;
		}
		if(NOLOHCaught.length != 0)
			str += "&NOLOHCaught="+NOLOHCaught.join(",");
        if(_NFocus != null)
            str += "&NOLOHFocus="+_NFocus+"&NOLOHSelectedText="+document.selection.createRange().text;
		if(_NContextMenuSource != null)
			str += "&NOLOHContextMenuSource="+_NContextMenuSource.id;
		if(_NFlashArgs != null)
		{
			str += "&NOLOHFlashArgs="+_NFlashArgs;
			_NFlashArgs = null;
		}
		if(_NURLTokenLink != null)
		{
			str += "&NOLOHURLTokenLink="+_NURLTokenLink;
			_NURLTokenLink = null;
		}
	    req = new ActiveXObject("Microsoft.XMLHTTP");
		_N(_NLoadImg).style.visibility = "visible";
		_N(_NLoadLbl).style.visibility = "visible";
        if(EventType != "Unload")
	        req.onreadystatechange = ProcessReqChange;
	    req.open("POST", document.URL.split("#", 1)[0], true);
	    req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	    req.setRequestHeader('Remote-Scripting', 'NOLOH-Postback');
	    req.send(str);
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