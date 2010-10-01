function _NMnuTglSubItms(mnuItmId)
{
	var menu = _N(mnuItmId);
	var subMenu =  (menu.ItmsPnl != null)?_N(menu.ItmsPnl):null;
	var parent = menu.parentNode;
	if(menu.IsSlct == null)
	{
		if(menu.IsMnu != null)
		{
			MnuItmGlobal = menu.id;
			document.attachEvent("onclick", _NMnuHideChldrn);
		}
		if(parent.SlctMnuItm != null)
			_NHideChldrn(parent.SlctMnuItm, true);
		menu.attachEvent("onmouseout", _NMnuTglOut);
		parent.SlctMnuItm = menu.id;
		_NMnuOutClrs(menu.TxtLbl, false)
		menu.IsSlct = true;
		if(subMenu != null && subMenu.ChildrenArray != null)
			ToggleVisibility(subMenu.id);
	}
	else
		_NHideChldrn(parent.SlctMnuItm);
}
function _NMnuTglOut()
{
	var mnuItmLbl = _N(event.srcElement.id);
	var mnuItm = mnuItmLbl.parentNode;
	var outObj = _N(event.toElement.id);
	//alert(mnuItmLbl.id + ' ' + mnuItm.id + ' ' + outObj.id);
	if(mnuItm.ItmsPnl != null && (outObj.parentNode.parentNode.id == mnuItm.ItmsPnl || outObj.id == mnuItm.ItmsPnl))
		return;
	else
	{
		var mnuId = (mnuItmLbl.SlctMnuItm != null)?mnuItmLbl.SlctMnuItm:mnuItm.id;
		_NHideChldrn(mnuId, true);
		//mnuItm.removeEventListener("mouseout", _NMnuTglOut, true);
	}
}
function _NMnuHideChldrn()
{
	_N(MnuItmGlobal).parentNode.IsClk = false;
	_NHideChldrn(MnuItmGlobal, true, true);
	document.detachEvent("onclick", _NMnuHideChldrn, true);
}
function _NHideChldrn(mnuItmId, topLvl, rmEvt)
{
	var opnMnu = _N(mnuItmId);
	if(opnMnu.ItmsPnl)
	{
		var chldMnu = _N(opnMnu.ItmsPnl);
		for(var i=0; i < chldMnu.ChildrenArray.length; ++i)
			_NHideChldrn(chldMnu.ChildrenArray[i], true);
		if(topLvl)
			_NSetProperty(opnMnu.ItmsPnl, 'style.display', 'none'); 
	}
	if(topLvl)
	{
		_NMnuOutClrs(opnMnu.TxtLbl, true);
		opnMnu.IsSlct = null;
	}
	if(rmEvt)
		document.detachEvent("onclick", _NMnuHideChldrn, true);
}
function _NMnuOutClrs(mnuItmId, isOut)
{
	var mnuItm = _N(mnuItmId);
	
	_NSetProperty(mnuItmId, "style.background", isOut?mnuItm.OtBckClr:mnuItm.OvBckClr);
	_NSetProperty(mnuItmId, "style.color", isOut?mnuItm.OtTxtClr:mnuItm.OvTxtClr);
}