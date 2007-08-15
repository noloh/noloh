function SelectNode(nodeId, event)
{
	var node = document.getElementById(nodeId);
	var idx = node.ListIndex;
	var listobj = document.getElementById(document.getElementById(node.ListId).treeNodesList);
	var selIdx, i;
	/*if(event.shiftKey)
	{
		
	}
	else */
	if(event.ctrlKey)
	{
		listobj.options[idx].selected = true;
		ChangeAndSave(listobj.options[idx].text, "style.background", "#316AC5");
		//document.getElementById(listobj.options[idx].text).style.background = "#316AC5";
		ChangeAndSave(listobj.options[idx].text, "style.color", "#FFFFFF");
		//document.getElementById(listobj.options[idx].text).style.color = "#FFFFFF";
	}
	else
	{
		while(listobj.selectedIndex != -1)
		{
			selIdx = listobj.selectedIndex;
			listobj.options[selIdx].selected = false;
			ChangeAndSave(listobj.options[selIdx].text, "style.background", "transparent");
			//document.getElementById(listobj.options[selIdx].text).style.background = "transparent";
			ChangeAndSave(listobj.options[selIdx].text, "style.color", "#000000");
			//document.getElementById(listobj.options[selIdx].text).style.color = "#000000";
		}
		//alert(nodeId + " " + idx);
		listobj.options[idx].selected = true;
		ChangeAndSave(listobj.options[idx].text, "style.background", "#316AC5");
		//document.getElementById(listobj.options[idx].text).style.background = "#316AC5";
		ChangeAndSave(listobj.options[idx].text, "style.color", "#FFFFFF");
		//document.getElementById(listobj.options[idx].text).style.color = "#FFFFFF";
	}
	ChangeAndSave(listobj.id, "selectedIndex", listobj.selectedIndex);
	ChangeAndSave(listobj.id, "selectedIndices", ImplodeSelectedIndices(listobj.options));
	//alert("select node finished");
}

function PlusMinusChange(PanelId, IconId, NodeId)
{
	var Node = document.getElementById(NodeId);
	if(document.getElementById(PanelId).style.display=="")
	{
		ChangeAndSave(PanelId, "style.display", "none");
		ChangeAndSave(IconId, "src", Node.CloseSrc!=null?Node.CloseSrc:document.getElementById(Node.ListId).CloseSrc);
	}
	else 
	{
		ChangeAndSave(PanelId, "style.display", "");
		ChangeAndSave(IconId, "src", Node.OpenSrc!=null?Node.OpenSrc:document.getElementById(Node.ListId).OpenSrc);
	}
}