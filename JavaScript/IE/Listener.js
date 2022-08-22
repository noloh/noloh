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
	var ref = this, iframe, form, url = location.href, ids = "", id, str, i, count, listener, interval;
	url = (url.indexOf("#!/")==-1 ? url.replace(location.hash,"") : url.replace("#!/",url.indexOf("?")==-1?"?":"&"));
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
		if(this.Transport == "Stream")
		{
			this.TransferDoc = new ActiveXObject("htmlfile");
			this.TransferDoc.open();
			this.TransferDoc.write("<html></html>");
			this.TransferDoc.close();
			
			var iframe = this.Request = this.TransferDoc.createElement("IFRAME");
			iframe.style.visibility = "hidden";
			iframe.style.position = "absolute";
			iframe.style.width = iframe.style.height = iframe.style.border = "0px";
			iframe.name = this.Id + "IF";
			iframe.src = "javascript:false;";
			
			this.TransferDoc.body.appendChild(iframe);

			iframe.contentWindow.document.open();
			iframe.contentWindow.document.close();  
			iframe.contentWindow.document.body.innerHTML = "<FORM id='frm' method='POST' target=_self action='" + url + 
				"'><INPUT type='text' name='_NVisit' value='"+ ++_N.Visit +
				"'><INPUT type='text' name='_NListener' value='"+ ids +
				"'></FORM>";
			iframe.contentWindow.document.getElementById("frm").submit();
			this.Timer = setInterval(function() {_NListenerStream.call(ref);}, interval);
		}
		else
		{
			this.Request = _NXHR("POST", url, function() {_NListenerReqStateChange.call(ref);}, true);
			this.Request.send("_NVisit="+ ++_N.Visit+"&_NListener="+ids);
		}
	}
};
_NListenerBundle.prototype.Stop = function()
{
	if(this.Request)
	{
		this.AbortSwitch = true;
		if(this.Transport == "Stream")
		{
			this.Request.src = "javascript:false;";
			this.TransferDoc.body.removeChild(this.Request);
			this.TransferDoc = null; 
		}
		else
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
		_NProcessResponse(this.Request.responseText);
		this.Request = null;
		if(this.Transport != "Stream" && !this.AbortSwitch)
			if(this.Transport == "LongPoll")
				this.Start();
			else if(this.Transport == "Poll")
			{
				var ref = this;
				setTimeout(function() {ref.Start();}, this.Interval);
			}
	}
}
function _NListenerStream()
{
	var iframe = this.Request;
	if(iframe.contentWindow)
	{
		var body = iframe.contentWindow.document.body;
		if(body)
		{
			var text = body.innerText.replace(/\\n/g, "\n").replace(/\\\\/g, "\\"), pos = text.lastIndexOf("/*_N2*/");
			if(pos > this.Offset)
			{
				_NProcessResponse(text.substring(this.Offset, pos));
				this.Offset = pos;
			}
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
