function _NEvteeSetP(id, nameValuePairs)
{
	var i = 0, obj = _N(id);
	while(i<nameValuePairs.length)
		_NChangeByObj(obj, nameValuePairs[i++], nameValuePairs[i++]);
}