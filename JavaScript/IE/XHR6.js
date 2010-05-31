function _NXHR(method, url, readystatechange, async)
{
	var xhr = new ActiveXObject("Microsoft.XMLHTTP");
	xhr.open(method, url, async);
	if(readystatechange)
    	xhr.onreadystatechange = readystatechange;
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.setRequestHeader("Remote-Scripting", "NOLOH");
	return xhr;
}