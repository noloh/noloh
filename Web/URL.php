<?php

abstract class URL
{
	const Disable = 0;
	const Display = 1;
	const Encrypt = 2;
	
	final function URL() {}
	
	static function GetToken($tokenName, $defaultValue=null)
	{
		return isset($_SESSION["NOLOHTokens"][$tokenName]) && $GLOBALS["NOLOHURLTokenMode"] ? $_SESSION["NOLOHTokens"][$tokenName] : $defaultValue;
	}
	
	static function SetToken($tokenName, $tokenValue)
	{
		if($GLOBALS["NOLOHURLTokenMode"])
		{
			$_SESSION["NOLOHTokens"][$tokenName] = $tokenValue;
			$GLOBALS["NOLOHTokenUpdate"] = true;
		}
	}
	
	static function UpdateTokens()
	{
		$URLString = "";
		foreach($_SESSION["NOLOHTokens"] as $key => $val)
			$URLString .= $key . "=" . $val . "&";
		$URLString = rtrim($URLString, "&");
		if($GLOBALS["NOLOHURLTokenMode"] == 2)
			$URLString = base64_encode($URLString);
		AddScript("location=document.URL.split('#',1)[0]+'#$URLString';NURL=location.toString()", Priority::Low);
		if(GetBrowser() == "ie")
			AddScript("var d=document.getElementById('NBackButton').contentWindow.document;d.open();d.write(location.toString());d.close()", Priority::Low);
	}
}

?>