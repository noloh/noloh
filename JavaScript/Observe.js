function _NObserve(id, prop, alias)
{
	var info = [prop, alias != null ? alias : prop], ob = _N.Observes;
	if(ob == null)
		ob = _N.Observes = {id: [info]};
	if(ob[id] == null)
		ob[id] = [info];
	else
		ob[id].push(info);
}
function _NObserveSave()
{
	var id, ob = _N.Observes, obj, infoSet, infos, i;
	for(var id in ob)
	{
		obj = _N(id);
		if(obj == null)
		{
			if(ob.length == 1)
				_N.Observes = null;
			else
				delete _N.Observes[id];
		}
		else
		{
			infoSet = ob[id];
			infos = infoSet.length;
			for(i=0; i<infos; ++i)
				_NSave(id, infoSet[i][1], obj[infoSet[i][0]]);
		}
	}
}