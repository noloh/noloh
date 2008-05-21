<?php
/**
 * @package Statics
 */

/**
 * The URL class contains constants and static functions pertaining to tokens in the context of Bookmarks.
 * 
 * For more information, please see
 * @link /Tutorials/BookmarkFriendly.html
 */
final class URL
{
	/**
	 * When passed into the third parameter, $tokenMode, of SetStartupPage, URL::Disable disables the use of tokens altogether.
	 * SetToken will have no effect, and GetToken will always return the default value. 
	 * <code>SetStartupPage('MyWebPage', 'http://www.mysite.com/ErrorPage.html', URL::Disable);</code>
	 */
	const Disable = 0;
	/**
	 * When passed into the third parameter, $tokenMode, of SetStartupPage, URL::Disable will display the name and value in the 
	 * user's address bar of any tokens that are set. Hence:
	 * <code>
	 * SetStartupPage('MyWebPage', 'http://www.mysite.com/ErrorPage.html', URL::Display);
	 * // Assume the two lines are separated out in accordance with good object-oriented programming practices
	 * URL::SetToken('productid', 17);
	 * </code>
	 * Will have the effect of writing #/productid=17 in the user's URL.
	 */
	const Display = 1;
	/**
	 * When passed into the third parameter, $tokenMode, of SetStartupPage, URL::Encrypt will display an encrypted string in the
	 * user's address bar of any tokens that are set. Hence:
	 * <code>
	 * SetStartupPage('MyWebPage', 'http://www.mysite.com/ErrorPage.html', URL::Encrypt);
	 * // Assume the two lines are separated out in accordance with good object-oriented programming practices
	 * URL::SetToken('productid', 17);
	 * </code>
	 * Will have the effect of writing random-looking characters in the user's URL.
	 */
	const Encrypt = 2;
	
	/**
	 * @ignore
	 */
	private function URL() {}
	/**
	 * Gets the value of a particular URL token. If that token has not been set, then $defaultValue will be returned
	 * @param string $tokenName
	 * @param mixed $defaultValue
	 * @return string
	 */
	static function GetToken($tokenName, $defaultValue=null)
	{
		return isset($_SESSION['_NTokens'][$tokenName]) && $GLOBALS['_NURLTokenMode'] ? $_SESSION['_NTokens'][$tokenName] : $defaultValue;
	}
	/**
	 * Sets the value of a particular URL token
	 * @param string $tokenName
	 * @param string $tokenValue
	 */
	static function SetToken($tokenName, $tokenValue)
	{
		if($GLOBALS['_NURLTokenMode'] && (!isset($_SESSION['_NTokens'][$tokenName]) || $_SESSION['_NTokens'][$tokenName]!=$tokenValue))
		{
			if(!isset($GLOBALS['_NTokenUpdate']))
			{
				$GLOBALS['_NTokenUpdate'] = true;
				if($GLOBALS['_NTokenTrailsExpiration'])
					$GLOBALS['_NInitialURLTokens'] = self::TokenString($_SESSION['_NTokens']);
			}
			$_SESSION['_NTokens'][$tokenName] = $tokenValue;
		}
	}
	/**
	 * @ignore
	 */
	static function TokenString($keyValuePairs)
	{
		$str = '';
		foreach($keyValuePairs as $key => $val)
			$str .= $key . '=' . $val . '&';
		$str = rtrim($str, '&');
		return $GLOBALS['_NURLTokenMode'] == 2 ? base64_encode($str) : $str;
	}
	/**
	 * @ignore
	 */
	static function UpdateTokens()
	{
		$tokenString = self::TokenString($_SESSION['_NTokens']);
		AddScript("_NSetURL('$tokenString')", Priority::Low);
		if($GLOBALS['_NTokenTrailsExpiration'])
		{
			$initialURLString = $GLOBALS['_NInitialURLTokens'];
			$file = getcwd()."/NOLOHSearchTrails.dat";
			if(file_exists($file) && time()-filemtime($file)<$GLOBALS['_NTokenTrailsExpiration'])
			{
				$trails = unserialize(base64_decode(file_get_contents($file)));
				if($trails === false)
					$trails = array();
				if(!isset($trails[$initialURLString]))
					$trails[$initialURLString] = array();
			}
			else 
			{
				$trails = array();
				$trails[$initialURLString] = array();
			}
			$trails[$initialURLString][$tokenString] = true;
			if(is_writable($file))
				file_put_contents($file, base64_encode(serialize($trails)));
		}
	}
	
	static function Redirect($url)
	{
		AddScript('location="'.$url.'";');
	}
	
	static function OpenInNewWindow($url, $newBrowserNotPanel = true)
	{
		if($newBrowserNotPanel)
			AddScript('window.open("' . $url . '");');
		else 
		{
			$wp = new WindowPanel($url, 0, 0, 500, 300);
			$wp->Controls->Add($iframe = new IFrame($url, 0, 0, 490, 250));
			$wp->Controls->Add(/*$timer = */new Timer(5000, true));
			//$iframe->SetEvent(new ClientEvent('alert("hey!");'), 'onreadystatechange;');
			AddScript('_N("'.$iframe->Id.'").src = "'.$url.'";', Priority::Low);
			$iframe->Shifts[] = Shift::With($wp->ResizeImage, Shift::Size);
			GetComponentById('N1')->Controls->Add($wp);
		}
	}
}

?>