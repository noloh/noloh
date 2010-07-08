/*! Copyright (c) 2005 - 2010 NOLOH, LLC. All rights reserved */

function _N(id)
{
	var obj;
	if(obj = document.getElementById(id));
	else if(obj = _N.Incubator[id]);
	else obj = _N[id];
	return obj;
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
function _NNS(obj, ns)
{
	var argLength = ns.length, i;
	for(i=0; i<argLength; ++i)
		obj = obj[ns[i]];
	return obj;
}
function _NCNS(obj)
{
	var argLength = arguments.length, i, arg, val;
	for(i=1; i<argLength; ++i)
	{
		val = obj[arg = arguments[i]];
		obj = (!val)?obj[arg] = []:val;
	}
	return obj;
}
function _NAvail(id)
{
	var obj = _N(id);
	if(obj.style.display == "none" || obj.style.visibility == "hidden" || obj.disabled == true)
		return false;
	return obj.parentNode.id ? _NAvail(obj.parentNode.id) : true;
}