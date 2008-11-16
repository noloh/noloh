function _NTimer(addTo, id, interval, repeat)
{
	this.ParentId = addTo;
	this.Id = id;
	this.Interval = interval;
	this.Repeat = repeat;
	this.Start = _NTimerStart;
	this.Stop = _NTimerStop;
	this.Destroy = _NTimerDestroy;
	var parent = _N(addTo);
	do
	{
		if(parent.TimerChildren == null)
			parent.TimerChildren = [];
		parent.TimerChildren.push(id);
		parent = parent.parentNode;
	}while (parent && parent.id);
	_N[id] = this;
	this.Start();
}
function _NTimerStart()
{
	this.Stop();
	var func = "var tmr=_N."+this.Id+";if(tmr.onelapsed) tmr.onelapsed();";
	this.Ref = this.Repeat ? window.setInterval(func, this.Interval) : window.setTimeout(func, this.Interval);
}
function _NTimerStop()
{
	if(this.Ref)
		if(this.Repeat)
			window.clearInterval(this.Ref);
		else
			window.clearTimeout(this.Ref);
}
function _NTimerDestroy()
{
	this.Stop();
	var i, parent = _N(this.ParentId);
	do
	{
		if(parent.TimerChildren != null)
			for(i=0; i<parent.TimerChildren.length; ++i)
				if(parent.TimerChildren[i] == this.Id)
					parent.TimerChildren.splice(i, 1);
		parent = parent.parentNode;
	}while (parent && parent.id);
}