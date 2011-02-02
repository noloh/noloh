function _NOpenPrintable(id, print)
{
	try
	{
		var newWin = window.open('', 'PrintableVersion', 'toolbar=no,status=no,menubar=yes,scrollbars=yes,directories=no'), 
			code = '<HTML><HEAD><TITLE>Print - ' + document.title + '</TITLE>', links = _N('NHead').getElementsByTagName('LINK'), length=links.length, i;
		for(i=0; i<length; ++i)
			code += '<LINK rel="stylesheet" type="text/css" href="' + links[i].href + '">';
		code += '</HEAD><BODY>' + _N(id).innerHTML + '</BODY></HTML>';
		newWin.document.write(code);
		newWin.document.close();
		if(print)
			newWin.print();
	}
	catch(e) {}
}