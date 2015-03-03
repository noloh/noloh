function _NSecRemind(id, prop)
{
	var val = _N(id)[prop];
	_NSave(id, prop, val);
	_N.Saved[id][prop] = '';
}