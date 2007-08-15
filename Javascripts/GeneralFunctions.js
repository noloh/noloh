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

function ToggleVisibility(whatobjid)
{
	var toggleObj = document.getElementById(whatobjid);
	if(toggleObj.style.visibility == "hidden")
	{
		BringToFront(whatobjid);
		ChangeAndSave(whatobjid, "style.visibility", "inherit");
	}
	else
		ChangeAndSave(whatobjid, "style.visibility", "hidden");
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

function _NAWH(id)
{
	var ele = document.getElementById(id);
	_NSave(id, "CachedWidth", ele.offsetWidth);
	_NSave(id, "CachedHeight", ele.offsetHeight);
}

function IsAvailable(objId)
{
	var obj = document.getElementById(objId);
	if(obj.style.display == "none" || obj.style.visibility == "hidden" || obj.disabled == true)
		return false;
	return obj.parentNode.id ? IsAvailable(obj.parentNode.id) : true;
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