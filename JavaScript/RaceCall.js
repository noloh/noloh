function _NChkCond(cond, call, callback)
{
	//console.log(cond, call);
	var _NChkIntvl = setInterval(
	function()
	{
		if (cond()) 
		{
			var returnVal = call();
			clearInterval(_NChkIntvl);
			if(callback)
				callback(returnVal);
		}
	}, 100);
}
