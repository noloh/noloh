tmpMouseUp=null;
tmpLVInfo=null;

function _N_LV_ModScroll(listViewId, columnPanelId)
{
	var listView = _N(listViewId);
	var columnPanel = _N(columnPanelId);
	_NSetProperty(columnPanelId, 'style.left', -(parseInt(listView.scrollLeft)) + "px");
	_NSetProperty(columnPanelId, 'style.width', (parseInt(columnPanel.style.width) + (parseInt(listView.scrollLeft))) + "px");
	if(listView.scrollTop == (listView.scrollHeight - listView.clientHeight) && listView.parentNode.DataBind != null)
	{
//	if(listView.scrollTop == (listView.scrollHeight - listView.clientHeight) && listView.DataBind != null)
		listView.parentNode.DataBind.call();
		listView.parentNode.style.cursor = 'wait';
	}
}
function _N_LV_Sort(id, arr)
{
	var tmpPnl = _N(id);
	for(i=0;i<arr.length;++i)
		tmpPnl.appendChild(tmpPnl.removeChild(_N(arr[i])));
}
function _N_LV_ResizeStart(line, clmn, innrPnl)
{
	ToggleVisibility(line);
	BringToFront(line);
	var tmpClmn = _N(clmn);
	_NSetProperty(line, 'style.left', parseInt(tmpClmn.style.left) + parseInt(tmpClmn.style.width) + parseInt(tmpClmn.parentNode.style.left) + 'px');
	document.attachEvent("onmouseup", _N_LV_ResizeEnd);
	tmpLVInfo = new Object;
	tmpLVInfo.Line = line;
	tmpLVInfo.LnStart = _N(line).style.left;
	tmpLVInfo.Clmn = clmn;
	tmpLVInfo.InnPnl = innrPnl;
}
function _N_LV_ResizeEnd()
{
	document.detachEvent("onmouseup", _N_LV_ResizeEnd);
	var tmpLn = _N(tmpLVInfo.Line);
	ToggleVisibility(tmpLVInfo.Line);
	var tmpIndex;
	var tmpClmn = _N(tmpLVInfo.Clmn);
	var tmpParent = tmpClmn.parentNode;
	var changeX =  parseInt(tmpLn.style.left) - parseInt(tmpLVInfo.LnStart);
	if(changeX != 0)
	{
		var i,j;
		for(i=0; i<tmpParent.childNodes.length;++i)
			if(tmpParent.childNodes[i].id == tmpClmn.id)
			{
				tmpIndex = i;
				break;
			}
		var tmpInnPnl = _N(tmpLVInfo.InnPnl);
		var tmpCount = tmpInnPnl.childNodes.length;
		var tmpInnPnlChildren;
		_NSetProperty(tmpInnPnl.id, 'style.width', parseInt(tmpInnPnl.style.width) + changeX + 'px');
		for(i=0; i<tmpCount;++i)
		{
			tmpInnPnlChildren = tmpInnPnl.childNodes[i];
			if(tmpIndex != null)
			{
				for(j=tmpIndex;j<tmpInnPnlChildren.childNodes.length;++j)
				{
					var tmpChild = tmpInnPnlChildren.childNodes[j];
					if(j == tmpIndex)
					{
						var tmpVal = (tmpChild.style.width != '')? tmpChild.style.width :tmpChild.offsetWidth;
						_NSetProperty(tmpInnPnlChildren.childNodes[j].id, 'style.width', 
							parseInt(tmpVal) + changeX + 'px'); 
					}
					else
					{
						_NSetProperty(tmpInnPnlChildren.childNodes[j].id, 'style.left', 
							parseInt(tmpChild.style.left) + changeX + 'px'); 
					}
				}
			}
		}
	}
}