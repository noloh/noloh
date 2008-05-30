function _NAddAccordPt(accordion, accordionPart)
{
	var tmpAccordion = _N(accordion);
	if(tmpAccordion.AccordionParts == null)
		tmpAccordion.AccordionParts = new Array();
	tmpAccordion.AccordionParts[tmpAccordion.AccordionParts.length] = accordionPart;
}
function _NExpandAccordPt(accordion, accordionPart)
{
	var tmpAccordion = _N(accordion);
	var tmpSum = 0;
	var index;
	var tmpHeight;
	for(var i= 0; i< tmpAccordion.AccordionParts.length; ++i)
	{
		if(tmpAccordion.AccordionParts[i] == accordionPart)
			index = i;
		else
		{
			var tmpAccordionPart = _N(tmpAccordion.AccordionParts[i]);
			_NSetProperty(tmpAccordionPart.BottomPart, "style.display", "none");
			tmpHeight = _N(tmpAccordionPart.TopPart).style.height;
			_NSetProperty(tmpAccordionPart.id, "style.height", tmpHeight);
			tmpSum += parseInt(tmpHeight);
		}
	};
	tmpAccordionPart = _N(tmpAccordion.AccordionParts[index]);
	_NSetProperty(tmpAccordionPart.id, "style.height", parseInt(tmpAccordion.style.height) - tmpSum + "px");
	_NSetProperty(tmpAccordionPart.BottomPart, "style.height", parseInt(tmpAccordionPart.style.height) - parseInt(_N(tmpAccordionPart.TopPart).style.height) + "px");
	_NSetProperty(tmpAccordionPart.BottomPart, "style.display", "");
	_NSetProperty(tmpAccordion.id, "selectedIndex", index);
}
function _NRmAccordPt(accordion, index)
{
	_N(accordion).AccordionParts.splice(index, 1);
}
function _NSetAccordPt(accordionPart, topPart, bottomPart)
{
	var tmpAccordionPart = _N(accordionPart);
	tmpAccordionPart.TopPart = topPart;
	tmpAccordionPart.BottomPart = bottomPart;
}
function _NScrollCheck(bodyId)
{
	var body = _N(bodyId);
	if(body.scrollTop == (body.scrollHeight - body.clientHeight) && body.parentNode.DataBind != null)
		body.parentNode.DataBind.call();
}