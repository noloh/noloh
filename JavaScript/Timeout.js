function _NTimeoutTicker()
{
	if(_N.TimeoutWorking == null)
		_N.TimeoutWorking = _N.TimeoutDuration;
	if(_N.TimeoutWorking == _N.TimeoutTicks)
	{
		if(_N.TimeoutAction == "Confirm")
		{
			if(confirm("The application is about to time out due to inactivity.\nPress okay to keep the application active."))
			{
				_N.TimeoutWorking = _N.TimeoutDuration;
				return "Ping";
			}
		}
		else if(_N.TimeoutAction == "Alert")
			alert("The application has timed out.\nActivity will no longer be possible until the page is refreshed.");
		_N.Request = true;
		_N.TimeoutTicks = 0;
		return "Die";
	}
	else
	{
		_N.TimeoutWorking -= _N.TimeoutTicks;
		if(_N.TimeoutWorking < _N.TimeoutTicks)
			_N.TimeoutTicks = _N.TimeoutWorking;
	}
	return "Ping";
}