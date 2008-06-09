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
	//alert(id + ' ' + func);
	//alert(id);
	//return;
	//alert(id);
	//var obj = document[id + 'I'];
	var obj = _N(id + 'I');//document['noloh'];
	var paramsString = "";
	var lastIndex = arguments.length-1;
	for(var i=2; i<lastIndex; ++i)
		paramsString += "arguments["+i+"],";
	if(lastIndex >= 2)
		paramsString += "arguments["+lastIndex+"]";
	eval("obj."+func+"("+paramsString+");");
	//obj.showPage(2);
}