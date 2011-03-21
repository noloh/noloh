_N.LVInfo=null;
function _NLVModScroll(listViewId, columnPanelId, innerPanelId)
{
	var listView = _N(listViewId);
	_NSet(columnPanelId, 'style.left', -(parseInt(listView.scrollLeft)) + "px");
	_NSet(columnPanelId, 'style.width', (listView.offsetWidth + (parseInt(listView.scrollLeft))) + "px");
	
//	console.log(listView.scrollTop, _N(innerPanelId).offsetHeight);
	if((listView.scrollTop + listView.offsetHeight >= _N(innerPanelId).offsetHeight) && listView.parentNode.DataFetch)
//	while((listView.scrollTop > _N(innerPanelId).offsetHeight) && listView.parentNode.DataFetch)
	{
//		console.log(listView.scrollTop, _N(innerPanelId).offsetHeight);
//		_N(innerPanelId).style.height = (_N(innerPanelId).offsetHeight + 50) + 'px';
//		console.log(listView.parentNode.DataFetch);
		_NSet(listView.parentNode.id, '_InnerOffset', [listView.scrollTop, _N(innerPanelId).offsetHeight]);
//		ToggleVisibility(listView.parentNode._N.Loader);
		listView.parentNode.DataFetch();
/*		setTimeout(function(){
			_NLVModScroll(listViewId, columnPanelId, innerPanelId)}, 100);*/
		
	}
//		listView.parentNode.DataFetch();
}
function _NHndlClmn(columnId, innerPanelId, remove)
{
	var clmn = _N(columnId);
	if(remove || clmn.offsetTop >= clmn.parentNode.offsetHeight)
	{
		var clmnWidth = _NOuterWidth(columnId), sum = _NOuterWidth(innerPanelId) + clmnWidth;
		if(remove)
		{
			sum = sum - (2 * clmnWidth); 
			var outerWidth = _NOuterWidth(_N(innerPanelId).parentNode.id);
			if(sum < outerWidth )
				sum = outerWidth;
		}
		_NSet(innerPanelId, 'style.width', sum + 'px');
	}
}
function _NLVSlct(id)
{
	var row = _N(id);
	var panel = row.parentNode;
	
	if(!panel.SelectedRows)
		panel.SelectedRows = {};
	
	if(!event.ctrlKey && !event.metaKey)
	{
		for(var key in panel.SelectedRows)
			_NSet(key, "className", _N(key).className.replace(row.SelCls, ''));
		panel.SelectedRows = {};
		panel.StringRep = "";
	}
	else if(panel.SelectedRows[id])
	{
		event.preventDefault();
		_NSet(id, "className", row.className.replace(row.SelCls, ''));
		delete(panel.SelectedRows[id]);
		panel.StringRep = panel.StringRep.replace(id + "~d2~", "");
		_NSave(panel.parentNode.parentNode.id, "_NSelectedRows", panel.StringRep);
		return;
	}
	panel.SelectedRows[id] = true;
	panel.StringRep += id + "~d2~";
	_NSave(panel.parentNode.parentNode.id, "_NSelectedRows", panel.StringRep);
	if(row.className.indexOf(row.SelCls) == -1)
		_NSet(id, 'className', row.className + ' ' + row.SelCls);
}
function _NLVSort(id, arr)
{
	for(var pnl = _N(id), i=0; i<arr.length; ++i)
		pnl.appendChild(pnl.removeChild(_N(arr[i])));
}
function _NLVSet(column, elements)
{
	var width = _NOuterWidth(column) +'px';
	for(var i=1, len=arguments.length; i<len; ++i)
		_NSet(arguments[i] + '_W', 'style.width', width);
}
function _NLVResizeStart(line, clmn, innrPnl)
{
	ToggleVisibility(line);
	BringToFront(line);
	var left = _N(clmn).offsetLeft + _N(clmn).parentNode.offsetLeft + _NOuterWidth(clmn, true);
	_NSet(line, 'style.left', left + 'px');
	_N.LVInfo = {Line:line, Clmn:clmn, InnPnl:innrPnl, LnStart:left};
}
function _NLVResizeEnd()
{
	var line = _N(_N.LVInfo.Line), index, clmn = _N(_N.LVInfo.Clmn), parent = clmn.parentNode, changeX;
	ToggleVisibility(line.id);
	if((changeX = parseInt(line.style.left) - _N.LVInfo.LnStart) != 0)
	{
		var i, count = parent.childNodes.length, innerPnlNodes = _N(_N.LVInfo.InnPnl).childNodes, innerPnlChildren;
		for(index=0; index < count, parent.childNodes[index].id != clmn.id; ++index);
//		_NSet(innerPnl.id, 'style.width', parseInt(innerPnl.style.width) + changeX + 'px');
		count = innerPnlNodes.length;
		for(i=0; i < count; ++i)
		{
			innerPnlChildren = innerPnlNodes[i].childNodes;
			if(typeof(innerPnlChildren[index]) !== 'undefined')
				_NSet(innerPnlChildren[index].id, 'style.width', 
					parseInt(innerPnlChildren[index].style.width) + changeX + 'px'); 
		}
	}
}