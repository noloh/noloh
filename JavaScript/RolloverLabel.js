function _NTglRlOvrLbl(id, state)
{
	var lbl = _N(id);
	if(lbl.Selected && state != 'Slct' || lbl.Cur == state)
		return;
	
	if(state == 'Slct' && lbl.Cur != 'Slct' && lbl.Group)
	{
		var prevLbl = lbl.Group.PrevSelectedElement;
		if(prevLbl != null)
			_NSetProperty(prevLbl, 'Selected', false);
	}
	_NSetRlOvrLblClr(id, lbl[state]);
	lbl.Cur = state;
	if(lbl.onchange != null)
		lbl.onchange.call();
}
function _NSetRlOvrLblClr(id, color)
{
	if(color instanceof Array)
	{
		_NSetProperty(id, 'style.color', color[0]);
		_NSetProperty(id, 'style.background', color[1]);
	}
	else
		_NSetProperty(id, 'style.color', color);
}
