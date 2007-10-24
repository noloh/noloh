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
	var tmpHeight;
	for(var i= 0; i< tmpAccordion.AccordionParts.length; ++i)
	{
		if(tmpAccordion.AccordionParts[i] == accordionPart)
			index = i;
		else
		{
			var tmpAccordionPart = document.getElementById(tmpAccordion.AccordionParts[i]);
			ChangeAndSave(tmpAccordionPart.BottomPart, "style.display", "none");
			tmpHeight = document.getElementById(tmpAccordionPart.TopPart).style.height;
			ChangeAndSave(tmpAccordionPart.id, "style.height", tmpHeight);
			tmpSum += parseInt(tmpHeight);
		}
	};
	tmpAccordionPart = document.getElementById(tmpAccordion.AccordionParts[index]);
	ChangeAndSave(tmpAccordionPart.id, "style.height", parseInt(tmpAccordion.style.height) - tmpSum + "px");
	ChangeAndSave(tmpAccordionPart.BottomPart, "style.height", parseInt(tmpAccordionPart.style.height) - parseInt(document.getElementById(tmpAccordionPart.TopPart).style.height) + "px");
	ChangeAndSave(tmpAccordionPart.BottomPart, "style.display", "");
	ChangeAndSave(tmpAccordion.id, "selectedIndex", index);
}
function SetAccordionPart(accordionPart, topPart, bottomPart)
{
	var tmpAccordionPart = document.getElementById(accordionPart);
	tmpAccordionPart.TopPart = topPart;
	tmpAccordionPart.BottomPart = bottomPart;
}
function N_ScrollCheck(bodyId)
{
	var body = document.getElementById(bodyId);
	if(body.scrollTop == (body.scrollHeight - body.clientHeight) && body.parentNode.DataBind != null)
		body.parentNode.DataBind.call();
}