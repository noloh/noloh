/*Easing Equations by Robert Penner, Copyright © 2001 Robert Penner. All rights reserved. Modified for NOLOH*/

function _NAniStart(id, prpty, from, to, queue, fps)
{
	anim = new _NAnimation(id, prpty, from, to, queue, fps);
}
function _NAnimation(id, prpty, from, to, queue, fps)
{
	this.StartTime = new Date().getTime();
	this.From = from;
	this.Destination = to;
	this.Difference = to - from;
	this.Obj = _N(id);
	this.Interval = Math.round(1000/fps);
	this.Duration = 5000;
	this.Change =  _NAniChange;
	this.Property = prpty;
	//alert(this.Change + ' is the change');
	this.Step = _NRunStep;
	/*this.Step = function() 
	{
		var curTime = new Date().getTime();
		alert(this.StartTime + ' ' + this.Duration);
		return;
		if(curTime < this.StartTime + this.Duration)
		{
			//var change = (curTime - this.StartTime) / this.Length
			_NSetProperty(this.Obj, 'style.' + this.Property, parseInt(Obj.style[this.Property]) + this.Change + 'px');
		}
		else
		{
			this.Stop();
		}
	}*/
	this.Stop = _NAniStop;
	
	this.Timer = setInterval(this.Step, this.Interval);
}
function _NAniStop()
{
	clearInterval(anim.Timer);
	_NSetProperty(anim.Obj.id, 'style.' + anim.Property, anim.Destination + 'px');
}
function _NRunStep()
{
	var curTime = new Date().getTime();

	if(curTime < anim.StartTime + anim.Duration)
	{
		//var change = (curTime - this.StartTime) / this.Length
		//alert(anim.Obj.id + ' ' + 'style.' + anim.Property + ' ' + parseInt(anim.Obj.style[anim.Property]) + anim.Change + 'px');
		_NSetProperty(anim.Obj.id, 'style.' + anim.Property, parseInt(anim.Obj.style[anim.Property]) + anim.Change(anim.Difference, anim.Duration, anim.Interval) + 'px');
	}
	else
	{
		anim.Stop();
	}
}
function _NAniChange(difference, duration, interval)
{
	return Math.round((difference)/(this.Duration / this.Interval));
}