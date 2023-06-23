function _NListener(addTo, id, transport, interval)
{
	this.ParentId = addTo;
	this.Id = id;
	this.Transport = transport;
	this.Interval = interval;
	this.Enabled = true;
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
	var bnd;
	if(_N.Listeners == null)
		_N.Listeners = {};
	if(_N.Listeners[transport] == null)
		bnd = _N.Listeners[transport] = new _NListenerBundle(id, transport);
	else
	{
		bnd = _N.Listeners[transport];
		bnd.Stop();
		bnd.Ids.push(id);
	}
	if(!bnd.Starting)
	{
		setTimeout(function() {bnd.Start(); bnd.Starting=null;}, 500);
		bnd.Starting = true;
	}
}
_NListener.prototype.Start = function()
{
	this.Started = true;
};
_NListener.prototype.Stop = function()
{
	this.Started = false;
};
_NListener.prototype.Destroy = function()
{
	var i, parent = _N(this.ParentId), bnd = _N.Listeners[this.Transport];
	bnd.Stop();
	do
	{
		if(parent.NonControls != null)
			for(i=0; i<parent.NonControls.length; ++i)
				if(parent.NonControls[i] == this.Id)
					parent.NonControls.splice(i, 1);
		parent = parent.parentNode;
	}while (parent && parent.id);
	var list = bnd.Ids, length = list.length;
	for(i=0; i<length; ++i)
		if(list[i] == this.Id)
			list.splice(i, 1);
	this.ParentId = null;
	if(list.length == 0)
		delete _N.Listeners[this.Transport];
};
function _NListenerBundle(id, transport)
{
	this.Ids = [id];
	this.Transport = transport;
}
_NListenerBundle.prototype.Start = function()
{
	this.Stop();
	this.Offset = 0;
	var ref = this, url = location.href, ids="", id, i, count, listener, interval;
	for(i=0, count=this.Ids.length; i<count; ++i)
		if((listener = _N(id = this.Ids[i])) && listener.ParentId && listener.Started && listener.Enabled)
		{
			ids += id + ",";
			if(interval == null || interval > listener.Interval)
				interval = listener.Interval;
		}
	if(ids)
	{
		ids = ids.substring(0, ids.length-1);
		this.Request = _NXHR("POST",
			url.indexOf("#!/")==-1 ? url.replace(location.hash,"") : url.replace("#!/",url.indexOf("?")==-1?"?":"&"),
			function() {_NListenerReqStateChange.call(ref);}, true);
		this.Request.send("_NVisit="+ ++_N.Visit+"&_NApp="+_NApp+"&_NListener="+ids);
		if(this.Transport == "Stream")
			this.Timer = setInterval(function() {_NListenerStream.call(ref);}, interval);
	}
};
_NListenerBundle.prototype.Stop = function()
{
	if(this.Request)
	{
		this.AbortSwitch = true;
		this.Request.abort();
		this.AbortSwitch = null;
		if(this.Timer)
			clearInterval(this.Timer);
		this.Request = null;
	}
};
function _NListenerReqStateChange()
{
	if(this.Request.readyState == 4 && this.Request.status == 200)
	{
		if(this.Timer)
			clearInterval(this.Timer);
		var poll = this.Transport == "Poll";
		if(poll)
			_NProcessResponse(this.Request.responseText);
		else
			_NListenerStream.call(this);
		this.Request = null;
		if(this.Transport != "Stream" && !this.AbortSwitch)
			if(this.Transport == "LongPoll")
				this.Start();
			else if(poll)
			{
				var ref = this;
				setTimeout(function() {ref.Start();}, this.Interval);
			}
	}
}
function _NListenerStream()
{
	if(this.Request.readyState >= 3)
	{
		var text = this.Request.responseText, pos = text.lastIndexOf("/*_N2*/");
		if(pos > this.Offset)
		{
			_NProcessResponse(text.substring(this.Offset, pos));
			this.Offset = pos;
		}
	}
}
function _NListenersHold()
{
	var list = _N.Listeners, transport;
	for(transport in list)
		list[transport].Stop();
}
function _NListenersCont()
{
	var list = _N.Listeners, transport;
	for(transport in list)
		list[transport].Start();
}