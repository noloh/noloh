function _NStrPnlAdd(pnl, obj)
{
	var panel = _N(pnl), object = _N(obj), sum;

	if(panel._N.AutX)
	{
		var old = panel.style.width;
		panel.style.width = '5000px';
		sum = object.offsetLeft + _NOuterWidth(obj, true);
		panel.style.width = old;
		_NStrPnl(sum, panel.clientWidth, pnl, 'style.width', sum);
	}
	if(panel._N.AutY)
	{
		sum = obj.offsetTop + _NOuterHeight(obj, true);
		_NStrPnl(sum, panel.clientHeight, pnl, 'style.height', sum);
	}
}
function _NStrPnl(sum, compare, obj, prop, to)
{
//	console.log(sum, compare, obj, prop);
	if(sum > compare)
		new _NAni(obj, prop, to, 0);
}
function _NStrResize(id)
{
	var obj = _N(id), max;
	if(obj._N.AutX)
	{
		max = _NStrPnlMaxChld(obj, [function (child){return _NOuterWidth(child.id, true)}, ['offsetLeft']]);
//		console.log(max + ' is the max');
		_NStrPnl(obj.clientWidth, max, id, 'style.width', max);
	}	
	if(obj._N.AutY)
	{
		_NStrPnlMaxChld(obj, [function (child){return _NOuterHeight(child.id, true)}, ['offsetTop']]);
		_NStrPnl(obj.clientHeight, max, id, 'style.height', max);
	}
}
/**
* obj - object whose children you wish to get max prop of
* props - array of namespaces to get to property. If array of arrays, arrays will be added to return max sum of pair
*/
function _NStrPnlMaxChld(obj, props)
{
	var count = obj.childNodes.length, val = 0, newVal, i;
	for(i=0; i<count; ++i)
	{
		newVal = _NSumProps(obj.childNodes[i], props, 0);
		if(newVal > val)
			val = newVal;	
	}
	return val;
}