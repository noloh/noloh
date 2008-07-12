function _NChgRlOvrTb(rlOvrTb, state)
{
	var tab = _N(rlOvrTb);
	if(tab.Selected || tab.cur == state)
		return;
	if(tab.Cur != null)
	{	
		_N(tab[tab.Cur]).style.display = 'none';
		_N(tab[state]).style.display = '';
		tab.Cur = state;
	}
	if(state == 'Slct')
	{
		var prevTab = tab.Group.GetSelectedElement();
		if(prevTab != null)
		{
			prevTab = _N(prevTab);
			_N(prevTab.Slct).style.display = 'none';
			_N(prevTab.Out).style.display = '';
			prevTab.Selected = false;
			_NSave(prevTab.id, 'Selected');
			prevTab.Cur = 'Out';
		}
		_N(tab.Out).style.display = 'none';
		_N(tab.Slct).style.display = '';
		_NSetProperty(rlOvrTb, 'Selected', true);
		if(tab.Select != null)
			tab.Select.call();
	}
}