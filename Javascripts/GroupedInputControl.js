function _NGIClick(id)
{
    document.getElementById(id).click();
}
function _NRBSave(id)
{
    var divId;
	var radio = document.getElementById(id+"I");
    var val = radio.checked;
	var radioGroup = document.getElementsByName(radio.name);
	for(var i=0; i < radioGroup.length; i++)
	{
        divId = radioGroup[i].id.replace("I", "");
		NOLOHChangeInit(divId, "checked");
        _NSave(divId, "checked", divId == id);
		if(radioGroup[i].checked != (NOLOHChanges[divId]["checked"][0] != null ? NOLOHChanges[divId]["checked"][0] : SavedControls[divId].checked) && document.getElementById(divId).onchange!=null)
			document.getElementById(divId).onchange.call();
	}
}
function _NCBSave(id)
{
    var checkbox = document.getElementById(id+"I");
    _NSave(id, "checked", checkbox.checked);
}