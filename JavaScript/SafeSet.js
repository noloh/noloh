function _NSfSet(id, prpty, value)
{
	var obj = _N(id), argLength = arguments.length, i;
	for(i=3; i<argLength; ++i)
		if(!obj[arguments[i]])
			obj = obj[arguments[i]] = [];
	obj[prpty] = value;
}