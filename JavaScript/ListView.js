_N.LVInfo=null;
function _NLVModScroll(listViewId, columnPanelId, innerPanelId)
{
	var listView = _N(listViewId);
	_NSetProperty(columnPanelId, 'style.left', -(parseInt(listView.scrollLeft)) + "px");
	_NSetProperty(columnPanelId, 'style.width', (parseInt(_N(columnPanelId).style.width) + (parseInt(listView.scrollLeft))) + "px");
	if(listView.scrollTop + listView.clientHeight >= _N(innerPanelId).offsetHeight && listView.parentNode.DataFetch)
		listView.parentNode.DataFetch();
}
function _NLVSlct(id, event)
{
	var row = _N(id);
	var panel = row.parentNode;
	
	if(!panel.SelectedRows)
		panel.SelectedRows = {};
	
	if(!event.ctrlKey && !event.metaKey)
	{
		for(var key in panel.SelectedRows)
			_NSetProperty(key, "className", _N(key).className.replace(row.SelCls, ''));
		panel.SelectedRows = {};
		panel.StringRep = "";
	}
	else if(panel.SelectedRows[id])
	{
		event.preventDefault();
		_NSetProperty(id, "className", row.className.replace(row.SelCls, ''));
		delete(panel.SelectedRows[id]);
		panel.StringRep = panel.StringRep.replace(id + "~d2~", "");
		_NSave(panel.parentNode.parentNode.id, "_NSelectedRows", panel.StringRep);
		return;
	}
	panel.SelectedRows[id] = true;
	panel.StringRep += id + "~d2~";
	_NSave(panel.parentNode.parentNode.id, "_NSelectedRows", panel.StringRep);
	if(row.className.indexOf(row.SelCls) == -1)
		_NSetProperty(id, 'className', row.className + ' ' + row.SelCls);
}
function _NLVSort(id, arr)
{
	var pnl = _N(id);
	for(i=0;i<arr.length;++i)
		pnl.appendChild(pnl.removeChild(_N(arr[i])));
}
function _NLVResizeStart(line, clmn, innrPnl)
{
	ToggleVisibility(line);
	BringToFront(line);
	var column = _N(clmn);
	_NSetProperty(line, 'style.left', parseInt(column.style.left) + parseInt(column.style.width) + parseInt(column.parentNode.style.left) + 'px');
	_N.LVInfo = {Line:line, Clmn:clmn, InnPnl:innrPnl, LnStart:_N(line).style.left};
}
function _NLVResizeEnd()
{
	var line = _N(_N.LVInfo.Line);
	ToggleVisibility(line.id);
	
	var index;
	var clmn = _N(_N.LVInfo.Clmn);
	var parent = clmn.parentNode;
	var changeX =  parseInt(line.style.left) - parseInt(_N.LVInfo.LnStart);
	
	if(changeX != 0)
	{
		var i,j;
		var count = parent.childNodes.length;
		for(i=0; i<count; ++i)
			if(parent.childNodes[i].id == clmn.id)
			{
				index = i;
				break;
			}
		var innerPnl = _N(_N.LVInfo.InnPnl);
		count = innerPnl.childNodes.length;
		var innerPnlChildren;
		_NSetProperty(innerPnl.id, 'style.width', parseInt(innerPnl.style.width) + changeX + 'px');
		
		for(i=0; i<count;++i)
		{
			innerPnlChildren = innerPnl.childNodes[i];

			for(j=index;j<innerPnlChildren.childNodes.length;++j)
			{
				if(j == index)
					_NSetProperty(innerPnlChildren.childNodes[j].id, 'style.width', 
						parseInt(innerPnlChildren.childNodes[j].style.width) + changeX + 'px'); 
				else
					_NSetProperty(innerPnlChildren.childNodes[j].id, 'style.left', 
						parseInt(innerPnlChildren.childNodes[j].style.left) + changeX + 'px'); 
			}
		}
	}
}