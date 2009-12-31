function _NLstCtrSel(id,idx)
{
	_N(id).options[idx].selected=true;
}
function _NLstCtrDesel(id,idx)
{
	_N(id).options[idx].selected=false;
}
function _NLstCtrClrSel(id)
{
	var opts = _N(id).options;
	var length = opts.length;
	for(var i=0; i<length; ++i)
		opts[i].selected=false;
}
function _NLstCtrAdd(id,text,val,idx)
{
	text = text.replace(/<NQt2>/g,"\"").replace(/<NQt1>/g,"\'");
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
function _NLstCtrRem(id,idx)
{
	_N(id).remove(idx);
}
function _NLstCtrClr(id)
{
	_N(id).options.length=0;
}
function _NLstCtrExplSelInds(options)
{
	var retString = "";
	if(options)
		for(var i=0; i < options.length; ++i)
			if(options[i].selected)
				retString += i + "~d2~";
	return retString.substring(0,retString.length-4);
}
function _NLstCtrSaveSelInd(id)
{
	_N.Saved[id].selectedIndex = _N(id).selectedIndex;
}
function _NLstCtrSaveSelInds(id)
{
	_N.Saved[id]._NSelectedIndices = _NLstCtrExplSelInds(_N(id).options);
}