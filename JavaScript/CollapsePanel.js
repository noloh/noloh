function _NClpsPnlTgl(id, clpse)
{
	var pnl = _N(id), body = _N(pnl.Body);
	
	if(pnl.NullHgt && !body.AnimationStop)
		body.AnimationStop = function() {if(pnl.Opn) pnl.style.height = ''; body.style.height = ''; body.OrigHgt = body.AnimationStop = null;}
			
	if(clpse == false || pnl.Opn == false)
	{
		if(pnl.NullHgt)
		{
			if(!body.OrigHgt)
			{
				body.style.display = '';
				body.OrigHgt = body.offsetHeight;
				body.style.height = '0px';
			}
			pnl.InHgt = body.OrigHgt;
		}
		new _NAni(pnl.Body, "style.height", pnl.InHgt, 500);
		pnl.Opn = true;
	}
	else if(pnl.Opn != false || clpse)
	{
		if(pnl.Animates == 1)
		{
			if(!pnl.NullHgt)
				_NSetProperty(pnl.Body, "style.height", '0px');
			_NSetProperty(id, "style.height", _N(pnl.Top).offsetHeight + 'px');
			pnl.Animates = null;
		}
		else
		{
			if(!body.OrigHgt)
				body.OrigHgt = body.offsetHeight;
			pnl.style.minHeight = _N(pnl.Top).offsetHeight + 'px';
			new _NAni(pnl.Body, "style.height", 'Hiding', 500);
		}
		pnl.Opn = false;
	}
}
function _NClpsPnlInHgt(id)
{
	var pnl = _N(id);
	if(pnl.style.height == '')
	{	
		pnl.NullHgt = true;
		pnl.InHgt = _N(pnl.Body).offsetHeight;
	}
	else
	{
		if(pnl.NullHgt)
			delete pnl.NullHgt;
		_NSetProperty(pnl.Body, 'style.height', (pnl.InHgt = pnl.offsetheightHeight - _N(pnl.Top).offsetHeight) + 'px');
	}
}