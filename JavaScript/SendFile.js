function _NRequestFile(iSrc)
{
	var iframe = document.createElement("IFRAME");
	iframe.id = iSrc;
	iframe.src = iSrc;
	iframe.style.display = "none";
	document.body.appendChild(iframe);
	window.setTimeout('document.body.removeChild(_N("' + iSrc + '"))', 5000);
}