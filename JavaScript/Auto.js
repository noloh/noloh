function _NAWH(id)
{
	var ele = _N(id);
	var awh = _N("NAWH");
	awh.className = ele.className;
	awh.style.fontSize = ele.style.fontSize;
	awh.style.width = ele.style.width;
	awh.style.height = ele.style.height;
	awh.innerHTML = ele.innerHTML;
	if(ele.style.width == "")
	{
		ele.style.width = awh.offsetWidth + "px";
		_NSave(id, "CachedWidth", awh.offsetWidth);
		
	}
	if(ele.style.height == "")
	{
		ele.style.height = awh.offsetHeight + "px";
		_NSave(id, "CachedHeight", awh.offsetHeight);
	}
}