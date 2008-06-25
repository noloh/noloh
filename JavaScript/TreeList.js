function InitTreeList(id)
{
	var tree = _N(id);
	tree.SelectedElements = [];
	tree.SelectedNodes = "";
}
function SelectNode(nodeId, elementId, event)
{
	var node = _N(nodeId);
	var tree = _N(node.ListId);

	if (document.selection && document.selection.createRange().text != "")
		document.selection.empty();
	else if (window.getSelection)
		window.getSelection().removeAllRanges();
	
	if(event.ctrlKey)
	{
		var elementsLength = tree.SelectedElements.length;
		for(var i=0; i<elementsLength; ++i)
			if(tree.SelectedElements[i] == elementId)
			{
				tree.SelectedElements.splice(i, 1);
				tree.SelectedNodes = tree.SelectedNodes.replace(i==0
					? (elementsLength==1?nodeId:(nodeId+"~d2~"))
					: ("~d2~"+nodeId), "");
				_NSave(tree.id, "_NSelectedNodes", tree.SelectedNodes);
				_NSetProperty(elementId, "style.background", "transparent");
				_NSetProperty(elementId, "style.color", "#000000");
				return;
			}
		tree.SelectedElements.push(elementId);
		if(tree.SelectedNodes != "")
			tree.SelectedNodes += "~d2~";
		tree.SelectedNodes += nodeId;
	}
	else
	{
		for(i = 0; i < tree.SelectedElements.length; ++i)
		{
			_NSetProperty(tree.SelectedElements[i], "style.background", "transparent");
			_NSetProperty(tree.SelectedElements[i], "style.color", "#000000");
		}
		tree.SelectedElements = [elementId];
		tree.SelectedNodes = nodeId;
	}
	_NSave(tree.id, "_NSelectedNodes", tree.SelectedNodes);
	_NSetProperty(elementId, "style.background", "#316AC5");
	_NSetProperty(elementId, "style.color", "#FFFFFF");
}
function PlusMinusChange(panelId, iconId, nodeId)
{
	var Node = _N(nodeId);
	if(_N(panelId).style.display=="")
	{
		_NSetProperty(panelId, "style.display", "none");
		_NSetProperty(iconId, "src", Node.CloseSrc!=null?Node.CloseSrc:_N(Node.ListId).CloseSrc);
	}
	else 
	{
		_NSetProperty(panelId, "style.display", "");
		_NSetProperty(iconId, "src", Node.OpenSrc!=null?Node.OpenSrc:_N(Node.ListId).OpenSrc);
	}
}