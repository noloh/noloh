/*!Easing Equations by Robert Penner, Copyright © 2001 Robert Penner. All rights reserved. Modified for NOLOH*/

_NAnims = [];
_NAnimsCount = 0;
_NAnimTimer = null;

function _NAni(id, prpty, from, to, duration, units, easing, fps)
{
	this.Obj = _N(id);
	this.ObjId = id;
	this.From = from;
	this.Destination = to;
	this.Difference = to - from;
	this.Property = prpty;
	this.Index = _NAnims.length;
	this.Duration = duration;
	this.Change = easing==1?_NAniLinear : easing==2?_NAniQuadratic : _NAniCubic;
	this.Units = units;
	if(this.Obj.ShiftsWith != null)
	{
		this.ShiftType = prpty=="style.width"?1: prpty=="style.height"?2: prpty=="style.left"?4: 5;
		SetShiftWithInitials(this.Obj);
	}
	this.Step = _NRunStep;
	this.Stop = _NAniStop;
	++_NAnimsCount;
	_NAnims.push(this);
	this.StartTime = new Date().getTime();
	if(_NAnimTimer == null)
		_NAnimTimer = setInterval(StepAllAnims, Math.round(1000/fps));
}
function StepAllAnims()
{
	var count= _NAnims.length;
	for(var i=0; i<count; ++i)
		if(_NAnims[i] != null)
			_NAnims[i].Step();
}
function _NAniStop()
{
	if(--_NAnimsCount == 0)
	{
		_NAnims = [];
		clearInterval(_NAnimTimer);
		_NAnimTimer = null;
	}
	else
		_NAnims[this.Index] = null;
	if(this.ObjId == "N1")
	{
		document.documentElement[this.Property] = this.Destination + this.Units;
		BodyScrollState();
	}
	else if(this.Property == 'opacity')
	{
		_NSetProperty(this.ObjId, 'style.filter', 'alpha(opacity='+this.Destination+')');
		if(this.Destination == 100)
			this.Obj.style.filter = '';
	}
	else if(this.Property == 'scrollLeft' || this.Property == 'scrollTop')
		NOLOHChangeByObj(this.Obj, this.Property, this.Destination);
	else
	{
		_NSetProperty(this.ObjId, this.Property, this.Destination + this.Units);
		if(this.Obj.ShiftsWith != null)
			if(this.Property == 'style.left' || this.Property == 'style.width')
				ShiftObjects(this.Obj.ShiftsWith, this.Difference, null, this.ShiftType);
			else
				ShiftObjects(this.Obj.ShiftsWith, null, this.Difference, this.ShiftType);
	}
	if(this.Destination == 1 && this.Obj._NOblivionC)
	{
		_NRem(this.ObjId);
		_NSetProperty(this.ObjId, '_NOblivionS', 1);
	}
}
function _NRunStep()
{
	var timePassed = new Date().getTime() - this.StartTime, delta;
	if(timePassed < this.Duration)
	{
		delta = this.Change(timePassed, this.Difference, this.Duration);
		if(this.ObjId == "N1")
			document.documentElement[this.Property] = this.From + delta + this.Units;
		else if(this.Property == 'opacity')
			_NSetProperty(this.ObjId, 'style.filter', 'alpha(opacity='+(this.From + delta)+')');
		else if(this.Property == 'scrollLeft' || this.Property == 'scrollTop')
			NOLOHChangeByObj(this.Obj, this.Property, this.From + delta + this.Units);
		else
		{
			_NSetProperty(this.ObjId, this.Property, this.From + delta + this.Units);
			if(this.Obj.ShiftsWith != null)
				if(this.Property == 'style.left' || this.Property == 'style.width')
					ShiftObjects(this.Obj.ShiftsWith, delta, null, this.ShiftType);
				else
					ShiftObjects(this.Obj.ShiftsWith, null, delta, this.ShiftType);
		}
	}
	else
		this.Stop();
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