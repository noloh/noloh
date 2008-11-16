/*! Copyright (c) 2005 - 2008 NOLOH, LLC. All rights reserved */

function _N(id)
{
	var obj;
	if(obj = document.getElementById(id))
		return obj;
	return _N[id];
}
function BringToFront(id)
{
	_NSetProperty(id, "style.zIndex", ++_N.HighestZ);
}
function SendToBack(id)
{
	_NSetProperty(id, "style.zIndex", --_N.LowestZ);
}
function ToggleVisibility(id)
{
	var obj = _N(id);
	if(obj.style.visibility == "hidden" || obj.style.display == "none")
	{
		BringToFront(id);
		_NSetProperty(id, "style.visibility", "inherit");
		_NSetProperty(id, "style.display", "");
	}
	else
		_NSetProperty(id, "style.display", "none");
}
function _NAvail(id)
{
	var obj = _N(id);
	if(obj.style.display == "none" || obj.style.visibility == "hidden" || obj.disabled == true)
		return false;
	return obj.parentNode.id ? _NAvail(obj.parentNode.id) : true;
}