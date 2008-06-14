function _NTglClpsePanel(id, titlId, bdyId, clpse)
{
	var pnl = _N(id);
	if((pnl.Opn == null || pnl.Opn) || clpse)
	{
		var tmpHgt = (pnl.style.height == '')?'':parseInt(pnl.style.height, 10);
		if(tmpHgt != pnl.Hgt)
			pnl.Hgt = tmpHgt;
		pnl.style.height = _N(titlId).style.height;
		_NSetProperty(bdyId, "style.display", "none");
		pnl.Opn = false;
	}
	else
	{
		pnl.style.height = (pnl.Hgt == '')?pnl.Hgt:pnl.Hgt + 'px';
		_NSetProperty(bdyId, "style.display", "");
		pnl.Opn = true;
	}
}