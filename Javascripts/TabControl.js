function _NStTbPg(tabControl, group)
{
	var grp = window[group];
	var tab = grp.GetSelectedElement();
	if(tab != null)
	{
		tab = _N(tab);
		var tabControl = _N(tabControl);
		if(tabControl.CurTabPg != null)
			_NSetProperty(tabControl.CurTabPg, 'style.display', 'none');
		if(tab.TabPg != null)
		{
			_NSetProperty(tab.TabPg, 'style.display', '');
			tabControl.CurTabPg = tab.TabPg;
		}
	}
}