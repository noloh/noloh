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
	/*if(event.shiftKey)
	{
		
	}
	else */
	if(event.ctrlKey)
	{
		tree.SelectedElements.push(elementId);
		if(tree.SelectedNodes != "")
			tree.SelectedNodes += "~d2~";
		tree.SelectedNodes += elementId;
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