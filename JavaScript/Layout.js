function VAlign(id, prop)
{
	_NSetProperty(id, (prop || 'style.top'), ((_N(id).parentNode.offsetHeight - _N(id).offsetHeight)/2) + 'px');
}
function HAlign(id, prop)
{
	_NSetProperty(id, (prop || 'style.left'), ((_N(id).parentNode.offsetWidth - _N(id).offsetWidth)/2) + 'px');
}