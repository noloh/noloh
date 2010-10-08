<?php
/**
 * UserAgent class
 *
 * This class has static functions that pertain to retrieving user agent's information, such as browser and operating system.
 * As NOLOH automatically handles browser and operating system based inconsistencies, it is extremely rare and discouraged
 * that someone would need to uses this class to retrieve that information themselves. It is, however, still available for
 * certain uses, e.g., tracking and statistical information about your users to a database or file.
 * 
 * Examples:
 * 
 * <pre>
 * put_file_contents('/tmp/users.dat', UserAgent::GetOperatingSystem(), FILE_APPEND);
 * </pre>
 * 
 * <pre>
 * Alert('Congratulations on using this app in ' . UserAgent::GetBrowser() . '! That is by far the hardest browser to develop for without NOLOH!';
 * </pre>
 * 
 * @package Statics
 */
final class UserAgent
{
	/**
	 * The Chrome browser
	 */
	const Chrome = 'ch';
	/**
	 * A short-hand for the Internet Explorer browser
	 */
	const IE = 'ie';
	/**
	 * The Internet Explorer browser
	 */
	const InternetExplorer = 'ie';
	/**
	 * The Links browser
	 */
	const Links = 'li';
	/**
	 * The Firefox family of browsers (e.g., it includes Gecko)
	 */
	const Firefox = 'ff';
	/**
	 * The Opera browser
	 */
	const Opera = 'op';
	/**
	 * The Safari family of browsers (e.g., it includes Konquerer)
	 */
	const Safari = 'sa';
	/**
	 * The Windows operating system
	 */
	const Windows = 'win';
	/**
	 * The Macintosh operation system
	 */
	const Macintosh = 'mac';
	/**
	 * The Linux family of operating system (e.g., it includes Unix)
	 */
	const Linux = 'lin';
	/**
	 * @ignore
	 */
	private function UserAgent() {}
	/**
	 * @ignore
	 */
	static function LoadInformation()
	{
		$userInfo = array();
		$agt = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
		
		/*
		if(strpos($agt, 'google') !== false || 
		   strpos($agt, 'msn') !== false ||
		   strpos($agt, 'yahoo') !== false ||
		   strpos($agt, 'jeeves') !== false ||
		   strpos($agt, 'altavista') !== false ||
		   strpos($agt, 'yahoo') !== false ||
		   strpos($agt, 'infoseek') !== false ||
		   strpos($agt, 'yahoo') !== false ||
		   strpos($agt, 'lycos') !== false ||
		   strpos($agt, 'libwww-perl') !== false ||
		   )
		*/
		
		$_SESSION['_NIsIE'] = false;
		if(preg_match('!chrome/([0-9.]+) !', $agt, $version))
        	$_SESSION['_NBrowser'] = 'ch';
        elseif(strpos($agt, 'konqueror') !== false || strpos($agt, 'safari') !== false)
        {
        	preg_match('!version/([0-9.]+) !', $agt, $version);
        	$_SESSION['_NBrowser'] = 'sa';
        }
        elseif(strpos($agt, 'gecko') !== false && preg_match('!firefox/([0-9.]+)!', $agt, $version))
        	$_SESSION['_NBrowser'] = 'ff';
        elseif(preg_match('!opera[ /]([0-9.]+) !', $agt, $version))
        	$_SESSION['_NBrowser'] = 'op';
        elseif(preg_match('!msie ([0-9.]+);!', $agt, $version))
        {
        	$_SESSION['_NBrowser'] = 'ie';
        	$_SESSION['_NIsIE'] = true;
        	if($version[1] == 6)
        		$_SESSION['_NIE6'] = true;
        }
        elseif(preg_match('!links \(([0-9.]+);!', $agt, $version))
        	$_SESSION['_NBrowser'] = 'li';
        else
        	$_SESSION['_NBrowser'] = 'other';
        $_SESSION['_NBrowserVersion'] = $version[1];
        
        if(strpos($agt, 'win') !== false || strpos($agt, '16bit') !== false)
        	$_SESSION['_NOS'] = 'win';
        elseif(strpos($agt, 'mac') !== false)
        	$_SESSION['_NOS'] = 'mac';
        elseif(strpos($agt, 'inux') !== false)
        	$_SESSION['_NOS'] = 'lin';
        elseif(strpos($agt, 'unix') !== false)
        	$_SESSION['_NOS'] = 'unix';
        else
        	$_SESSION['_NOS'] = 'other';
	}
	/**
	 * Returns the user's browser
	 * @return mixed
	 */
	public static function GetBrowser()
	{
		return $_SESSION['_NBrowser'];
	}
	/**
	 * Returns whether or not the user is using internet explorer as their browser.<br>
	 * This is identical in functionality to UserAgent::GetBrowser()==UserAgent::IE, but provides a shortcut as, in practice, internet explorer
	 * often needs code different than other browsers.
	 * @return boolean
	 */
	public static function IsIE()
	{
		return $_SESSION['_NIsIE'];
	}
	/**
	 * Returns whether or not PHP is running from the command-line.
	 * @return boolean
	 */
	function IsCLI() 
	{
		return php_sapi_name() === 'cli' && empty($_SERVER['REMOTE_ADDR']);
	}
	/**
	 * Returns whether or not the user agent is a spider, e.g., a search bot.
	 */
	function IsSpider()
	{
		return $_SESSION['_NBrowser'] === 'other' && $_SESSION['_NOS'] === 'other';
	}
	/**
	 * @ignore
	 */
	public static function IsWebKit()
	{
		$browser = $_SESSION['_NBrowser'];
		return $browser === 'ch' || $browser === 'sa';
	}
	/**
	 * Returns the user's browser version
	 * @return string
	 */
	public static function GetBrowserVersion()
	{
		return $_SESSION['_NBrowserVersion'];
	}
	/**
	 * @ignore
	 */
	public static function IsIE6()
	{
		return $_SESSION['_NIsIE'] && isset($_SESSION['_NIE6']);
	}
	/**
	 * Returns the user's operating system
	 * @return mixed
	 */
	public static function GetOperatingSystem()
	{
		return $_SESSION['_NOS'];
	}
	
