function _NXHR(method, url, readystatechange, async)
{
	var xhr = new XMLHttpRequest();
	xhr.open(method, url, async);
	if(readystatechange)
    	xhr.onreadystatechange = readystatechange;
	xhr.setRequestHeader("Accept", "application/javascript");
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.setRequestHeader("Remote-Scripting", "NOLOH");
	return xhr;
}