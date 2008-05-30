function _NTglRlOvrLbl(id, state)
{
	var lbl = _N(id);
	if(lbl.Cur != state && (lbl.Selected == null || !lbl.Selected))
	{
		if(state == 'Slct')
		{
			var prevLbl = lbl.Group.GetSelectedElement();
			if(prevLbl != null)
			{
				prevLbl = _N(prevLbl);
				_NSetRlOvrLblClr(prevLbl.id, prevLbl['Out']);
				_NSetProperty(prevLbl.id, 'Selected', false);
				prevLbl.Cur = 'Out';
			}
			_NSetProperty(id, 'Selected', true);
			if(lbl.Select != null)
				lbl.Select.call();	
		}
		_NSetRlOvrLblClr(id, lbl[state]);
		if(lbl.onchange != null)
			lbl.onchange.call();
		lbl.Cur = state;
	}
	else if(state == 'Slct' && (lbl.Tgl))
	{
		_NSetRlOvrLblClr(id, lbl['Out']);
		_NSetProperty(id, 'Selected', false);
		if(lbl.onchange != null)
			lbl.onchange.call();
		lbl.Cur = 'Out';
	}
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