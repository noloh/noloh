function _NTglSubMnuItms(mnuItmId)
{
	var menu = document.getElementById(mnuItmId);
	var subMenu =  (menu.ItmsPnl != null)?document.getElementById(menu.ItmsPnl):null;
	var tmpParent = menu.parentNode;
	if(menu.IsSlct == null)
	{
		if(menu.IsMnu != null)
		{
			MnuItmGlobal = menu.id;
			document.addEventListener("click", _NHideMnuChldrn, true);
		}
		if(tmpParent.SlctMnuItm != null)
			_NHideChldrn(tmpParent.SlctMnuItm, true, false);
		menu.addEventListener("mouseout", _NTglMnuOut, true);
		tmpParent.SlctMnuItm = menu.id;
		ChangeMenuOutColors(menu.TxtLbl, false)
		menu.IsSlct = true;
		if(subMenu != null && subMenu.ChildrenArray != null)
			ToggleVisibility(subMenu.id);
	}
	else
		_NHideChldrn(tmpParent.SlctMnuItm, false, false);
}
function _NTglMnuOut(event)
{
	var mnuItmLbl = document.getElementById(event.target.id);
	var mnuItm = mnuItmLbl.parentNode;
	var outObj = document.getElementById(event.relatedTarget.id);
	//alert(mnuItmLbl.id + ' ' + mnuItm.id + ' ' + outObj.id);
	if(mnuItm.ItmsPnl != null && (outObj.parentNode.parentNode.id == mnuItm.ItmsPnl || outObj.id == mnuItm.ItmsPnl))
		return;
	else
	{
		var mnuId = (mnuItmLbl.SlctMnuItm != null)?mnuItmLbl.SlctMnuItm:mnuItm.id;
		_NHideChldrn(mnuId, true, false);
		//mnuItm.removeEventListener("mouseout", _NTglMnuOut, true);
	}
}
function _NHideMnuChldrn(event)
{
	document.getElementById(MnuItmGlobal).parentNode.IsClk = false;
	_NHideChldrn(MnuItmGlobal, true, true);
	document.removeEventListener("click", _NHideMnuChldrn, true);
}
function _NHideChldrn(mnuItmId, topLvl, rmEvt)
{
	var opnMnu = document.getElementById(mnuItmId);
	if(opnMnu.ItmsPnl != null)
	{
		var chldMnu = document.getElementById(opnMnu.ItmsPnl);
		for(var i=0; i < chldMnu.ChildrenArray.length; ++i)
			_NHideChldrn(chldMnu.ChildrenArray[i], true, false);
		if(topLvl)
			ChangeAndSave(opnMnu.ItmsPnl, 'style.display', 'none'); 
	}
	if(topLvl)
	{
		ChangeMenuOutColors(opnMnu.TxtLbl, true);
		opnMnu.IsSlct = null;
	}
	if(rmEvt)
		document.removeEventListener("click", _NHideMnuChldrn, true);
}
function ChangeMenuOutColors(mnuItmId, isOut)
{
	var tmpMnuItm = document.getElementById(mnuItmId);
	if(isOut)
	{
		ChangeAndSave(mnuItmId, "style.background", tmpMnuItm.OtBckClr);
		ChangeAndSave(mnuItmId, "style.color", tmpMnuItm.OtTxtClr);
	}
	else
	{
		ChangeAndSave(mnuItmId, "style.background", tmpMnuItm.OvBckClr);
		ChangeAndSave(mnuItmId, "style.color", tmpMnuItm.OvTxtClr);
	}
}