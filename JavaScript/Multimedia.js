function _NTalk(id, val)
{
	var obj = _N(id.substring(0, id.length-1));
	_N.EventVars.FlashArgs = "";
	var lastIndex = arguments.length-1;
	for(var i=1; i<lastIndex; ++i)
		_N.EventVars.FlashArgs += arguments[i] + "~d3~";
	if(lastIndex >= 1)
		_N.EventVars.FlashArgs += arguments[lastIndex];
	if(obj.Talk)
		obj.Talk();
}
function _NFlashInvoke(id, func)
{
	var obj = _N(id + 'I');
	try
	{
		if(obj.PercentLoaded() == 100)
		{
			_NInvokeArgs = null;
			var args = Array.prototype.slice.call(arguments, 2);
			obj[func].apply(obj, args);
		}
		else
			obj.MakeError();
	}
	catch(e)
	{
		_NInvokeArgs = arguments;
		window.setTimeout(function() { _NFlashInvoke.apply(null, _NInvokeArgs); }, 250);
	}
}