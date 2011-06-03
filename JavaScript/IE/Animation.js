/*!Easing Equations by Robert Penner, Copyright © 2001 Robert Penner. All rights reserved. Modified for NOLOH*/

function _NAni(id, prpty, to, duration, units, easing, from, fps)
{
	var display=false, triggerStart=true, count= _NAni.Active.length;
	for(var i=0; i<count; ++i)
		if(_NAni.Active[i] && _NAni.Active[i].ObjId == id)
		{
			triggerStart = false;
			if(_NAni.Active[i].Property == prpty)
			{
				_NAni.Active[i].CleanUp();
				break;
			}
		}
	this.ObjId = id;
	this.Obj = id == "N1" ? document.documentElement : _N(id);
	if(isNaN(to))
	{
		this.Destination = prpty=="style.left"?-this.Obj.offsetWidth: prpty=="style.top"?-this.Obj.offsetHeight: 0;
		if(to == "Oblivion")
			this.Oblivion = true;
		else if(to == "Hiding")
			this.Hiding = true;
	}
	else
		this.Destination = to;
	this.Property = prpty;
	if(from == "Hiding")
	{
		from = null;
		display = true;
	}
	if(this.Obj._NHiding)
	{
		this.Obj._NHiding = null;
		display = true;
	}
	if(display)
	{
		_NSet(this.ObjId, 'style.display', '');
		_NSet(this.ObjId, 'style.visibility', 'inherit');
	}
	this.From = from == null ? (prpty == "opacity" ? (this.Obj.style.filter?parseInt(this.Obj.style.filter.substring(13)):100) : parseInt(eval("this.Obj."+prpty+";"))) : from;
	if(isNaN(this.From))
		this.From = prpty=="style.width"?this.Obj.offsetWidth: prpty=="style.height"?this.Obj.offsetHeight: prpty=="style.left"?this.Obj.offsetLeft: prpty=="style.top"?this.Obj.offsetTop: 0;
	this.Difference = this.Destination - this.From;
	this.Index = _NAni.Active.length;
	this.Duration = isNaN(duration) ? 1000 : duration;
	this.Change = easing ? (easing==1?_NAniLinear : easing==2?_NAniQuadratic : _NAniCubic) : _NAniQuadratic;
	this.Units = (units==null&&units!="") ? "px" : units;
	this.LastDelta = 0;
	if(this.Obj.ShiftsWith)
		this.ShiftType = prpty=="style.width"?1: prpty=="style.height"?2: prpty=="style.left"?4: 5;
	if(this.Obj.AnimationStart && triggerStart)
		this.Obj.AnimationStart();
	++_NAni.ActiveCount;
	_NAni.Active.push(this);
	this.StartTime = new Date().getTime();
	if(!this.Duration)
		this.Step();
	else if(!_NAni.Timer)
		_NAni.Timer = setInterval(_NAniStepAll, Math.round(1000/ (fps?fps:30)));
}
_NAni.Active = [];
_NAni.ActiveCount = 0;
_NAni.prototype.Step = function()
{
	var timePassed = new Date().getTime() - this.StartTime, delta;
	if(timePassed < this.Duration)
		this.Move(this.Change(timePassed, this.Difference, this.Duration));
	else
	{
		this.Move(this.Difference);
		this.FinishingTouches();
		this.CleanUp();
	}
};
_NAni.prototype.Move = function(delta)
{
	if(this.ObjId == "N1")
		document.documentElement[this.Property] = this.From + delta + this.Units;
	else if(this.Property == 'opacity')
		_NSet(this.ObjId, 'style.filter', 'alpha(opacity='+(this.From + delta)+')');
	else if(this.Property == 'scrollLeft' || this.Property == 'scrollTop')
		_NChangeByObj(this.Obj, this.Property, this.From + delta);
	else
	{
		_NSet(this.ObjId, this.Property, this.From + delta + this.Units);
		if(this.Obj.ShiftsWith && this.Obj.ShiftsWith[this.ShiftType])
			if(this.ShiftType == 1 || this.ShiftType == 4)
				_NShftObjs(this.Obj.ShiftsWith[this.ShiftType], delta - this.LastDelta, 0);
			else
				_NShftObjs(this.Obj.ShiftsWith[this.ShiftType], 0, delta - this.LastDelta);
		this.LastDelta = delta;
	}
	if(this.Obj.AnimationStep)
		this.Obj.AnimationStep.call(this.Obj);
};
_NAni.prototype.FinishingTouches = function()
{
	/*if(this.ObjId == "N1")
		_NBodyScrollState();
	else */
	if(this.Property == "opacity" && this.Destination == 100)
		this.Obj.style.filter = '';
	else if(this.Oblivion)
	{
		_NRem(this.ObjId);
		_NSet(this.ObjId, '_NOblivion', 1);
	}
	else if(this.Hiding)
	{
		this.Obj._NHiding = true;
		_NSet(this.ObjId, 'style.display', 'none');
	}
	if(this.Obj.AnimationStop)
	{
		var count= _NAni.Active.length;
		for(var i=0; i<count; ++i)
			if(_NAni.Active[i] && _NAni.Active[i].ObjId == this.ObjId && i != this.Index)
				return;
		this.Obj.AnimationStop();
	}
};
_NAni.prototype.CleanUp = function()
{
	if(--_NAni.ActiveCount == 0)
	{
		_NAni.Active = [];
		clearInterval(_NAni.Timer);
		_NAni.Timer = null;
	}
	else
		_NAni.Active[this.Index] = null;
};
function _NAniStepAll()
{
	var count= _NAni.Active.length;
	for(var i=0; i<count; ++i)
		if(_NAni.Active[i])
			_NAni.Active[i].Step();
}
function _NAniLinear(time, difference, duration)
{
	return Math.round(difference * time/duration);
}
function _NAniQuadratic(time, difference, duration)
{
	if((time /= duration/2) < 1)
		return Math.round(difference/2 * time * time);
	return Math.round(-difference/2 * (--time * (time-2) -1));
}
function _NAniCubic(time, difference, duration)
{
	if((time /= duration/2) < 1)
		return Math.round(difference/2 * time * time * time);
	return Math.round(difference/2 * ((time-=2) * time * time + 2));
}