function _NTbPgSt(group)
{
	var grp = _N(group), tab = grp.GetSelectedElement();
	if(tab = _N(tab))
	{
		var prevTab = grp.PrevSelectedElement;
		if(prevTab != null)
			_NSet(_N(prevTab).TabPg, 'style.visibility', 'hidden');
		if(tab.TabPg != null)
			_NSet(tab.TabPg, 'style.visibility', 'inherit');
	}
}
function _NTbPgRm(id)
{
	var group = _N(id).Group, el;
	var idx = _N(id).Group.Remove(id);
	idx = (idx > 0)?idx -1 : 0;
	if(group.Elements[idx])
		_NSet(group.Elements[idx], 'Selected', true);
}
function _NTbPgScrl(id, prop, sign)
{
	var tabs = _N(id);
	var tabScroller = tabs.parentNode, tabWidth = _NOuterWidth(id, true);
	
	if(tabWidth >= tabScroller.offsetWidth)
	{
		var destination = tabs.offsetLeft + (sign * tabScroller._N.scrollincrement);
		if(sign == -1 && destination + tabWidth < tabScroller.clientWidth)
			destination =  tabScroller.clientWidth - tabWidth;
		else if(sign == 1 && destination > 0)
			destination = 0;
		new _NAni(id, prop, destination, tabScroller.scrollduration);
	}
}
function _NTbPgScrlChk(id)
{
	var tabBar = _N(id), sum, display = '';
	var tabScroller = tabBar.parentNode;
	if(tabScroller._N.auto)
	{
		var back = _N(tabScroller._N.back), next = _N(tabScroller._N.next), outerWidth = _NOuterWidth(id, true);
//		console.log(outerWidth +  ' is the outer width');
//		console.log(tabScroller.clientWidth +  ' is the scroller width');
		
		if(back.style.display == 'none' && outerWidth > tabScroller.clientWidth)
		{
			back.style.display = '';
			next.style.display = '';
			sum = (tabScroller.clientWidth - (_NOuterWidth(back.id, true) + _NOuterWidth(next.id, true))) + 'px';
		}
		else if(back.style.display !== 'none' && outerWidth < tabScroller.offsetWidth)
		{
			sum = tabScroller.parentNode.clientWidth + 'px';
			display = 'none';
			new _NAni(id, 'style.left', 0, tabScroller.scrollduration);
		}
		else
			return;
		_NSet(tabScroller.id, 'style.width', sum);
		_NSet(back.id, 'style.display', display);
		_NSet(next.id, 'style.display', display);
	}
}