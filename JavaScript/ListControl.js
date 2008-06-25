function _NListSel(id,idx)
{
	_N(id).options[idx].selected=true;
}
function _NListDesel(id,idx)
{
	_N(id).options[idx].selected=false;
}
function _NListClrSel(id)
{
	var opts = _N(id).options;
	var length = opts.length;
	for(var i=0; i<length; ++i)
		opts[i].selected=false;
}
function _NListAdd(id,text,val,idx)
{
	var opts = _N(id).options;
	if(typeof idx == "undefined")
		opts[opts.length] = new Option(text,val);
	else
	{
		var i=opts.length;
		while(i>idx)
			opts[i] = new Option(opts[--i].text, opts[i].value);
		opts[i] = new Option(text,val);
	}
	return [];
}
function _NListRem(id,idx)
{
	_N(id).remove(idx);
}
function _NListClr(id)
{
	_N(id).options.length=0;
}