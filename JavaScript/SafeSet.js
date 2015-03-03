function _NSfSet(id, prpty, value)
{
/*	var obj = _N(id), argLength = arguments.length, i;
	obj = _NCDot(obj, args);
	for(i=3; i<argLength; ++i)
		if(!obj[arguments[i]])
			obj = obj[arguments[i]] = [];
		//Asher - Added 4/19/2010 to fix problem with not setting namespace once set
		else 
			obj = obj[arguments[i]];
			*/
	var obj = _N(id), argLength = arguments.length, i, arg, val;
	for(i=3; i<argLength; ++i)
	{
		val = obj[arg = arguments[i]];
		obj = (!val)?obj[arg] = []:val;
	}
	return obj[prpty] = value;
}