function _NTalk(id, val)
{
	var obj = _N(id.substring(0, id.length-1));
	_NFlashArgs = "";
	var lastIndex = arguments.length-1;
	for(var i=1; i<lastIndex; ++i)
		_NFlashArgs += arguments[i] + "~d3~";
	if(lastIndex >= 1)
		_NFlashArgs += arguments[lastIndex];
	if(obj.Talk != null)
		obj.Talk.call();
}
function _NInvokeFlash(id, func)
{
	var obj = _N(id + 'I');
	var evalString = "";
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
		window.setTimeout("_NInvokeFlash.apply(null, _NInvokeArgs);", 250);
	}
	finally
	{
		if(evalString != "")
			eval(evalString);
	}
}