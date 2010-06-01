function _NTreeInit(id)
{
	var tree = _N(id);
	tree.SelectedElements = [];
	tree.SelectedNodes = "";
}
function _NTreeClick(nodeId)
{
	var tree = _N(_N(nodeId).ListId);

	if (document.selection && document.selection.createRange().text != "")
		document.selection.empty();
	else if (window.getSelection)
		window.getSelection().removeAllRanges();
	
	if(event && event.ctrlKey)
		_NTreeSlctTgl(nodeId);
	else
		_NTreeSlctOne(nodeId);
}
function _NTreeSlct(nodeId)
{
	var node = _N(nodeId);
	var tree = _N(node.ListId);
	_NSave(tree.id, "_NSelectedNodes", tree.SelectedNodes);
	_NSetProperty(node._N.El, "style.background", "#316AC5");
	_NSetProperty(node._N.El, "style.color", "#FFFFFF");
}
function _NTreeSlctOne(nodeId)
{
	var node = _N(nodeId);
	var tree = _N(node.ListId), i;
	for(i = 0; i < tree.SelectedElements.length; ++i)
	{
		_NSetProperty(tree.SelectedElements[i], "style.background", "transparent");
		_NSetProperty(tree.SelectedElements[i], "style.color", "#000000");
	}
	tree.SelectedElements = [node._N.El];
	tree.SelectedNodes = nodeId;
	_NTreeSlct(nodeId);
//	_NTreeSlct(nodeId, elementId);
}
function _NTreeSlctTgl(nodeId)
{
	var elementId, node = _N(nodeId);
	elementId = node._N.El;
	var tree = _N(node.ListId), elementsLength = tree.SelectedElements.length, i;
	for(i=0; i<elementsLength; ++i)
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
	_NTreeSlct(nodeId);
}
function _NTreeTgl(panelId, iconId, nodeId)
{
	var node = _N(nodeId);
	if(_N(panelId).style.display=="")
	{
		_NSetProperty(panelId, "style.display", "none");
		_NSetProperty(iconId, "src", node.CloseSrc!=null?node.CloseSrc:_N(node.ListId).CloseSrc);
	}
	else 
	{
		_NSetProperty(panelId, "style.display", "");
		_NSetProperty(iconId, "src", node.OpenSrc!=null?node.OpenSrc:_N(node.ListId).OpenSrc);
	}
}