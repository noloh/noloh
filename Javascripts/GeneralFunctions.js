function BringToFront(id)
{
	ChangeAndSave(id, "style.zIndex", ++HighestZIndex);
}

function SendToBack(id)
{
	ChangeAndSave(id, "style.zIndex", --LowestZIndex);
}

function ChangeImage(id, src)
{
	ChangeAndSave(id, "src", src);
}

function ChangeLabelText(id, text)
{
	ChangeAndSave(id, "innerHTML", text);
}

function ToggleVisibility(id)
{
	var toggleObj = document.getElementById(id);
	if(toggleObj.style.visibility == "hidden" || toggleObj.style.display == "none")
	{
		BringToFront(id);
		ChangeAndSave(id, "style.visibility", "inherit");
		ChangeAndSave(id, "style.display", "");
	}
	else
		ChangeAndSave(id, "style.display", "none");
}

function FindX(objId)
{
	var curleft = 0;
	var obj = document.getElementById(objId);
	if (obj.offsetParent)
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft;
			obj = obj.offsetParent;
		}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function FindY(objId)
{
	var curtop = 0;
	var obj = document.getElementById(objId);
	if (obj.offsetParent)
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop;
			obj = obj.offsetParent;
		}
	else if (obj.y)
		curtop += obj.y;
	return curtop;
}

function IsAvailable(objId)
{
	var obj = document.getElementById(objId);
	if(obj.style.display == "none" || obj.style.visibility == "hidden" || obj.disabled == true)
		return false;
	return obj.parentNode.id ? IsAvailable(obj.parentNode.id) : true;
}

function _NAWH(id)
{
	var ele = document.getElementById(id);
	var awh = document.getElementById("NAWH");
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
	var obj = document.getElementById(id);
	var parent = document.getElementById(parentId);
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
	MoveBuoyant(id);
}

function StopBuoyant(id)
{
	var obj = document.getElementById(id);
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
	var obj = document.getElementById(id);
	obj.style.left = FindX(obj.BuoyantParentId) + obj.BuoyantLeft + "px";
	obj.style.top = FindY(obj.BuoyantParentId) + obj.BuoyantTop + "px";
}

function _NRemStyle(remPath, nPath)
{
	document.getElementById('NHead').removeChild(document.getElementById(remPath));
	var blankStyle = document.createElement("LINK");
	blankStyle.rel = "stylesheet";
	blankStyle.type = "text/css";
	blankStyle.href = nPath+"Web/UI/Blank.css";
	document.getElementById('NHead').appendChild(blankStyle);
	document.getElementById('NHead').removeChild(blankStyle);
}

function _NRequestFile(iSrc)
{
	var iframe = document.createElement("IFRAME");
	iframe.id = iSrc;
	iframe.src = iSrc;
	iframe.style.display = "none";
	document.body.appendChild(iframe);
	window.setTimeout('document.body.removeChild(document.getElementById("' + iSrc + '"))', 5000);
}
