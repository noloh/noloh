function _NRlLblTgl(id, state)
{
	var lbl = _N(id);
	if(lbl.Selected && state != 'Slct' || lbl.Cur == state)
		return;
	
	/*if(state == 'Slct' && lbl.Cur != 'Slct' && lbl.Group)
	{
		var prevLbl = lbl.Group.PrevSelectedElement;
		if(prevLbl != null)
			_NSet(prevLbl, 'Selected', false);
	}*/
	_NRlLblSetClr(id, lbl[state]);
	lbl.Cur = state;
	if(lbl.onchange)
		lbl.onchange();
}
function _NRlLblSetClr(id, color)
{
	if(color instanceof Array)
	{
		_NSet(id, 'style.color', color[0]);
		_NSet(id, 'style.background', color[1]);
	}
	else
		_NSet(id, 'style.color', color);
}
