function SelectNode(nodeId, listId, event)
{
	var idx = document.getElementById(nodeId).ListIndex;
	var listobj = document.getElementById(document.getElementById(listId).treeNodesList);
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

function PlusMinusChange(PanelId, IconId)
{
	var PanelObj = document.getElementById(PanelId); 
	var IconObj = document.getElementById(IconId);
	if(PanelObj.style.display=="")
	{
		ChangeAndSave(PanelObj.id, "style.display", "none");
		//PanelObj.style.display = "none"; 
		ChangeAndSave(IconObj.id, "src", IconObj.CloseSrc);
		//IconObj.src = IconObj.CloseSrc;
	}
	else 
	{
		ChangeAndSave(PanelObj.id, "style.display", "");
		//PanelObj.style.display="";
		ChangeAndSave(IconObj.id, "src", IconObj.OpenSrc);
		//IconObj.src = IconObj.OpenSrc;
	}
}