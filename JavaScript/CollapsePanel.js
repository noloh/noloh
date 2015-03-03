function _NClpsPnlTgl(id, clpse)
{
	var pnl = _N(id);

	if(!pnl.AnimationStop)
		pnl.AnimationStop = function(){
			if(pnl.Opn && !pnl.Hgt)
				_NSet(id, 'style.height', '');
			pnl.AnimationStop = null;
		}
	if(pnl.Opn == false || !clpse)
	{
		var body = _N(pnl.Body);
		if(!pnl.Hgt)
		{
			body.style.display = '';
			pnl.NullHgt = body.offsetHeight + _N(pnl.Top).offsetHeight;
		}
		new _NAni(id, "style.height", pnl.Hgt?pnl.Hgt:pnl.NullHgt, 500);
		new _NAni(pnl.Body, "opacity", 100, 500);
		pnl.Opn = true;
	}
	else if(pnl.Opn != false || clpse)
	{
		pnl.style.minHeight = _N(pnl.Top).offsetHeight + 'px';
		var time = 500;
		if (pnl.InitClpse) 
		{
			time = pnl.InitClpse = 0; 
			_NSet(id, 'style.height', pnl.style.minHeight);
		}
		else 
			new _NAni(id, "style.height", _N(pnl.Top).offsetHeight, time);
		new _NAni(pnl.Body, "opacity", 'Hiding', time);
		pnl.Opn = false;
	}
}
function _NClpsPnlSetHgt(id, hgt)
{
	var pnl = _N(id);
	pnl.Hgt = hgt;
	if(hgt)
		_N(pnl.Body).style.height = (hgt - _N(pnl.Top).offsetHeight) + 'px';
}
