function InitTreeList(id)
{
	var tree = document.getElementById(id);
	tree.SelectedElements = Array();
	tree.SelectedNodes = "";
}
function SelectNode(nodeId, elementId, event)
{
	var node = document.getElementById(nodeId);
	var tree = document.getElementById(node.ListId);

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
				ChangeAndSave(elementId, "style.background", "transparent");
				ChangeAndSave(elementId, "style.color", "#000000");
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
			ChangeAndSave(tree.SelectedElements[i], "style.background", "transparent");
			ChangeAndSave(tree.SelectedElements[i], "style.color", "#000000");
		}
		tree.SelectedElements = Array(elementId);
		tree.SelectedNodes = nodeId;
	}
	_NSave(tree.id, "_NSelectedNodes", tree.SelectedNodes);
	ChangeAndSave(elementId, "style.background", "#316AC5");
	ChangeAndSave(elementId, "style.color", "#FFFFFF");
}
function PlusMinusChange(panelId, iconId, nodeId)
{
	var Node = document.getElementById(nodeId);
	if(document.getElementById(panelId).style.display=="")
	{
		ChangeAndSave(panelId, "style.display", "none");
		ChangeAndSave(iconId, "src", Node.CloseSrc!=null?Node.CloseSrc:document.getElementById(Node.ListId).CloseSrc);
	}
	else 
	{
		ChangeAndSave(panelId, "style.display", "");
		ChangeAndSave(iconId, "src", Node.OpenSrc!=null?Node.OpenSrc:document.getElementById(Node.ListId).OpenSrc);
	}
}