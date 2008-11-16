<?php
/**
 * URL class
 *
 * The URL class contains constants and static functions pertaining to tokens in the context of Bookmarks.
 * 
 * For more information, please see
 * @link /Tutorials/BookmarkFriendly.html
 * 
 * @package Statics
 */
final class URL
{
	/**
	 * When passed into the third parameter, $tokenMode, of SetStartupPage, URL::Disable disables the use of tokens altogether.
	 * SetToken will have no effect, and GetToken will always return the default value. 
	 * <pre>SetStartupPage('MyWebPage', 'http://www.mysite.com/ErrorPage.html', URL::Disable);</pre>
	 */
	const Disable = 0;
	/**
	 * When passed into the third parameter, $tokenMode, of SetStartupPage, URL::Disable will display the name and value in the 
	 * user's address bar of any tokens that are set. Hence:
	 * <pre>
	 * SetStartupPage('MyWebPage', 'http://www.mysite.com/ErrorPage.html', URL::Display);
	 * // Assume the two lines are separated out in accordance with good object-oriented programming practices
	 * URL::SetToken('productid', 17);
	 * </pre>
	 * Will have the effect of writing #/productid=17 in the user's URL.
	 */
	const Display = 1;
	/**
	 * When passed into the third parameter, $tokenMode, of SetStartupPage, URL::Encrypt will display an encrypted string in the
	 * user's address bar of any tokens that are set. Hence:
	 * <pre>
	 * SetStartupPage('MyWebPage', 'http://www.mysite.com/ErrorPage.html', URL::Encrypt);
	 * // Assume the two lines are separated out in accordance with good object-oriented programming practices
	 * URL::SetToken('productid', 17);
	 * </pre>
	 * Will have the effect of writing random-looking characters in the user's URL.
	 */
	const Encrypt = 2;
	/**
	 * A possible value for the Destination of a Link, Tokens means the Link will point to the Tokens that have been set on that Link object.
	 */
	const Tokens = null;
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
	 * @param boolean $removeSubsequentTokens If true, every token appearing after the current one will be removed
	 * @return string The value passed in
	 */
	static function SetToken($tokenName, $tokenValue, $removeSubsequentTokens=false)
	{
		if($GLOBALS['_NURLTokenMode'] && (!isset($_SESSION['_NTokens'][$tokenName]) || $_SESSION['_NTokens'][$tokenName]!=$tokenValue))
		{
			self::QueueUpdateTokens();
			if($tokenValue === null)
				unset($_SESSION['_NTokens'][$tokenName]);
			else
				$_SESSION['_NTokens'][$tokenName] = $tokenValue;
			if($removeSubsequentTokens)
			{
				reset($_SESSION['_NTokens']);
				for($position=1; key($_SESSION['_NTokens'])!=$tokenName; ++$position)
					next($_SESSION['_NTokens']);
				array_splice($_SESSION['_NTokens'], $position);
			}
		}
		return $tokenValue;
	}
	/**
	 * Removes a particular URL token
	 * @param string $tokenName
	 */
	static function RemoveToken($tokenName)
	{
		if($GLOBALS['_NURLTokenMode'] && isset($_SESSION['_NTokens'][$tokenName]))
		{
			self::QueueUpdateTokens();
			unset($_SESSION['_NTokens'][$tokenName]);
		}
	}
	/**
	 * @ignore
	 */
	static function TokenString($keyValuePairs)
	{
		$str = '';
		foreach($keyValuePairs as $key => $val)
			$str .= urlencode($key) . '=' . urlencode($val) . '&';
		$str = rtrim($str, '&');
		return $GLOBALS['_NURLTokenMode'] == 2 ? base64_encode($str) : $str;
	}
	/**
	 * @ignore
	 */
	static function TokenDisplayText($keyValuePairs)
	{
		$str = '';
		foreach($keyValuePairs as $val)
			$str .= $val . ' ';
		$str = rtrim($str);
		return $str;
	}
	/**
	 * @ignore
	 */
	static function QueueUpdateTokens()
	{
		if(!isset($GLOBALS['_NTokenUpdate']))
		{
			$GLOBALS['_NTokenUpdate'] = true;
			if($GLOBALS['_NTokenTrailsExpiration'])
				$GLOBALS['_NInitialURLTokens'] = self::TokenString($_SESSION['_NTokens']);
		}
	}
	/**
	 * @ignore
	 */
	static function UpdateTokens()
	{
		$tokenString = self::TokenString($_SESSION['_NTokens']);
		AddScript('_NSetURL(\''.$tokenString.'\')', Priority::Low);
		self::UpdateTrails('?' . $tokenString, $GLOBALS['_NURLTokenMode']==2?$tokenString:self::TokenDisplayText($_SESSION['_NTokens']));
	}
	/**
	 * Redirects to another URL
	 * @param string $url
	 * @param string $searchEngineLinkText 
	 */
	static function Redirect($url, $searchEngineLinkText=null)
	{
		AddScript('location="'.$url.'";');
		if(!isset($GLOBALS['_NInitialURLTokens']))
			$GLOBALS['_NInitialURLTokens'] = self::TokenString($_SESSION['_NTokens']);
		self::UpdateTrails($url, $searchEngineLinkText?$searchEngineLinkText:$url);
	}
	/**
	 * @ignore
	 */
	static function UpdateTrails($tokenString, $tokenText)
	{
		if($GLOBALS['_NTokenTrailsExpiration'])
		{
			$initialURLString = $GLOBALS['_NInitialURLTokens'];
			$file = getcwd().'/NOLOHSearchTrails.dat';
			if(file_exists($file) && (time()-filemtime($file)<$GLOBALS['_NTokenTrailsExpiration']*86400))
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
			$trails[$initialURLString][$tokenString] = array($tokenText, time());
			if(is_writable($file))
				file_put_contents($file, base64_encode(serialize($trails)));
		}
	}
	/**
	 * Returns the URL Tracker, a ClientEvent that will get called when the URL changes, useful
	 * for various statistical purposes, e.g., Google Analytics.
	 * @return ClientEvent
	 */
	static function GetTracker()
	{
		return WebPage::That()->GetEvent('Tracker');
	}
	/**
	 * Sets the URL Tracker, a ClientEvent that will get called when the URL changes, useful
	 * for various statistical purposes, e.g., Google Analytics.
	 * @param ClientEvent $tracker
	 */
	static function SetTracker($tracker)
	{
		return WebPage::That()->SetEvent($tracker, 'Tracker');
	}
	/**
	 * Opens a URL in a new window
	 * @param string $url
	 * @param boolean $newBrowserNotPanel Specifies whether the url opens in a browser window, or alternatively, in a WindowPanel
	 */
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
			WebPage::That()->Controls->Add($wp);
		}
	}
	/**
	 * For browsers that support automatic bookmarks (sometimes known as favorites), it adds this application to the user's list. 
	 * Otherwise, it will tell the user how he may easily bookmark this application manually.
	 */
	static function Bookmark()
	{
		switch(UserAgent::GetBrowser())
		{
			case 'ie':
				AddScript('window.external.AddFavorite(document.URL,document.title);', Priority::Low);
				break;
			case 'ff': 
				AddScript('window.sidebar.addPanel(document.title,document.URL,"");', Priority::Low);
				break;
			case 'op':
				$version = (int)substr($_SERVER['HTTP_USER_AGENT'], strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') + 6);
				if($version === 9)
				{
					WebPage::That()->Controls->Add($wp = new WindowPanel('Bookmark', 0, 0, 330, 70));
					$wp->Controls->Add($link = new Link(null, 'Please click here to bookmark this application.', 20, 5, null, null));
					$link->Click = $wp->CloseImage->Click;
					NolohInternal::SetProperty('rel', 'sidebar', $link);
					AddScript('var a=_N("' . $link->Id . '");a.href=window.location;a.title=document.title;', Priority::Low);
					break;
				}
				if($version === 7 || $version === 8)
				{
					AddScript('var a=document.createElement("a");a.href=document.URL;a.title=document.title;a.rel="sidebar";a.click();', Priority::Low);
					break;
				}
			case 'sa':
				$first = UserAgent::GetOperatingSystem() === 'mac' ? 'Cmd' : 'Ctrl';
				$second = strpos($_SERVER['HTTP_USER_AGENT'], 'konqueror') === false ? 'D' : 'B';
				Alert('Please press ' . $first . '+' . $second . ' to bookmark this application.');
		}
	}
}

?>