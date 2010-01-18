function _NChkCond(cond, call)
{
	//console.log(cond, call);
	var _NChkIntvl = setInterval(
	function()
	{
		if (cond()) 
		{
			call();
			clearInterval(_NChkIntvl);
		}
	}, 100);
}
