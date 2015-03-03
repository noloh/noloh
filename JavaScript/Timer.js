function _NTimer(addTo, id, interval, repeat)
{
	this.ParentId = addTo;
	this.Id = id;
	this.Interval = interval;
	this.Repeat = repeat;
	var parent = _N(addTo);
	do
	{
		if(parent.NonControls == null)
			parent.NonControls = [];
		parent.NonControls.push(id);
		parent = parent.parentNode;
	}while (parent && parent.id);
	_N[id] = this;
	this.Start();
};
_NTimer.prototype.Start = function()
{
	this.Stop();
	var func = "var tmr=_N."+this.Id+";if(tmr.onelapsed) tmr.onelapsed();";
	this.Ref = this.Repeat ? window.setInterval(func, this.Interval) : window.setTimeout(func, this.Interval);
};
_NTimer.prototype.Stop = function()
{
	if(this.Ref)
		if(this.Repeat)
			window.clearInterval(this.Ref);
		else
			window.clearTimeout(this.Ref);
};
_NTimer.prototype.Destroy = function()
{
	this.Stop();
	var i, parent = _N(this.ParentId);
	do
	{
		if(parent.NonControls != null)
			for(i=0; i<parent.NonControls.length; ++i)
				if(parent.NonControls[i] == this.Id)
					parent.NonControls.splice(i, 1);
		parent = parent.parentNode;
	}while (parent && parent.id);
};