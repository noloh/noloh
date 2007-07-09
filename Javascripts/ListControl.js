function _NListSel(id,idx)
{
	document.getElementById(id).options[idx].selected=true;
}
function _NListDesel(id,idx)
{
	document.getElementById(id).options[idx].selected=false;
}
function _NListAdd(id,text,val,idx)
{
	var opts = document.getElementById(id).options;
	if(typeof idx == "undefined")
		opts[opts.length] = new Option(text,val);
	else
	{
		var i=opts.length;
		while(i>idx)
			opts[i] = new Option(opts[--i].text, opts[i].value);
		opts[i] = new Option(text,val);
	}
	return Array();
}
function _NListRem(id,idx)
{
	document.getElementById(id).remove(idx);
}
function _NListClr(id)
{
	document.getElementById(id).options.length=0;
}