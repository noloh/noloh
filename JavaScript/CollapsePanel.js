function _NClpsPnlTgl(id, clpse)
{
	var pnl = _N(id);
	
	if(!pnl.AnimationStop)
		pnl.AnimationStop = function(){
			if(pnl.Opn && !pnl.Hgt)
				_NSetProperty(id, 'style.height', '');
			pnl.AnimationStop = null;
		}	
	if(clpse == false || pnl.Opn == false)
	{	
		if(!pnl.Hgt)
		{
			var body = _N(pnl.Body);
			body.style.display = '';
			pnl.NullHgt = body.offsetHeight + _N(pnl.Top).offsetHeight;
		}
		new _NAni(id, "style.height", ((pnl.Hgt)?pnl.Hgt:pnl.NullHgt), 500);
		new _NAni(pnl.Body, "opacity", 100, 500);
		pnl.Opn = true;
	}
	else if(pnl.Opn != false || clpse)
	{
		pnl.style.minHeight = _N(pnl.Top).offsetHeight + 'px';
		var time = (pnl.InitClpse)?(pnl.InitClpse = 0):500;
		new _NAni(id, "style.height", _N(pnl.Top).offsetHeight, time);
		new _NAni(pnl.Body, "opacity", 'Hiding', time);
		pnl.Opn = false;
	}
}