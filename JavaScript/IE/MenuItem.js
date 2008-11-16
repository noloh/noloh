function _NMnuTglSubItms(mnuItmId)
{
	var menu = _N(mnuItmId);
	var subMenu =  (menu.ItmsPnl != null)?_N(menu.ItmsPnl):null;
	var tmpParent = menu.parentNode;
	if(menu.IsSlct == null)
	{
		if(menu.IsMnu != null)
		{
			MnuItmGlobal = menu.id;
			document.attachEvent("onclick", _NMnuHideChldrn);
		}
		if(tmpParent.SlctMnuItm != null)
			_NHideChldrn(tmpParent.SlctMnuItm, true, false);
		menu.attachEvent("onmouseout", _NMnuTglOut);
		tmpParent.SlctMnuItm = menu.id;
		_NMnuOutClrs(menu.TxtLbl, false)
		menu.IsSlct = true;
		if(subMenu != null && subMenu.ChildrenArray != null)
			ToggleVisibility(subMenu.id);
	}
	else
		_NHideChldrn(tmpParent.SlctMnuItm, false, false);
}
function _NMnuTglOut(event)
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
		_NHideChldrn(mnuId, true, false);
		//mnuItm.removeEventListener("mouseout", _NMnuTglOut, true);
	}
}
function _NMnuHideChldrn(event)
{
	_N(MnuItmGlobal).parentNode.IsClk = false;
	_NHideChldrn(MnuItmGlobal, true, true);
	document.detachEvent("onclick", _NMnuHideChldrn, true);
}
function _NHideChldrn(mnuItmId, topLvl, rmEvt)
{
	var opnMnu = _N(mnuItmId);
	if(opnMnu.ItmsPnl != null)
	{
		var chldMnu = _N(opnMnu.ItmsPnl);
		for(var i=0; i < chldMnu.ChildrenArray.length; ++i)
			_NHideChldrn(chldMnu.ChildrenArray[i], true, false);
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
	var tmpMnuItm = _N(mnuItmId);
	if(isOut)
	{
		_NSetProperty(mnuItmId, "style.background", tmpMnuItm.OtBckClr);
		_NSetProperty(mnuItmId, "style.color", tmpMnuItm.OtTxtClr);
	}
	else
	{
		_NSetProperty(mnuItmId, "style.background", tmpMnuItm.OvBckClr);
		_NSetProperty(mnuItmId, "style.color", tmpMnuItm.OvTxtClr);
	}
}