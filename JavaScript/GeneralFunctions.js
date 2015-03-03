/*! Copyright (c) 2005 - 2010 NOLOH, LLC. All rights reserved */
function _N(id)
{
	return document.getElementById(id) || _N.Incubator[id] || _N[id] || null;
}
function BringToFront(id)
{
	_NSet(id, "style.zIndex", ++_N.HighestZ);
}
function SendToBack(id)
{
	_NSet(id, "style.zIndex", --_N.LowestZ);
}
function ToggleVisibility(id)
{
	var obj = _N(id);
	if(obj.style.visibility == "hidden" || obj.style.display == "none")
	{
		BringToFront(id);
		_NSet(id, "style.visibility", "inherit");
		_NSet(id, "style.display", "");
	}
	else
		_NSet(id, "style.display", "none");
}
function _NNS(obj, ns, checkUndefined)
{
	var argLength = ns.length, i;
	for(i=0; i < argLength; obj = obj[ns[i++]])
	{
		if(checkUndefined && typeof(obj) == 'undefined')
			return false;
	}
	return checkUndefined?typeof(obj) != 'undefined':obj;
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
	//!obj Temporary hack to make sure obj exists, until shifts are corrected
	if(!obj || obj.style.display == "none" || obj.style.visibility == "hidden" || obj.disabled == true)
		return false;
	return obj.parentNode.id ? _NAvail(obj.parentNode.id) : true;
}