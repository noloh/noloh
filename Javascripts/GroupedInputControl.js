function CaptionClick(id)
{
	var obj = document.getElementById(id);
	var val = obj.checked;
	obj.click();
	if(val!=obj.checked && obj.onchange!=null) 
		obj.onchange.call();
}