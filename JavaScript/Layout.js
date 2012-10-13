function VAlign(id, prop)
{
	var top = ((_N(id).parentNode.offsetHeight - _N(id).offsetHeight)/2);
	_NSet(id, (prop || 'style.top'), ((top > 0)?top:0) + 'px');
}
function HAlign(id, prop)
{
	var left = ((_N(id).parentNode.offsetWidth - _N(id).offsetWidth)/2);
	_NSet(id, (prop || 'style.left'), ((left > 0)?left:0) + 'px');
}