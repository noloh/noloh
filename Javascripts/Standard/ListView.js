tmpMouseUp=null;
tmpLVInfo=null;

function _N_LV_ModScroll(listViewId, columnPanelId)
{
	var listView = document.getElementById(listViewId);
	var columnPanel = document.getElementById(columnPanelId);
	ChangeAndSave(columnPanelId, 'style.left', -(parseInt(listView.scrollLeft)) + "px");
	ChangeAndSave(columnPanelId, 'style.width', (parseInt(columnPanel.style.width) + (parseInt(listView.scrollLeft))) + "px");
	if(listView.scrollTop == (listView.scrollHeight - listView.clientHeight) && listView.parentNode.DataBind != null)
	{
//	if(listView.scrollTop == (listView.scrollHeight - listView.clientHeight) && listView.DataBind != null)
		listView.parentNode.DataBind.call();
		listView.parentNode.style.cursor = 'wait';
	}
}
function _N_LV_Sort(id, arr)
{
	var tmpPnl = document.getElementById(id);
	for(i=0;i<arr.length;++i)
		tmpPnl.appendChild(tmpPnl.removeChild(document.getElementById(arr[i])));
}
function _N_LV_ResizeStart(line, clmn, innrPnl)
{
	ToggleVisibility(line);
	BringToFront(line);
	var tmpClmn = document.getElementById(clmn);
	ChangeAndSave(line, 'style.left', parseInt(tmpClmn.style.left) + parseInt(tmpClmn.style.width) + parseInt(tmpClmn.parentNode.style.left) + 'px');
	document.body.addEventListener("mouseup", _N_LV_ResizeEnd, true);
	tmpLVInfo = new Object;
	tmpLVInfo.Line = line;
	tmpLVInfo.LnStart = document.getElementById(line).style.left;
	tmpLVInfo.Clmn = clmn;
	tmpLVInfo.InnPnl = innrPnl;
}
function _N_LV_ResizeEnd()
{
	document.body.removeEventListener("mouseup", _N_LV_ResizeEnd, true);
	var tmpLn = document.getElementById(tmpLVInfo.Line);
	ToggleVisibility(tmpLVInfo.Line);
	var tmpIndex;
	var tmpClmn = document.getElementById(tmpLVInfo.Clmn);
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
		var tmpInnPnl = document.getElementById(tmpLVInfo.InnPnl);
		var tmpCount = tmpInnPnl.childNodes.length;
		var tmpInnPnlChildren;
		for(i=0; i<tmpCount;++i)
		{
			tmpInnPnlChildren = tmpInnPnl.childNodes[i];
			if(tmpIndex != null)
			{
				for(j=tmpIndex;j<tmpInnPnlChildren.childNodes.length;++j)
				{
					if(j == tmpIndex)
					{
						ChangeAndSave(tmpInnPnlChildren.childNodes[j].id, 'style.width', 
							parseInt(tmpInnPnlChildren.childNodes[j].style.width) + changeX + 'px'); 
					}
					else
					{
						ChangeAndSave(tmpInnPnlChildren.childNodes[j].id, 'style.left', 
							parseInt(tmpInnPnlChildren.childNodes[j].style.left) + changeX + 'px'); 
					}
				}
			}
		}
	}
}