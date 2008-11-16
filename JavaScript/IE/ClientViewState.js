_N.Saved = [];
_N.Changes = [];
_N.Visit = -1;
_N.HighestZ = 0;
_N.LowestZ = 0;
_N.Request = true;
_N.HistoryLength = history.length;
_N.LookUp = 
{
	"style.left": "Left",
	"style.top": "Top",
	"style.width": "Width",
	"style.height": "Height",
	"style.zIndex": "ZIndex",
	"style.background": "BackColor",
	"style.color": "Color",
	value: "_NText",
	innerHTML: "_NText",
	selectedIndex: "SelectedIndex",
	className: "CSSClass",
	src: "Src",
	scrollLeft: "ScrollLeft",
	scrollTop: "ScrollTop"
}
function _NInit(loadLblId, loadImgId, debugMode)
{
	window.onscroll = _NBodyScrollState;
	window.onresize = _NBodySizeState;
	_N.Title = document.title;
	_N.LoadLbl = loadLblId;
	_N.LoadImg = loadImgId;
	_N.DebugMode = debugMode;
	_NSetProperty("N1", "Width", document.documentElement.clientWidth);
	_NSetProperty("N1", "Height", document.documentElement.clientHeight);
	var graveyard = document.createElement("DIV");
	graveyard.id = "Graveyard";
	graveyard.style.display = "none";
	document.body.appendChild(graveyard);
	if(location.hash=="")
		location = location + "#/";
	_N.Hash = location.hash;
	_N.URL = location.href;
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
		d.write(location.href);
		d.close();
		_N.URLChecker = setInterval('_NCheckURL()', 500);
	}
}
function _NCheckURL()
{
	var inner = _N('NBackButton').contentWindow.document.body.innerText;
	if((_N.Hash != location.hash && _N.Hash.charAt(1)=="/" && location.hash.charAt(1)=="/") || (_N.URL != inner))
	{
		clearInterval(_N.URLChecker);
		var str = "_NVisit="+ ++_N.Visit + "&_NApp=" + _NApp + "&_NSkeletonless=true";
		_N.Request = new ActiveXObject("Microsoft.XMLHTTP");
		_N(_N.LoadImg).style.visibility = "visible";
		_N(_N.LoadLbl).style.visibility = "visible";
		_N.Request.onreadystatechange = _NReqStateChange;
		if(_N.HistoryLength+1==history.length)
			var targetURL = inner;
		else
		{
			var targetURL = location.href;
			var d=_N('NBackButton').contentWindow.document;
			d.open();
			d.write(location.href);
			d.close();
			_N.HistoryLength = history.length;
		}
		_N.Request.open("POST", (targetURL.indexOf('#/')==-1 ? targetURL.replace(_N.Hash,'')+(targetURL.indexOf('?')==-1?'?':'&') : targetURL.replace('#/',targetURL.indexOf('?')==-1?'?':'&')+'&')
           	+ '_NVisit=0&_NApp' + _NApp + '&_NWidth=' + document.documentElement.clientWidth + '&_NHeight=' + document.documentElement.clientHeight, true);
		location = targetURL;
		_N.Hash = location.hash;
		_N.URL = location.href;
		_N.Request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		_N.Request.setRequestHeader('Remote-Scripting', 'NOLOH');
		_N.Request.send(str);
		_N("N1").innerHTML = "";
	}
}
function _NSetURL(hash, id)
{
	_N.URLTokenLink = id;
	location = document.URL.split('#',1)[0] + "#/" + hash;
	_N.Hash = location.hash;
	_N.URL=location.href;
	var d=_N('NBackButton').contentWindow.document;
	d.open();
	d.write(location.href);
	d.close();
	_N.HistoryLength = history.length;
	setTimeout(function() {document.title = _N.Title;}, 2000);
	if(_N.Tracker)
		eval(_N.Tracker);
}
function _NSetTitle(title)
{
	document.title = _N.Title = title;
}
function _NSaveControl(id)
{
	var obj = _N(id);
	_N.Saved[id] = obj.cloneNode(false);
	_N.Saved[id].selectedIndex = obj.selectedIndex;

}
function _NSetProperty(id, property, value)
{
	_NChange(id, property, value);
	_NSave(id, property, value);
	return value;
}
function _NChangeInit(id, property)
{
	if(!_N.Changes[id])
		_N.Changes[id] = [];
	if(!_N.Changes[id][property])
		_N.Changes[id][property] = [];
}
function _NChange(id, property, value)
{
	_NChangeByObj(_N(id), property, value);
}
function _NChangeByObj(obj, property, value)
{
	if(!obj)
		return;
	switch(property)
	{
		case "KeyPress":
		case "ReturnKey":
		case "TypePause":
			obj.onkeypress = _NKeyEvntsPress;
			obj.onkeyup = _NKeyEvntsUp;
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
			eval("obj." + property + " = function(event) {" + value + ";}");
			break;
		case "oncontextmenu":
			eval("obj.oncontextmenu = function(event) {" + value + "; if(obj.ContextMenu) _NCMShow(obj); return false;}");
			break;
		case "onmousedown":
			eval("obj.onmousedown = function(event) {" + value + "; if(obj.Shifts && obj.Shifts.length!=0) _NShftSta(obj.Shifts);}");
			break;
		case "DragCatch":
			if(value == "")
			{
				for(var i=0; i<_N.Catchers.length; ++i)
					if(_N.Catchers[i] == obj.id)
					{
						_N.Catchers.splice(i, 1);
						break;
					}
			}
			else
				_N.Catchers.push(obj.id);
			eval("obj.DragCatch = function(event) {" + value + ";}");
			break;
		case "href":
			obj.href = value=="#" ? "javascript:void(0);" : value;
			break;
		case "Shifts":
			if(!obj.onmousedown)
				_NChangeByObj(obj, "onmousedown", "");
		case "ChildrenArray":
			eval("obj." + property + " = " + value + ";");
			break;
		case "Group":
			if(obj.Group = _N(value))
				obj.Group.Elements.push(obj.id);
			break;
		case "Selected":
			if(obj.Selected==true != value)
			{
				if(obj.Group)
					obj.Group.PrevSelectedElement = obj.Group.GetSelectedElement();
				_NSave(obj.id,'Selected',obj.Selected=value);
				if(!_N.Request)
				{
					if(value)
					{
						if(obj.Select)
							obj.Select();
					}
					else
						if(obj.Deselect)
							obj.Deselect();
					if(obj.Group && obj.Group.onchange)
						obj.Group.onchange();
				}
			}
			break;
		case "style.zIndex":
			if(value > _N.HighestZ)
				_N.HighestZ = value;
			if(value < _N.LowestZ)
				_N.LowestZ = value;
			obj.style.zIndex = obj.BuoyantParentId == null ? value : value + 9999;
			break;
		case "style.left":
			if(!obj.BuoyantParentId)
			{
				obj.style.left = value;
				if(obj.BuoyantChildren)
					for(var i=0; i<obj.BuoyantChildren.length; ++i)
						_NByntMv(obj.BuoyantChildren[i]);
			}
			else
			{
				obj.BuoyantLeft = parseInt(value);
				_NByntMv(obj.id);
			}
			break;
		case "style.top":
			if(!obj.BuoyantParentId)
			{
				obj.style.top = value;
				if(obj.BuoyantChildren)
					for(var i=0; i<obj.BuoyantChildren.length; ++i)
						_NByntMv(obj.BuoyantChildren[i]);
			}
			else
			{
				obj.BuoyantTop = parseInt(value);
				_NByntMv(obj.id);
			}
			break;
		case "ContextMenu":
			if(!obj.oncontextmenu)
				_NChangeByObj(obj, "oncontextmenu", "");
		default:
			eval("obj." + property + " = value;");
	}
}
function _NSave(id, property, value)
{
	if(id.indexOf("_") >= 0)
		return;
	_NChangeInit(id, property);
	var obj = _N(id);
	if(typeof value == "undefined")
		eval("value = obj."+property+";");
	switch(property)
	{
		case "value":
			_NChangeInit(id, "value");
			_N.Changes[id][property][0] = (typeof value == "string" ? value.replace(/&/g, "~da~").replace(/\+/g, "~dp~") : value);
			break;
		case "style.left":
		case "style.top":
		case "style.width":
		case "style.height":
			_NChangeInit(id, property);
			_N.Changes[id][property][0] = parseInt(value);
			break;
		case "style.visibility":
		case "style.display":
			_NChangeInit(id, "Visible");
			var obj = _N(id);
			_N.Changes[id]["Visible"][0] = obj.style.display=="none" ? "null" : (obj.style.visibility == "inherit");
			break;
		case "style.filter":
			_NChangeInit(id, "Opacity");
			_N.Changes[id]["Opacity"][0] = parseInt(value.substring(14));
			break;
		default:
			_NChangeInit(id, property);
			_N.Changes[id][property][0] = typeof value == "boolean" ? (value ? 1 : 0) : value;
	}
}
function _NBodyScrollState()
{
	var x = document.documentElement.scrollLeft+1;
	var y = document.documentElement.scrollTop+1;
	var loadImg = _N(_N.LoadImg);
	loadImg.style.left = x+"px";
	loadImg.style.top = y+"px";	
	var loadLbl = _N(_N.LoadLbl);
	loadLbl.style.left = x+30+"px";
	loadLbl.style.top = y+6+"px";
}
function _NBodySizeState()
{
	var body = _N("N1");
	if(body.ShiftsWith)
	{
		var deltaX = document.documentElement.clientWidth - body.Width;
		var deltaY = document.documentElement.clientHeight - body.Height;
		for(var i in body.ShiftsWith)
			_NShftObjs(body.ShiftsWith[i], deltaX, deltaY);
	}
	if(body.BuoyantChildren)
	{
		var buoyantCount = body.BuoyantChildren.length;
		for(var i=0; i<buoyantCount; ++i)
			_NByntMv(body.BuoyantChildren[i]);
	}
	_NSetProperty("N1", "Width", document.documentElement.clientWidth);
	_NSetProperty("N1", "Height", document.documentElement.clientHeight);
}
function _NSetP(id, nameValuePairs)
{
	var i = 0, obj = _N(id);
	while(i<nameValuePairs.length)
	{
		_NChangeByObj(obj, nameValuePairs[i], nameValuePairs[i+1]);
		_N.Saved[id][nameValuePairs[i++]] = nameValuePairs[i++];
	}
}
function _NAdd(addTo, tag, nameValuePairs, beforeId)
{
	var ele = document.createElement(tag), i=0;
	ele.style.position = "absolute";
	while(i<nameValuePairs.length)
		_NChangeByObj(ele, nameValuePairs[i++], nameValuePairs[i++]);
	addTo = _N(addTo);
	if(typeof beforeId == "undefined")
		addTo.appendChild(ele);
	else
	{
		var before = _N(beforeId);
		if(before && before.parentNode == addTo)
			addTo.insertBefore(ele, before);
		else
			addTo.appendChild(ele);
	}
	_NSaveControl(ele.id);
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
    if(ele.BuoyantChildren)
    	for(var i=0; i<ele.BuoyantChildren.length; ++i)
			_NRem(ele.BuoyantChildren[i]);
    if(ele.TimerChildren)
    	for(var i=0; i<ele.TimerChildren.length; ++i)
			_N(ele.TimerChildren[i]).Stop();
}
function _NRes(id, parentId)
{
	var ele = _N(id);
	_N("Graveyard").removeChild(ele);
	_N(parentId).appendChild(ele);
    if(ele.BuoyantChildren)
    	for(var i=0; i<ele.BuoyantChildren.length; ++i)
			_NRes(ele.BuoyantChildren[i], parentId);
	if(ele.TimerChildren)
	    for(var i=0; i<ele.TimerChildren.length; ++i)
			_N(ele.TimerChildren[i]).Start();
}
function _NAsc(id)
{
	var ele = _N(id);
	if(ele)
    {
        if(ele.BuoyantChildren)
        	for(var i=0; i<ele.BuoyantChildren.length; ++i)
        	{
        		_NAsc(ele.BuoyantChildren[i]);
        		var parent = ele.parentNode;
        		do
				{
					if(parent.BuoyantChildren)
						parent.BuoyantChildren.splice(parent.BuoyantChildren.indexOf(ele.BuoyantChildren[i]), 1);
					parent = parent.parentNode;
				}while (parent && parent.id);
        	}
		if(ele.TimerChildren)
			for(var i=0; i<ele.TimerChildren.length; ++i)
				_N(ele.TimerChildren[i]).Destroy();
        ele.parentNode.removeChild(ele);
    }
}
function _NGCAsc(idArr)
{
	var arrLength = idArr.length;
	for(var i=0; i<arrLength; ++i)
		_NAsc(idArr[i]);
}
function _NChangeString()
{
	var change = "", changes = "", id, property;
	for(id in _N.Changes)
	{
		change = id;
		for(property in _N.Changes[id])
			if(_N.Changes[id][property][0] != _N.Saved[id][property])
			{
				change += "~d1~" + (_N.LookUp[property] ? _N.LookUp[property] : property) + "~d1~" + _N.Changes[id][property][0];
				_N.Saved[id][property] = _N.Changes[id][property][0];
			}
		if(change != id)
			changes += change + "~d0~";
	}
	_N.Changes = [];
	return changes.substring(0,changes.length-4);
}
function _NProcessResponse(response)
{
	if(response[0] != "")
	{
		var s = document.createElement("SCRIPT");
		s.type = "text/javascript";
		s.text = response[0];
		document.getElementsByTagName('head')[0].appendChild(s);
	}
	if(_N.DebugMode == "Full")
	{
		var r = response[1].match(/((?:[^'";]|'(?:[^\\]|\\.)*?'|"(?:[^\\]|\\.)*?")*?);/mg);
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
function _NUnServer()
{
	_N(_N.LoadImg).style.visibility = "hidden";
	_N(_N.LoadLbl).style.visibility = "hidden";
	_N.Request = null;
	_N.URLChecker = setInterval('_NCheckURL()', 500);
}
function _NReqStateChange()
{
	if(_N.Request.readyState == 4)
	{
   		var response = _N.Request.responseText.split("/*_N*/", 2);
   		if(typeof _N.DebugMode == "undefined")
		{
			_NProcessResponse(response);
			_NUnServer();
		}
		else
		{
	   		try
	   		{
				_NProcessResponse(response);
	   		}
	   		catch(err)
	   		{
				alert(_N.DebugMode ? "A javascript error has occurred:\n\n" + err.name + "\n" + err.description : "An application error has occurred.");
	   		}
	        finally
	        {
				_NUnServer();
	        }
		}
	}
}
function _NServer(eventType, id)
{
	if(!_N.Request)
	{
		clearInterval(_N.URLChecker);
		var str = "_NChanges="+_NChangeString()+"&_NEvents="+eventType+"@"+id+"&_NVisit="+ ++_N.Visit + "&_NApp=" + _NApp;
		if(window.event)
			str += "&_NMouseX="+(window.event.clientX+document.documentElement.scrollLeft)+
				"&_NMouseY="+(window.event.clientY+document.documentElement.scrollTop);
		if(_N.Key)
		{
			str += "&_NKey="+_N.Key;
			_N.Key = null;
		}
		if(_N.Caught)
			str += "&_NCaught="+_N.Caught.join(",");
        if(_N.Focus)
            str += "&_NFocus="+_N.Focus+"&_NSelectedText="+document.selection.createRange().text;
		if(_N.ContextMenuSource)
			str += "&_NCMSource="+_N.ContextMenuSource.id;
		if(_N.FlashArgs)
		{
			str += "&_NFlashArgs="+_N.FlashArgs;
			_N.FlashArgs = null;
		}
		if(_N.URLTokenLink)
		{
			str += "&_NTokenLink="+_N.URLTokenLink;
			_N.URLTokenLink = null;
		}
	    _N.Request = new ActiveXObject("Microsoft.XMLHTTP");
		_N(_N.LoadImg).style.visibility = "visible";
		_N(_N.LoadLbl).style.visibility = "visible";
        if(eventType != "Unload")
	        _N.Request.onreadystatechange = _NReqStateChange;
	    _N.Request.open("POST", document.URL.split("#", 1)[0], true);
	    _N.Request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	    _N.Request.setRequestHeader('Remote-Scripting', 'NOLOH');
	    _N.Request.send(str);
	}
}