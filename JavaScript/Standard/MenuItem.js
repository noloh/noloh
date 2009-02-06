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
			document.addEventListener("click", _NMnuHideChldrn, true);
		}
		//if(subMenu)
		//	subMenu.addEventListener("mouseout", _NTimeToggle, true);
		 
		if(parent.SlctMnuItm != null)
		{
			//clearTimeout(_N(tmpParent).Hide);
			_NHideChldrn(parent.SlctMnuItm, true);
		}
		menu.addEventListener("mouseout", _NMnuTglOut, true);
		parent.SlctMnuItm = menu.id;
		_NMnuOutClrs(menu.TxtLbl, false)
		menu.IsSlct = true;
		if(subMenu && subMenu.ChildrenArray)
			ToggleVisibility(subMenu.id);
	}
	else
		_NHideChldrn(parent.SlctMnuItm);
}
function _NTimeToggle()
{
	//console.log('mouseout triggered of ' + event.currentTarget.id); 
	//var menu = event.currentTarget;
	//console.log(menu.Hide);
	/*if(menu.Hide)
	{
		console.log('Clearing Timer from ' + menu.id);
		clearTimeout(menu.Hide);
	}*/
	//console.log('Selected MenuItem is ' + menu.SlctMnuItm);
	//if(menu.SlctMnuItm)
	//{
	//	console.log('Adding Timer to ' + menu.id);
	//	//menu.Hide = setTimeout(_NMnuHideChldrn, /*menu.HideDly*/1000);
	//}
}
function _NMnuTglOut(event)
{
	var mnuItmLbl = _N(event.target.id);
	var mnuItm = mnuItmLbl.parentNode;
	var outObj = _N(event.relatedTarget.id);
	//console.log(mnuItmLbl.id + ' ' + mnuItm.id + ' ' + outObj.id);
	if(mnuItm.ItmsPnl != null && (outObj.parentNode.parentNode.id == mnuItm.ItmsPnl || outObj.id == mnuItm.ItmsPnl))
		return;
	else
	{
		var mnuId = (mnuItmLbl.SlctMnuItm != null)?mnuItmLbl.SlctMnuItm:mnuItm.id;
		_NHideChldrn(mnuId, true);
	}
}
function _NMnuHideChldrn(event)
{
	//console.log('_NMnuHideChldrn');
	_N(MnuItmGlobal).parentNode.IsClk = false;
	_NHideChldrn(MnuItmGlobal, true, true);
	document.removeEventListener("click", _NMnuHideChldrn, true);
}
function _NHideChldrn(mnuItmId, topLvl, rmEvt)
{
	var opnMnu = _N(mnuItmId);
	if(opnMnu.ItmsPnl != null)
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
		document.removeEventListener("click", _NMnuHideChldrn, true);
}
function _NMnuOutClrs(mnuItmId, isOut)
{
	var mnuItm = _N(mnuItmId);
	
	_NSetProperty(mnuItmId, "style.background", isOut?mnuItm.OtBckClr:mnuItm.OvBckClr);
	_NSetProperty(mnuItmId, "style.color", isOut?mnuItm.OtTxtClr:mnuItm.OvTxtClr);
}