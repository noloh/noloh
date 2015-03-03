function _NMkupSet(id, text)
{
	_N(id).innerHTML = text.replace(/<NQt2>/g,"\"").replace(/<NQt1>/g,"\'").replace(/<Nendl>/g,"\n");
}