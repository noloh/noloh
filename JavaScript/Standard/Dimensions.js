function _NOuterWidth(id, margin)
{
	return _NOuterProp(id, margin, ['offsetWidth', 'marginLeft', 'marginRight']);
}
function _NOuterHeight(id, margin)
{	
	return _NOuterProp(id, margin, ['offsetHeight', 'marginTop', 'marginBottom']);
}
function _NOuterProp(id, margin, props)
{
	var sum, display, obj = _N(id);
	display = obj.style.display;
	if(display == 'none')
		obj.style.display = '';
	sum = obj[props[0]];
	if(margin)
	{
		var styles = window.getComputedStyle(obj, null);
		sum = _NSumProps(obj, [sum, styles[props[1]], styles[props[2]]], 0);
	}
	obj.style.display = display;
	return sum;
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
