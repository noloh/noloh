function _NCBCptClk(id)
{
	_N(id+"I").click();
}
function _NCBSave(id)
{
	_NSet(id, "Selected", _N(id+"I").checked);
}