function PlusMinusSwitchClick(ObjId)
{
	var Obj = _N(ObjId);
	if(Obj.src.indexOf("plus.gif") != -1)
		_NSetProperty(Obj.id, "src", Obj.src.replace("plus.gif", "minus.gif"));
		//Obj.src = Obj.src.replace("plus.gif", "minus.gif");
	else if(Obj.src.indexOf("minus.gif") != -1)
		_NSetProperty(Obj.id, "src", Obj.src.replace("minus.gif", "plus.gif"));
		//Obj.src = Obj.src.replace("minus.gif", "plus.gif");
		
	if(Obj.onchange != null)
		Obj.onchange.call();
}