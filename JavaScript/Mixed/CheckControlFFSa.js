function _NCCClick(id)
{
	_N(id).click();
}
function _NRBSave(id)
{
	var i, divId, changeArr = [];
	var radio = _N(id+"I");
	var radioGroup = document.getElementsByName(radio.name);
	for(i=0; i < radioGroup.length; ++i)
	{
		divId = radioGroup[i].id.replace("I", '');
		NOLOHChangeInit(divId, "checked");
		if(radioGroup[i].checked != (NOLOHChanges[divId]["checked"][0] != null ? NOLOHChanges[divId]["checked"][0] : SavedControls[divId].checked) && _N(divId).onchange!=null)
			changeArr.push(divId);
		_NSave(divId, "checked", divId == id);
	}
	for(i=0; i < changeArr.length; ++i)
		_N(changeArr[i]).onchange.call();
	var group = window[radio.name];
	if(group && !group.tagName && group.onchange)
		group.onchange.call();
}
function _NCBSave(id)
{
	var checkbox = _N(id+"I");
	_NSave(id, "checked", checkbox.checked);
}