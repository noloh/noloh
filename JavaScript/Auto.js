function _NAWH(id)
{
	var ele = _N(id), awh = _N("NAWH"), result;
	awh.className = ele.className;
	awh.style.fontSize = ele.style.fontSize;
	awh.style.width = ele.style.width;
	awh.style.height = ele.style.height;
	awh.innerHTML = ele.innerHTML;
	awh.style.border = "0px solid black";
	awh.style.margin = "0px";
	awh.style.padding = "0px";
	if(ele.style.width == "")
	{
		ele.style.width = (result = awh.offsetWidth) + "px";
		_NSave(id, "_NCaW", result);
	}
	if(ele.style.height == "")
	{
		ele.style.height = (result = awh.offsetHeight) + "px";
		_NSave(id, "_NCaH", result);
	}
}