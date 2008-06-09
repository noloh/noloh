/*Easing Equations by Robert Penner, Copyright © 2001 Robert Penner. All rights reserved. Modified for NOLOH*/

_NAnims = Array();
_NAnimsCount = 0;
_NAnimTimer = null;

function _NAniStart(id, prpty, from, to, duration, units, easing, fps)
{
	_NAnims.push(new _NAnimation(id, prpty, from, to, duration, units, easing, fps));
}
function _NAnimation(id, prpty, from, to, duration, units, easing, fps)
{
	++_NAnimsCount;
	this.Index = _NAnims.length;
	this.StartTime = new Date().getTime();
	this.From = from;
	this.Destination = to;
	this.Difference = to - from;
	this.Obj = _N(id);
	this.ObjId = id;
	this.Duration = duration;
//	this.Change =  _NAniLinearCumulativeChange;
//	this.Change =  _NAniQuadraticCumulativeChange;
//	this.Change =  _NAniCubicCumulativeChange;
	this.Change = easing==1?_NAniLinear : easing==2?_NAniQuadratic : _NAniCubic;
	this.Units = units;
	this.Property = prpty;
	if(this.Obj.ShiftsWith != null)
	{
		this.ShiftType = prpty=="style.width"?1: prpty=="style.height"?2: prpty=="style.left"?4: 5;
		SetShiftWithInitials(this.Obj);
	}
	this.Step = _NRunStep;
	this.Stop = _NAniStop;
	//this.Timer = setInterval('_NAnims['+this.Index+'].Step();', Math.round(1000/fps));
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
	//clearInterval(this.Timer);
	if(--_NAnimsCount == 0)
	{
		_NAnims = Array();
		clearInterval(_NAnimTimer);
		_NAnimTimer = null;
	}
	else
		_NAnims[this.Index] = null;
	//if(this.Property == 'style.opacity')
	//	_NSetProperty(this.ObjId, 'style.opacity', this.Destination/100);
		//_NSetProperty(this.ObjId, 'style.opacity', this.From + delta);
	//else
	//{
		_NSetProperty(this.ObjId, this.Property, this.Destination + this.Units);
		if(this.Obj.ShiftsWith != null)
			if(this.Property == 'style.left' || this.Property == 'style.width')
				ShiftObjects(this.Obj.ShiftsWith, this.Difference, null, this.ShiftType);
			else
				ShiftObjects(this.Obj.ShiftsWith, null, this.Difference, this.ShiftType);
	//}
	//}
}
function _NRunStep()
{
	var timePassed = new Date().getTime() - this.StartTime, delta;
	if(timePassed < this.Duration)
	{
		delta = this.Change(timePassed, this.Difference, this.Duration);
		//alert(timePassed + " - " + this.Difference + " - " + this.Duration);
		//alert(delta);
		if(this.Property == 'opacity')
			_NSetProperty(this.ObjId, 'style.opacity', (this.From + delta)/100);
			//_NSetProperty(this.ObjId, 'style.opacity', this.From + delta);
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