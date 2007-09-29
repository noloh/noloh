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
	var tmpSum = 0;
	var index;
	for(var i= 0; i< tmpAccordion.AccordionParts.length; ++i)
	{
		if(tmpAccordion.AccordionParts[i] == accordionPart)
		{
			index = i;
			break;
		}
		var tmpAccordionPart = document.getElementById(tmpAccordion.AccordionParts[i]);
		ChangeAndSave(tmpAccordionPart.BottomPart, "style.height", "0px");
		ChangeAndSave(tmpAccordionPart.id, "style.height", document.getElementById(tmpAccordionPart.TopPart).style.height);
		tmpSum += parseInt(tmpAccordionPart.offsetHeight);
	}
	for(var j = tmpAccordion.AccordionParts.length - 1; j > index; --j)
	{
		tmpAccordionPart = document.getElementById(tmpAccordion.AccordionParts[j]);
		ChangeAndSave(tmpAccordionPart.BottomPart, 'style.height', '0px');
		ChangeAndSave(tmpAccordionPart.BottomPart, "style.visibility", 'hidden');
		ChangeAndSave(tmpAccordionPart.id, "style.height", document.getElementById(tmpAccordionPart.TopPart).style.height);
		tmpSum += parseInt(tmpAccordionPart.offsetHeight);
	}
	
	tmpAccordionPart = document.getElementById(tmpAccordion.AccordionParts[index]);
	//var tmpHeight = parseInt(tmpAccordion.style.height) - tmpSum -  parseInt(tmpAccordionPart.style.top);
	ChangeAndSave(tmpAccordionPart.id, "style.height", parseInt(tmpAccordion.style.height) - tmpSum -  parseInt(tmpAccordionPart.style.top) + "px");
	ChangeAndSave(tmpAccordionPart.BottomPart, "style.height", parseInt(tmpAccordionPart.style.height) - parseInt(document.getElementById(tmpAccordionPart.TopPart).style.height) + "px");
	ChangeAndSave(tmpAccordionPart.BottomPart, "style.visibility", "inherit");
	ChangeAndSave(tmpAccordion.id, "selectedIndex", index);
}
function SetAccordionPart(accordionPart, topPart, bottomPart)
{
	var tmpAccordionPart = document.getElementById(accordionPart);
	tmpAccordionPart.TopPart = topPart;
	tmpAccordionPart.BottomPart = bottomPart;
}