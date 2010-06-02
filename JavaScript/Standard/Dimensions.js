function _NOuterWidth(id, margin)
{
	var obj = _N(id);
	if(margin)
	{
		var styles = window.getComputedStyle(obj, null);
		return _NSumProps(obj, [obj.offsetWidth, styles.marginLeft, styles.marginRight], 0);
	}
	return obj.offsetWidth;
}
function _NOuterHeight(id, margin)
{	
	var obj = _N(id);
	if(margin)
	{
		var styles = window.getComputedStyle(obj, null);
		return _NSumProps(obj, [obj.offsetHeight, styles.marginTop, styles.marginBottom], 0);
	}
	return obj.offsetHeight;
	
	/*return _NSumProps(_N(id), margin
//		?[['offsetHeight'], ['style', 'borderLeftWidth'], ['style', 'borderRightWidth'], ['style', 'marginTop'], ['style', 'marginBottom']]
		?[['offsetHeight'], ['style', 'marginTop'], ['style', 'marginBottom']]
		:[['offsetHeight']], 0);*/
}
function _NSumProps(obj, props, i)
{
	var val, prop, type;
	if(i < props.length)
	{
//		if(isNaN(val = (typeof (prop = props[i]) != 'number'?parseInt(_NNS(obj, props[i])):prop)))
		type = typeof (prop = props[i]);
		if(type == 'function') type = typeof (prop = prop(obj));
		if(isNaN(val = (type != 'number')?(parseInt((type != 'string')?_NNS(obj, prop):prop)):prop))
			val = 0;
//		console.log('value of ' + prop + ' is ' + val);
		return val + _NSumProps(obj, props, i + 1);
	}
	return 0;
	//return (i >= props.length)?0:(parseInt(_NNS(obj, props[i])) + _NSumProps(obj, props, i + 1));
}
