function _NStyleRem(remPath, nPath)
{
	_N('NHead').removeChild(_N(remPath));
	var blankStyle = document.createElement("LINK");
	blankStyle.rel = "stylesheet";
	blankStyle.type = "text/css";
	blankStyle.href = nPath+"Styles/Blank.css";
	_N('NHead').appendChild(blankStyle);
	_N('NHead').removeChild(blankStyle);
}