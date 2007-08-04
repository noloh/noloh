<?php
/**
 * @package Web
 *
 */
final class URL
{
	const Disable = 0;
	const Display = 1;
	const Encrypt = 2;
	
	private function URL() {}
	
	static function GetToken($tokenName, $defaultValue=null)
	{
		return isset($_SESSION['NOLOHTokens'][$tokenName]) && $GLOBALS['NOLOHURLTokenMode'] ? $_SESSION['NOLOHTokens'][$tokenName] : $defaultValue;
	}
	
	static function SetToken($tokenName, $tokenValue)
	{
		if($GLOBALS['NOLOHURLTokenMode'] && (!isset($_SESSION['NOLOHTokens'][$tokenName]) || $_SESSION['NOLOHTokens'][$tokenName]!=$tokenValue))
		{
			if(!isset($GLOBALS['NOLOHTokenUpdate']))
			{
				$GLOBALS['NOLOHTokenUpdate'] = true;
				if($GLOBALS['NOLOHTokenTrailsExpiration'])
					$GLOBALS['NOLOHInitialURLTokens'] = self::TokenString($_SESSION['NOLOHTokens']);
			}
			$_SESSION['NOLOHTokens'][$tokenName] = $tokenValue;
		}
	}
	
	static function TokenString($keyValuePairs)
	{
		$str = '';
		foreach($keyValuePairs as $key => $val)
			$str .= $key . '=' . $val . '&';
		$str = rtrim($str, '&');
		if($GLOBALS['NOLOHURLTokenMode'] == 2)
			$str = base64_encode($str);
		return $str;
	}
	
	static function UpdateTokens()
	{
		$tokenString = self::TokenString($_SESSION['NOLOHTokens']);
		AddScript("_NSetURL('$tokenString')", Priority::Low);
		if($GLOBALS['NOLOHTokenTrailsExpiration'])
		{
			$initialURLString = $GLOBALS['NOLOHInitialURLTokens'];
			$file = getcwd()."/NOLOHSearchTrails.dat";
			if(file_exists($file) && time()-filemtime($file)<$GLOBALS['NOLOHTokenTrailsExpiration'])
			{
				$trails = unserialize(base64_decode(file_get_contents($file)));
				if($trails === false)
					$trails = array();
				if(!isset($trails[$initialURLString]))
					$trails[$initialURLString] = array();
				Alert(serialize($trails));
			}
			else 
			{
				$trails = array();
				$trails[$initialURLString] = array();
			}
			$trails[$initialURLString][$tokenString] = true;
			file_put_contents($file, base64_encode(serialize($trails)));
		}
	}
}

?>