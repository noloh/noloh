function SetMarkupString(whatDistinctId, whatText)
{
	var tmpMarkupString = whatText;
	tmpMarkupString = tmpMarkupString.replace(/<NQt2>/g,"\"");
	tmpMarkupString = tmpMarkupString.replace(/<NQt1>/g,"\'");
	tmpMarkupString = tmpMarkupString.replace(/<Nendl>/g,"\n");
	_N(whatDistinctId).innerHTML = tmpMarkupString; 
}

function _NSetPEvtee(id, nameValuePairs)
{
	var i = 0;
	var obj = _N(id);
	while(i<nameValuePairs.length)
		NOLOHChangeByObj(obj, nameValuePairs[i++], nameValuePairs[i++]);
}