	/*
	private static $Browser;
	private static $Version;
	private static $Platform;
	private static $IsIE;
	
    static function LoadInformation()
    {
        $browser = array_flip(array('ns', 'ns2', 'ns3', 'ns4', 'ns4up', 'nav', 'ns6', 'ns6up', 'firefox', 'firefox0.x', 'firefox1.x', 'gecko', 'ie', 'ie3', 'ie4', 'ie4up', 'ie5', 'ie5_5', 'ie5up', 'ie6', 'ie6up', 'opera', 'opera2', 'opera3', 'opera4', 'opera5', 'opera6', 'opera7', 'opera5up', 'opera6up', 'opera7up', 'aol', 'aol3', 'aol4', 'aol5', 'aol6', 'aol7', 'aol8', 'webtv', 'aoltv', 'tvnavigator', 'hotjava', 'hotjava3', 'hotjava3up', 'konq', 'safari', 'netgem', 'webdav'));
        $os = array_flip(array('win', 'win95', 'win16', 'win31', 'win9x', 'win98', 'winme', 'win2k', 'winxp', 'winnt', 'win2003', 'os2', 'mac', 'mac68k', 'macppc', 'linux', 'unix', 'vms', 'sun', 'sun4', 'sun5', 'suni86', 'irix', 'irix5', 'irix6', 'hpux', 'hpux9', 'hpux10', 'aix', 'aix1', 'aix2', 'aix3', 'aix4', 'sco', 'unixware', 'mpras', 'reliant', 'dec', 'sinix', 'freebsd', 'bsd'));

        $majorVersion = 0;
        $subVersion = 0;

        if (isset($_SERVER['HTTP_USER_AGENT']))
        	$userAgent = $_SERVER['HTTP_USER_AGENT'];
        else
        	$userAgent = '';
        	
        $agt = strtolower($userAgent);
        
        if (preg_match(";^([[:alnum:]]+)[ /\(]*[[:alpha:]]*([\d]*)(\.[\d\.]*);", $agt, $matches))
            list(, $leadingIdentifier, $majorVersion, $subVersion) = $matches;
    
        // Browser type
        $browser['webdav']  = ($agt == 'microsoft data access internet publishing provider dav' || $agt == 'microsoft data access internet publishing provider protocol discovery');
        $browser['konq']    = $browser['safari'] = (strpos($agt, 'konqueror') !== false || strpos($agt, 'safari') !== false);
        $browser['text']    = strpos($agt, 'links') !== false || strpos($agt, 'lynx') !== false || strpos($agt, 'w3m') !== false;
        $browser['ns']      = strpos($agt, 'mozilla') !== false && !(strpos($agt, 'spoofer') !== false) && !(strpos($agt, 'compatible') !== false) && !(strpos($agt, 'hotjava') !== false) && !(strpos($agt, 'opera') !== false) && !(strpos($agt, 'webtv') !== false) ? 1 : 0;
        $browser['netgem']  = strpos($agt, 'netgem') !== false;
        $browser['ns2']     = $browser['ns'] && $majorVersion == 2;
        $browser['ns3']     = $browser['ns'] && $majorVersion == 3;
        $browser['ns4']     = $browser['ns'] && $majorVersion == 4;
        $browser['ns4up']   = $browser['ns'] && $majorVersion >= 4;
        // determine if this is a Netscape Navigator
        $browser['nav']     = $browser['ns'] && $majorVersion < 5;
        $browser['ns6']     = !$browser['konq'] && $browser['ns'] && $majorVersion == 5;
        $browser['ns6up']   = $browser['ns6'] && $majorVersion >= 5;
        $browser['gecko']   = strpos($agt, 'gecko') !== false && !$browser['konq'];
        $browser['firefox'] = $browser['gecko'] && strpos($agt, 'firefox') !== false;
        $browser['firefox0.x'] = $browser['firefox'] && strpos($agt, 'firefox/0.') !== false;
        $browser['firefox1.x'] = $browser['firefox'] && strpos($agt, 'firefox/1.') !== false;
        $browser['ie']      = strpos($agt, 'msie') !== false && !(strpos($agt, 'opera') !== false);
        $browser['ie3']     = $browser['ie'] && $majorVersion < 4;
        $browser['ie4']     = $browser['ie'] && $majorVersion == 4 && (strpos($agt, 'msie 4') !== false);
        $browser['ie4up']   = $browser['ie'] && !$browser['ie3'];
        $browser['ie5']     = $browser['ie4up'] && (strpos($agt, 'msie 5.0') !== false);
        $browser['ie5_5']   = $browser['ie4up'] && (strpos($agt, 'msie 5.5') !== false);
        $browser['ie5up']   = $browser['ie4up'] && !$browser['ie3'] && !$browser['ie4'];
        //$browser['ie5_5up'] = $browser['ie5up'] && !$browser['ie5'];
        $browser['ie6']     = strpos($agt, 'msie 6') !== false;
        $browser['ie6up']   = $browser['ie5up'] && !$browser['ie5'] && !$browser['ie5_5'];
        $browser['opera']   = strpos($agt, 'opera') !== false;
        $browser['opera2']  = strpos($agt, 'opera 2') !== false || strpos($agt, 'opera/2') !== false;
        $browser['opera3']  = strpos($agt, 'opera 3') !== false || strpos($agt, 'opera/3') !== false;
        $browser['opera4']  = strpos($agt, 'opera 4') !== false || strpos($agt, 'opera/4') !== false;
        $browser['opera5']  = strpos($agt, 'opera 5') !== false || strpos($agt, 'opera/5') !== false;
        $browser['opera6']  = strpos($agt, 'opera 6') !== false || strpos($agt, 'opera/6') !== false;
        $browser['opera7']  = strpos($agt, 'opera 7') !== false || strpos($agt, 'opera/7') !== false;
        $browser['opera5up'] = $browser['opera'] && !$browser['opera2'] && !$browser['opera3'] && !$browser['opera4'];
        $browser['opera6up'] = $browser['opera'] && !$browser['opera2'] && !$browser['opera3'] && !$browser['opera4'] && !$browser['opera5'];
        $browser['opera7up'] = $browser['opera'] && !$browser['opera2'] && !$browser['opera3'] && !$browser['opera4'] && !$browser['opera5'] && !$browser['opera6'];

        $browser['aol']   = strpos($agt, 'aol') !== false;
        $browser['aol3']  = $browser['aol'] && $browser['ie3'];
        $browser['aol4']  = $browser['aol'] && $browser['ie4'];
        $browser['aol5']  = strpos($agt, 'aol 5') !== false;
        $browser['aol6']  = strpos($agt, 'aol 6') !== false;
        $browser['aol7']  = strpos($agt, 'aol 7') !== false || strpos($agt, 'aol7') !== false;
        $browser['aol8']  = strpos($agt, 'aol 8') !== false || strpos($agt, 'aol8') !== false;
        $browser['webtv'] = strpos($agt, 'webtv') !== false; 
        $browser['aoltv'] = $browser['tvnavigator'] = strpos($agt, 'navio') !== false || strpos($agt, 'navio_aoltv') !== false; 
        $browser['hotjava'] = strpos($agt, 'hotjava') !== false;
        $browser['hotjava3'] = $browser['hotjava'] && $majorVersion == 3;
        $browser['hotjava3up'] = $browser['hotjava'] && $majorVersion >= 3;

        // OS Check 
        $os['win']   = strpos($agt, 'win') !== false || strpos($agt, '16bit') !== false;
        $os['win95'] = strpos($agt, 'win95') !== false || strpos($agt, 'windows 95') !== false;
        $os['win16'] = strpos($agt, 'win16') !== false || strpos($agt, '16bit') !== false || strpos($agt, 'windows 3.1') !== false || strpos($agt, 'windows 16-bit') !== false;  
        $os['win31'] = strpos($agt, 'windows 3.1') !== false || strpos($agt, 'win16') !== false || strpos($agt, 'windows 16-bit') !== false;
        $os['winme'] = strpos($agt, 'win 9x 4.90') !== false;
        $os['win2k'] = strpos($agt, 'windows nt 5.0') !== false;
        $os['winxp'] = strpos($agt, 'windows nt 5.1') !== false;
        $os['win2003'] = strpos($agt, 'windows nt 5.2') !== false;
        $os['win98'] = strpos($agt, 'win98') !== false || strpos($agt, 'windows 98') !== false;
        $os['win9x'] = $os['win95'] || $os['win98'];
        $os['winnt'] = (strpos($agt, 'winnt') !== false || strpos($agt, 'windows nt') !== false) && strpos($agt, 'windows nt 5') === false;
        $os['win32'] = $os['win95'] || $os['winnt'] || $os['win98'] || $majorVersion >= 4 && strpos($agt, 'win32') !== false || strpos($agt, '32bit') !== false;
        $os['os2']   = strpos($agt, 'os/2') !== false || strpos($agt, 'ibm-webexplorer') !== false;
        $os['mac']   = strpos($agt, 'mac') !== false;
        $os['mac68k']   = $os['mac'] && (strpos($agt, '68k') !== false || strpos($agt, '68000') !== false);
        $os['macppc']   = $os['mac'] && (strpos($agt, 'ppc') !== false || strpos($agt, 'powerpc') !== false);
        $os['sun']      = strpos($agt, 'sunos') !== false;
        $os['sun4']     = strpos($agt, 'sunos 4') !== false;
        $os['sun5']     = strpos($agt, 'sunos 5') !== false;
        $os['suni86']   = $os['sun'] && strpos($agt, 'i86') !== false;
        $os['irix']     = strpos($agt, 'irix') !== false;
        $os['irix5']    = strpos($agt, 'irix 5') !== false;
        $os['irix6']    = strpos($agt, 'irix 6') !== false || strpos($agt, 'irix6') !== false;
        $os['hpux']     = strpos($agt, 'hp-ux') !== false;
        $os['hpux9']    = $os['hpux'] && strpos($agt, '09.') !== false;
        $os['hpux10']   = $os['hpux'] && strpos($agt, '10.') !== false;
        $os['aix']      = strpos($agt, 'aix') !== false;
        $os['aix1']     = strpos($agt, 'aix 1') !== false;
        $os['aix2']     = strpos($agt, 'aix 2') !== false;
        $os['aix3']     = strpos($agt, 'aix 3') !== false;
        $os['aix4']     = strpos($agt, 'aix 4') !== false;
        $os['linux']    = strpos($agt, 'inux') !== false;
        $os['sco']      = strpos($agt, 'sco') !== false || strpos($agt, 'unix_sv') !== false;
        $os['unixware'] = strpos($agt, 'unix_system_v') !== false; 
        $os['mpras']    = strpos($agt, 'ncr') !== false; 
        $os['reliant']  = strpos($agt, 'reliant') !== false;
        $os['dec']      = strpos($agt, 'dec') !== false || strpos($agt, 'osf1') !== false || strpos($agt, 'dec_alpha') !== false || strpos($agt, 'alphaserver') !== false || strpos($agt, 'ultrix') !== false || strpos($agt, 'alphastation') !== false;
        $os['sinix']    = strpos($agt, 'sinix') !== false;
        $os['freebsd']  = strpos($agt, 'freebsd') !== false;
        $os['bsd']      = strpos($agt, 'bsd') !== false;
        $os['unix']     = strpos($agt, 'x11') !== false || strpos($agt, 'unix') !== false || $os['sun'] || $os['irix'] || $os['hpux'] || $os['sco'] || $os['unixware'] || $os['mpras'] || $os['reliant'] || $os['dec'] || $os['sinix'] || $os['aix'] || $os['linux'] || $os['bsd'] || $os['freebsd'];
        $os['vms']      = strpos($agt, 'vax') !== false || strpos($agt, 'openvms') !== false;
	
        $tempBrowserArray = array();
        foreach($browser as $key => $val)
        	if($val)
				$tempBrowserArray[$key] = $val;  
				
        $tempOSArray = array();
        foreach($os as $key => $val)
        	if($val)
				$tempOSArray[$key] = $val;        	

		SetGlobal("Browser", $tempBrowserArray);
		SetGlobal("Platform", $tempOSArray);
		SetGlobal("Version", $majorVersion . $subVersion);
		SetGlobal("IsIE", (self::IsBrowser("ie"))?true:false);
		if(self::IsBrowser("ie"))
			$browserName = "ie";
		elseif(self::IsBrowser("firefox"))
			$browserName = "ff";
		elseif(self::IsBrowser("sa"))
			$browserName = "sa";
		elseif(self::IsBrowser("op"))
			$browserName = "op";
		else 
			$browserName = "other";
		SetGlobal("BrowserName", $browserName);
    }
	public static function GetBrowser(){return GetGlobal("Browser");}
	public static function GetVersion(){return GetGlobal("Version");}
	public static function GetPlatform(){return GetGlobal("Platform");}
	public static function IsIE(){return GetGlobal("IsIE");}
	public static function IsBrowser($browser = null)
	{
		$browser = strtolower($browser);
		$tempBrowserArray = self::GetBrowser();
		foreach($tempBrowserArray as $key => $val)
			if(strpos($key, $browser) !== false)
	        	return true;
	    return false;
	}
	public static function IsOS($os = null)
	{
		$os = strtolower($os);
		$tempOSArray = self::GetPlatform();
		//print_r($tempOSArray);
		foreach($tempOSArray as $key => $val)
			if(strpos($key, $os) !== false)
	        	return true;
	    return false;
	}
	*/
}

?>