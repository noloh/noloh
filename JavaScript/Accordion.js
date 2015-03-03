function _NAccPtAdd(accordion, accordionPart)
{
	var tmpAccordion = _N(accordion);
	if(tmpAccordion.AccordionParts == null)
		tmpAccordion.AccordionParts = [];
	tmpAccordion.AccordionParts[tmpAccordion.AccordionParts.length] = accordionPart;
	tmpAccordion.TitleHeight += _N(_N(accordionPart).Top).offsetHeight;
}
function _NAccPtExpd(accordPart)
{
	var part = _N(accordPart);
	_NClpsPnlSetHgt(part.id, (parseInt(_N(part.Accord).offsetHeight) - _N(part.Accord).TitleHeight + _N(part.Top).offsetHeight));
}
function _NAccPtRm(accordion, index)
{
	_N(accordion).AccordionParts.splice(index, 1);
}