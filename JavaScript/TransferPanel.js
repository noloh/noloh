function _NTPTransfer(fromId, toId)
{
	var from = _N(fromId);
	var to = _N(toId);
	while(from.selectedIndex >= 0)
	{
		to.options.add(new Option(from.options[from.selectedIndex].text, from.options[from.selectedIndex].value));
		from.remove(from.selectedIndex);
	}
	_NSave(fromId, "_NItems", _NExplOpts(from.options));
	_NSave(toId, "_NItems", _NExplOpts(to.options));
}
function _NExplOpts(options)
{
	var tempString ="";
	for(var i=0; i<options.length; ++i)
	{
		tempString += options[i].value + "~d2~";
		tempString += options[i].text + "~d3~";
	}
    return tempString.substring(0,tempString.length-4);
}