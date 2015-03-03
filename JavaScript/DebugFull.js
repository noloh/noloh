function _NDebugFull(code)
{
	var f;
	try
	{
		f = new Function(code);
	}
	catch(err)
	{
		for(var len = code.length - 2; len>0; --len)
		{
			try
			{
				var fTrim = new Function(code.substring(0, len));
				if(fTrim)
				{
					alert("A JavaScript parser error has occurred starting with the following code:\n\n" + code.substring(len));
					return;
				}
			}
			catch(err2)	{}
		}
	}
	for(var length = code.length, braces = 0, start = 0, stop = 0, chr; stop < length; ++stop)
	{
		chr = code.charAt(stop);
		switch(chr)
		{
			case "'":
			case '"':
				while(code.charAt(++stop) != chr || code.charAt(stop-1) == "\\"); break;
			case "{":
				++braces; break;
			case "}":
				--braces; break;
			case "/":
				if(code.charAt(++stop) == "*")
					while(code.substring(++stop, stop+2) != "*/");
				break;
			case ";":
				if(braces == 0 && stop-start>1)
				{
					var line = code.substring(start, ++stop);
					try
					{
						eval(line);
					}
					catch(err)
					{
						err.message += "\n\nWhile processing the statement:\n" + line;
						_NAlertError(err);
						return;
					}
					start = stop;
				}
		}
	}
}