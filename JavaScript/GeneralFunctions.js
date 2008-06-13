function _N(id)
{
	return document.getElementById(id);
	/*var obj;
	if(obj = document.getElementById(id))
		return obj;
	return window[id];*/
}

function BringToFront(id)
{
	_NSetProperty(id, "style.zIndex", ++HighestZIndex);
}

function SendToBack(id)
{
	_NSetProperty(id, "style.zIndex", --LowestZIndex);
}

function ChangeLabelText(id, text)
{
	_NSetProperty(id, "innerHTML", text);
}

function ToggleVisibility(id)
{
	var toggleObj = _N(id);
	if(toggleObj.style.visibility == "hidden" || toggleObj.style.display == "none")
	{
		BringToFront(id);
		_NSetProperty(id, "style.visibility", "inherit");
		_NSetProperty(id, "style.display", "");
	}
	else
		_NSetProperty(id, "style.display", "none");
}

function IsAvailable(objId)
{
	var obj = _N(objId);
	if(obj.style.display == "none" || obj.style.visibility == "hidden" || obj.disabled == true)
		return false;
	return obj.parentNode.id ? IsAvailable(obj.parentNode.id) : true;
}

function _NAWH(id)
{
	var ele = _N(id);
	var awh = _N("NAWH");
	awh.style.fontSize = ele.style.fontSize;
	awh.style.width = ele.style.width;
	awh.style.height = ele.style.height;
	awh.innerHTML = ele.innerHTML;
	if(ele.style.width == "")
	{
		ele.style.width = awh.offsetWidth + "px";
		_NSave(id, "CachedWidth", awh.offsetWidth);
		
	}
	if(ele.style.height == "")
	{
		ele.style.height = awh.offsetHeight + "px";
		_NSave(id, "CachedHeight", awh.offsetHeight);
	}
}

function StartBuoyant(id, parentId)
{
	var obj = _N(id);
	var parent = _N(parentId);
	obj.BuoyantParentId = parentId;
	obj.BuoyantLeft = parseInt(obj.style.left);
	obj.BuoyantTop = parseInt(obj.style.top);
	obj.BuoyantZIndex = obj.style.zIndex;
	obj.style.zIndex = 9999;
	do
	{
		if(parent.BuoyantChildren == null)
			parent.BuoyantChildren = Array();
		parent.BuoyantChildren.push(id);
		parent = parent.parentNode;
	}while (parent && parent.id);
	setTimeout(function() {MoveBuoyant(id);}, 500);
}

function StopBuoyant(id)
{
	var obj = _N(id);
	obj.style.left = obj.BuoyantLeft + "px";
	obj.style.top = obj.BuoyantTop + "px";
	obj.style.zIndex = obj.BuoyantZIndex;
	obj.BuoyantParentId = null;
	obj.BuoyantLeft = null;
	obj.BuoyantTop = null;
	obj.BuoyantZIndex = null;
}

function MoveBuoyant(id)
{
	var obj = _N(id);
	var parent = _N(obj.BuoyantParentId);
	obj.style.left = FindX(obj.BuoyantParentId) + (parseInt(parent.style.borderLeftWidth,10)|0) + obj.BuoyantLeft + "px";
	obj.style.top = FindY(obj.BuoyantParentId) + (parseInt(parent.style.borderTopWidth,10)|0) + obj.BuoyantTop + "px";
}

function _NRemStyle(remPath, nPath)
{
	_N('NHead').removeChild(_N(remPath));
	var blankStyle = document.createElement("LINK");
	blankStyle.rel = "stylesheet";
	blankStyle.type = "text/css";
	blankStyle.href = nPath+"Controls/Blank.css";
	_N('NHead').appendChild(blankStyle);
	_N('NHead').removeChild(blankStyle);
}

function _NRequestFile(iSrc)
{
	var iframe = document.createElement("IFRAME");
	iframe.id = iSrc;
	iframe.src = iSrc;
	iframe.style.display = "none";
	document.body.appendChild(iframe);
	window.setTimeout('document.body.removeChild(_N("' + iSrc + '"))', 5000);
}
