function AddAccordionPart(accordion, accordionPart)
{
	var tmpAccordion = document.getElementById(accordion);
	if(tmpAccordion.AccordionParts == null)
		tmpAccordion.AccordionParts = new Array();
	tmpAccordion.AccordionParts[tmpAccordion.AccordionParts.length] = accordionPart;
}
function ExpandAccordionPart(accordion, accordionPart)
{
	var tmpAccordion = document.getElementById(accordion);
	var tmpAbovePart;
	var tmpBelowTop;
	var whatIndex;
	for(var i= 0; i< tmpAccordion.AccordionParts.length; i++)
	{
		if(tmpAccordion.AccordionParts[i] == accordionPart)
		{
//			if(tmpAccordion.selectedIndex == i)
//				return;
			whatIndex = i;
			break;
		}
		if(i != 0)
			tmpAbovePart = document.getElementById(tmpAccordion.AccordionParts[i-1]);
		var tmpAccordionPart = document.getElementById(tmpAccordion.AccordionParts[i]);
		ChangeAndSave(tmpAccordionPart.BottomPart, "style.height", "0px");
		ChangeAndSave(tmpAccordionPart.id, "style.height", document.getElementById(tmpAccordionPart.TopPart).style.height);
		if(tmpAbovePart != null)
			ChangeAndSave(tmpAccordionPart.id, "style.top", parseInt(tmpAbovePart.style.height) + parseInt(tmpAbovePart.style.top) + "px");
		else
			ChangeAndSave(tmpAccordionPart.id, "style.top", "0px");
	}
	for(var j = tmpAccordion.AccordionParts.length - 1; j > whatIndex; j--)
		{
		if(j != tmpAccordion.AccordionParts.length - 1)
			tmpBelowTop = parseInt(document.getElementById(tmpAccordion.AccordionParts[j+1]).style.top);
		else 
			tmpBelowTop =  parseInt(tmpAccordion.style.height); 
		tmpAccordionPart = document.getElementById(tmpAccordion.AccordionParts[j]);
		ChangeAndSave(tmpAccordionPart.BottomPart, "style.height", "0px");
		ChangeAndSave(tmpAccordionPart.BottomPart, "style.visibility", "hidden");
		ChangeAndSave(tmpAccordionPart.id, "style.height", document.getElementById(tmpAccordionPart.TopPart).style.height);
		ChangeAndSave(tmpAccordionPart.id, "style.top",  tmpBelowTop - parseInt(document.getElementById(tmpAccordionPart.TopPart).style.height) + "px");
		}
	var isThere = false;
	if(whatIndex > 0)
		{
		tmpAbovePart = document.getElementById(tmpAccordion.AccordionParts[whatIndex-1]);
		isThere = true;
		}
	if(whatIndex != tmpAccordion.AccordionParts.length - 1)
		tmpBelowTop = parseInt(document.getElementById(tmpAccordion.AccordionParts[whatIndex+1]).style.top);
	else 
		tmpBelowTop = parseInt(tmpAccordion.style.height); 
	tmpAccordionPart = document.getElementById(tmpAccordion.AccordionParts[whatIndex]);
	ChangeAndSave(tmpAccordionPart.id, "style.top", (isThere == true)?parseInt(tmpAbovePart.style.height) + parseInt(tmpAbovePart.style.top) + "px" : 0 + "px"); 
	ChangeAndSave(tmpAccordionPart.id, "style.height", tmpBelowTop - parseInt(tmpAccordionPart.style.top) + "px");
	ChangeAndSave(tmpAccordionPart.BottomPart, "style.height", parseInt(tmpAccordionPart.style.height) - parseInt(document.getElementById(tmpAccordionPart.TopPart).style.height) + "px");
	ChangeAndSave(tmpAccordionPart.BottomPart, "style.visibility", "inherit");
	ChangeAndSave(tmpAccordion.id, "selectedIndex", whatIndex);
}
function SetAccordionPart(accordionPart, topPart, bottomPart)
{
	var tmpAccordionPart = document.getElementById(accordionPart);
	tmpAccordionPart.TopPart = topPart;
	tmpAccordionPart.BottomPart = bottomPart;
}