function SetMarkupString(whatDistinctId, whatText)
{
	var tmpMarkupString = whatText;
	tmpMarkupString = tmpMarkupString.replace(/<NQt2>/g,"\"");
	tmpMarkupString = tmpMarkupString.replace(/<NQt1>/g,"\'");
	tmpMarkupString = tmpMarkupString.replace(/<Nendl>/g,"\n");
	document.getElementById(whatDistinctId).innerHTML = tmpMarkupString; 
}