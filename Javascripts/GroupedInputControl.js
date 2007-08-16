function CaptionClick(id)
{
	var obj = document.getElementById(id);
	var val = obj.checked;
	obj.click();
	if(val!=obj.checked && obj.onchange!=null) 
		obj.onchange.call();
}
function RadioButtonSave(id)
{
	var radio = document.getElementById(id);
	var radioGroup = document.getElementsByName(radio.name);
	for(var i=0; i < radioGroup.length; i++)
	{
		NOLOHChangeInit(radioGroup[i].id,"checked");
		//alert(radioGroup[i].checked + " vs " + SavedControls[radioGroup[i].id].checked);
		if(radioGroup[i].checked != (NOLOHChanges[radioGroup[i].id]["checked"][0] != null ? NOLOHChanges[radioGroup[i].id]["checked"][0] : SavedControls[radioGroup[i].id].checked) && radioGroup[i].onchange!=null)
			radioGroup[i].onchange.call();
		_NSave(radioGroup[i].id, "checked", radioGroup[i].id == id);
	}
	_NSave(id, "checked");
}