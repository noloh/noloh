function _NTbPgSt(group)
{
	var grp = _N(group), tab = grp.GetSelectedElement();
	if(tab = _N(tab))
	{
		var prevTab = grp.PrevSelectedElement;
		if(prevTab != null)
			_NSetProperty(_N(prevTab).TabPg, 'style.display', 'none');
		if(tab.TabPg != null)
			_NSetProperty(tab.TabPg, 'style.display', '');
	}
}