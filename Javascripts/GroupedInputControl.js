function _NGIClick(id)
{
    document.getElementById(id).click();
}
function _NRBSave(id)
{
    var i, divId, changeArr = Array();
	var radio = document.getElementById(id+"I");
	var radioGroup = document.getElementsByName(radio.name);
	for(i=0; i < radioGroup.length; ++i)
	{
        divId = radioGroup[i].id.replace("I", '');
		NOLOHChangeInit(divId, "checked");
        if(radioGroup[i].checked != (NOLOHChanges[divId]["checked"][0] != null ? NOLOHChanges[divId]["checked"][0] : SavedControls[divId].checked) && document.getElementById(divId).onchange!=null);
            changeArr.push(divId);
        _NSave(divId, "checked", divId == id);
	}
    for(i=0; i < changeArr.length; ++i)
        document.getElementById(changeArr[i]).onchange.call();
}
function _NCBSave(id)
{
    var checkbox = document.getElementById(id+"I");
    _NSave(id, "checked", checkbox.checked);
}