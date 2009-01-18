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
	//console.log(part.Group);
	var prevPart = part.Group.PrevSelectedElement;
	//console.log(part.Group);
	//console.log(prevPart);
	if(prevPart && prevPart != accordPart)
		_NSetProperty(prevPart, 'Selected', false);
	part.InHgt = (parseInt(_N(part.Accord).style.height) - _N(part.Accord).TitleHeight);
	//console.log((_N(part.Top.offsetHeight) + _N(accord).TitleHeight) + ' is the height');
}
function _NAccPtRm(accordion, index)
{
	_N(accordion).AccordionParts.splice(index, 1);
}