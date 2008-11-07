/*!Easing Equations by Robert Penner, Copyright © 2001 Robert Penner. All rights reserved. Modified for NOLOH*/

_NAnims = [];
_NAnimsCount = 0;
_NAnimTimer = null;

function StepAllAnims()
{
	var count= _NAnims.length;
	for(var i=0; i<count; ++i)
		if(_NAnims[i])
			_NAnims[i].Step();
}
function _NAni(id, prpty, to, duration, units, easing, from, fps)
{
	var count= _NAnims.length;
	for(var i=0; i<count; ++i)
		if(_NAnims[i] && _NAnims[i].ObjId == id && _NAnims[i].Property == prpty)
		{
			_NAnims[i].CleanUp();
			break;
		}
	this.ObjId = id;
	this.Obj = id == "N1" ? document.documentElement : _N(id);
	if(isNaN(to))
	{
		this.Destination = 1;
		if(to == "Oblivion")
			this.Oblivion = true;
		else if(to == "Hiding")
			this.Hiding = true;
	}
	else
		this.Destination = to;
	this.Property = prpty;
	this.From = from == null ? (prpty == "opacity" ? (this.Obj.style.opacity?this.Obj.style.opacity*100:100) : parseInt(eval("this.Obj."+prpty+";"))) : from;
	this.Difference = this.Destination - this.From;
	this.Index = _NAnims.length;
	this.Duration = duration ? duration : 1000;
	this.Change = easing ? (easing==1?_NAniLinear : easing==2?_NAniQuadratic : _NAniCubic) : _NAniQuadratic;
	this.Units = (units==null&&units!="") ? "px" : units;
	this.LastDelta = 0;
	if(this.Obj.ShiftsWith)
		this.ShiftType = prpty=="style.width"?1: prpty=="style.height"?2: prpty=="style.left"?4: 5;
	if(this.Obj._NHiding)
	{
		this.Obj._NHiding = false;
		_NSetProperty(this.ObjId, 'style.display', '');
	}
	if(this.Obj.AnimationStart)
		this.Obj.AnimationStart();
	++_NAnimsCount;
	_NAnims.push(this);
	this.StartTime = new Date().getTime();
	if(!_NAnimTimer)
		_NAnimTimer = setInterval(StepAllAnims, Math.round(1000/ (fps?fps:30)));
}
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
}
_NAni.prototype.Move = function(delta)
{
	if(this.ObjId == "N1")
		document.documentElement[this.Property] = this.From + delta + this.Units;
	else if(this.Property == 'opacity')
		_NSetProperty(this.ObjId, 'style.opacity', (this.From + delta)/100);
	else
	{
		_NSetProperty(this.ObjId, this.Property, this.From + delta + this.Units);
		if(this.Obj.ShiftsWith && this.Obj.ShiftsWith[this.ShiftType])
			if(this.ShiftType == 1 || this.ShiftType == 4)
				ShiftObjects(this.Obj.ShiftsWith[this.ShiftType], delta - this.LastDelta, 0);
			else
				ShiftObjects(this.Obj.ShiftsWith[this.ShiftType], 0, delta - this.LastDelta);
		this.LastDelta = delta;
	}
}
_NAni.prototype.FinishingTouches = function()
{
	if(this.ObjId == "N1")
		BodyScrollState();
	else if(this.Property == "opacity" && this.Destination == 100)
		this.Obj.style.opacity = '';
	else if(this.Destination == 1)
		if(this.Oblivion)
		{
			_NRem(this.ObjId);
			_NSetProperty(this.ObjId, '_NOblivion', 1);
		}
		else if(this.Hiding)
		{
			this.Obj._NHiding = true;
			_NSetProperty(this.ObjId, 'style.display', 'none');
		}
}
_NAni.prototype.CleanUp = function()
{
	if(--_NAnimsCount == 0)
	{
		_NAnims = [];
		clearInterval(_NAnimTimer);
		_NAnimTimer = null;
	}
	else
		_NAnims[this.Index] = null;
	if(this.Obj.AnimationStop)
		this.Obj.AnimationStop();
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