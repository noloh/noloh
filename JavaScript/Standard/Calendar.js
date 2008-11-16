function _NCalLMt(id)
{
	var cal = _N(id);
	cal.ViewDate.setMonth(_NSetProperty(id,"ViewMonth",cal.ViewDate.getMonth()-1));
	cal.ViewDate.setFullYear(_NSetProperty(id,"ViewYear",cal.ViewDate.getFullYear()));
	_NCalPrint(id);
}
function _NCalNMt(id)
{
	var cal = _N(id);
	cal.ViewDate.setMonth(_NSetProperty(id,"ViewMonth",cal.ViewDate.getMonth()+1));
	cal.ViewDate.setFullYear(_NSetProperty(id,"ViewYear",cal.ViewDate.getFullYear()));
	_NCalPrint(id);
}
function _NCalLYr(id)
{
	var cal = _N(id);
	cal.ViewDate.setFullYear(_NSetProperty(id,"ViewYear",cal.ViewDate.getFullYear()-1));
	_NCalPrint(id);
}
function _NCalNYr(id)
{
	var cal = _N(id);
	cal.ViewDate.setFullYear(_NSetProperty(id,"ViewYear",cal.ViewDate.getFullYear()+1));
	_NCalPrint(id);
}
function _NCalSlctDt(event, calid)
{
	var cal = _N(calid);
	var lab = event.target;
	_N(cal.SelectedLabelId).style.fontWeight = "normal";
	cal.SelectedLabelId = lab.id;
	cal.SelectDate.setDate(_NSetProperty(calid,"Date",lab.innerHTML));
	cal.SelectDate.setMonth(_NSetProperty(calid,"Month",cal.ViewDate.getMonth()));
	cal.SelectDate.setFullYear(_NSetProperty(calid,"Year",cal.ViewDate.getFullYear()));
	lab.style.fontWeight = "bold";
	if(cal.onchange)
		cal.onchange();
}
function _NCalShow(id, viewMonth, viewYear, selectDate, selectMonth, selectYear)
{
	var cal = _N(id);
	cal.SelectDate = new Date();
	cal.ViewDate = new Date();
	cal.ViewDate.setFullYear(viewYear, viewMonth, 1);
	cal.SelectDate.setFullYear(selectYear, selectMonth, selectDate);
	_NSaveControl(id);
	_NCalPrint(id);
}
function _NCalPrint(id)
{
	var ubound, date, i, obj, cal = _N(id);
	var month = cal.ViewDate.getMonth();
	var year = cal.ViewDate.getFullYear();
	id = parseInt(id.replace("N", ""));
	var offset = id + 13;
	cal.ViewDate.setDate(1);
	_N("N" + (id+1)).innerHTML = _NCalShMt(cal.ViewDate) + " " + cal.ViewDate.getFullYear();
	ubound = cal.ViewDate.getDay()+offset;
	for(i = offset; i < ubound; i++)
	{
		obj = _N("N" + i);
		obj.innerHTML = "";
	}
	ubound = offset+42;
	for(i = cal.ViewDate.getDay() + offset; i < ubound; ++i)
	{
		obj = _N("N" + i);
		if(month == cal.ViewDate.getMonth())
		{
			obj.innerHTML = cal.ViewDate.getDate();
			if(cal.ViewDate.getDate()==cal.SelectDate.getDate() && cal.ViewDate.getMonth()==cal.SelectDate.getMonth()
			 														  && cal.ViewDate.getFullYear()==cal.SelectDate.getFullYear())
			{
				obj.style.fontWeight = "bold";
				cal.SelectedLabelId = obj.id;
			}
			else
				obj.style.fontWeight = "normal";
		}
		else
			obj.innerHTML = "";
		cal.ViewDate.setDate(cal.ViewDate.getDate()+1);
	}
	cal.ViewDate.setFullYear(year, month);
}
function _NCalFlDy(dtObj)
{
	var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
	return weekday[dtObj.getDay()];
}
function _NCalShDy(dtObj)
{
	var day = _NCalFlDy(dtObj);
	if(day == "Thursday")
		return day.substring(0, 4);
	return day.substring(0, 3);
}
function _NCalFlMt(dtObj)
{
	var fullmonth = ["January","February","March","April","May","June",
		"July","August","September","October","November","December"];
	return fullmonth[dtObj.getMonth()];
}
function _NCalMtZer(dtObj)
{
	var month = dtObj.getMonth();
	if(month <= 9)
		return "0"+(month+1).toString();
	return month;
}
function _NCalDtZer(dtObj)
{
	if(dtObj.getDate() <= 9)
		return "0" + dtObj.getDate().toString();
	return dtObj.getDate();
}
function _NCalShMt(dtObj)
{
	var month = _NCalFlMt(dtObj);
	if(month == "September")
		return month.substring(0, 4);
	return month.substring(0, 3);
}
function _NCalSuff(dtObj)
{
	var date = dtObj.getDate();
	if(date == 1 || date == 21 || date == 31)
		return date.toString() + "st";
	else
	if(date == 2 || date == 22)
		return date.toString() + "nd";
	else
	if(date == 3 || date == 23)
		return date.toString() + "rd";
	return date.toString() + "th";
}
function _NCalYr(dtObj)
{
	return dtObj.getFullYear().toString().substring(2,4);
}
function _NCalDtLtr(letter, dtObj)
{
	switch(letter)
	{
		case "d":	return _NCalDtZer(dtObj);
		case "D":	return _NCalShDy(dtObj);
		case "F":	return _NCalFlMt(dtObj);
		case "j":	return dtObj.getDate();
		case "l":	return _NCalFlDy(dtObj);
		case "m":	return _NCalMtZer(dtObj);
		case "M":	return _NCalShMt(dtObj);
		case "n":	return dtObj.getMonth() + 1;
		case "w":	return dtObj.getDay() + 1;	
		case "y":	return _NCalYr(dtObj);
		case "Y":	return dtObj.getFullYear();
		case "S":	return _NCalSuff(dtObj);
	}
	return letter;
}
function _NCalDtStr(calid, dateStr)
{
	var d = _N(calid).SelectDate;
	var finalStr = "";
	for(var i = 0; i < dateStr.length; i++)
		finalStr += _NCalDtLtr(dateStr.substring(i, i+1), d);
	return finalStr;
}