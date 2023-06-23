function _NAddExtSource(path)
{
//	console.log(path + '_here');
//	alert(path);
	_N('NHead').appendChild(document.createElement('SCRIPT')).src = path;
}