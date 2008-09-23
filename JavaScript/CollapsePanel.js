function _NTglClpsePanel(id, titlId, bdyId, clpse)
{
	var pnl = _N(id);
	if((pnl.Opn == null || pnl.Opn) || clpse)
	{
		var hgt = (pnl.style.height == '')?'':parseInt(pnl.style.height, 10);
		if(hgt != pnl.Hgt)
			pnl.Hgt = hgt;
		pnl.style.height = _N(titlId).style.height;
		_NSetProperty(bdyId, "style.display", "none");
		pnl.Opn = false;
		if(pnl.Collapse != null)
			pnl.Collapse.call();
	}
	else
	{
		pnl.style.height = (pnl.Hgt == '')?pnl.Hgt:pnl.Hgt + 'px';
		_NSetProperty(bdyId, "style.display", "");
		pnl.Opn = true;
		if(pnl.Expand != null)
			pnl.Expand.call();
	}
}