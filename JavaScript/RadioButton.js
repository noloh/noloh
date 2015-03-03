function _NRBSave(id)
{
	if(_N(id).Selected != true)
	{
		var i, divId, div, changeArr = [];
		var radio = _N(id+"I");
		var radioGroup = document.getElementsByName(radio.name);
		for(i=0; i < radioGroup.length; ++i)
			if(radioGroup[i].tagName == "INPUT")
			{
				divId = radioGroup[i].id.replace("I", '');
				_NSave(divId, "Selected", divId==id);
			}
		for(i=0; i < radioGroup.length; ++i)
			if(radioGroup[i].tagName == "INPUT")
			{
				div = _N(divId = radioGroup[i].id.replace("I", ''));
				_NChange(divId, "Selected", divId==id);
				if(div.onchange)
					div.onchange();
			}
	}
}