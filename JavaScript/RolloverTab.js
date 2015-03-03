function _NRlTbChg(rlOvrTb, state)
{
	var tab = _N(rlOvrTb);
	if(!tab.Cur)
		tab.Cur = 'Out';
		
	if(tab.Selected && state != 'Slct' || tab.Cur == state)
		return;
	
	if(tab.Cur != null)
	{
		_N(tab[tab.Cur]).style.display = 'none';
		_N(tab[state]).style.display = '';
		tab.Cur = state;
	}
	if(state == 'Slct')
	{
		/*if(tab.Group)
		{
			var prevTab = tab.Group.PrevSelectedElement;
			if(prevTab != null)
				_NSet(prevTab, 'Selected', false);
		}*/
		_N(tab.Out).style.display = 'none';
		_N(tab.Slct).style.display = '';
	}
}
function _NLeave(id)
{
	_NSet(id, '_NOblivion', 1);
	_NRem(id);
	var obj = _N(id);
	if(obj.Leave)
		obj.Leave.call(obj);
}