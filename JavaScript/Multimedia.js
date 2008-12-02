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
	var obj = _N(id + 'I'), evalString = "";
	try
	{
		if(obj.PercentLoaded() == 100)
		{
			_NInvokeArgs = null;
			var paramsString = "";
			var lastIndex = arguments.length-1;
			for(var i=2; i<lastIndex; ++i)
				paramsString += "arguments["+i+"],";
			if(lastIndex >= 2)
				paramsString += "arguments["+lastIndex+"]";
			evalString = "obj."+func+"("+paramsString+");";
		}
		else
			obj.MakeError();
	}
	catch(e)
	{
		_NInvokeArgs = arguments;
		window.setTimeout("_NFlashInvoke.apply(null, _NInvokeArgs);", 250);
	}
	finally
	{
		if(evalString)
			eval(evalString);
	}
}