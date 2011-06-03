function _NCBCptClk(id)
{
	var obj=_N(id);
	var iObj=_N(id+"I");
	var checked = iObj.checked;
	iObj.click();
	if(obj.onchange && iObj.checked!=checked)
		obj.onchange();
}
function _NCBSave(id)
{
	_NSet(id, "Selected", _N(id+"I").checked);
}