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
 * System::Alert('Congratulations on using this app in ' . UserAgent::GetBrowser() . '! That is by far the hardest browser to develop for without NOLOH!';
 * </pre>
 * 
 * @package Statics
 */
final class UserAgent
{
	/******* DEVICES *******/
	/**
	 * A personal computer Device. Note: When speaking of various types of devices, a Mac is still considered a personal computer.
	 */
	const PC = 'pc';
	/**
	 * A search engine spider Device
	 */
	const Spider = 'spi';
	/*
	 * A mobile Device
	 */
	const Mobile = 'mob';
	/**
	 * A slate device
	 */
	const Slate = 'sla';
	
	
	/******* BROWSERS *******/
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
	
	
	/******* OPERATING SYSTEMS *******/
	/**
	 * The Windows operating system
	 */
	const Windows = 'win';
	/**
	 * The Mac operating system
	 */
	const Mac = 'mac';
	/**
	 * An alias for the Mac operation system
	 */
	const Macintosh = 'mac';
	/**
	 * The Linux family of operating system (e.g., it includes Unix)
	 */
	const Linux = 'lin';
	
	
	/******* MOBILES *******/
	/**
	 * The iPhone mobile
	 */
	const IPhone = 'ipho';
	/**
	 * The Android mobile
	 */
	const Android = 'andr';
	/**
	 * The Blackberry mobile
	 */
	const Blackberry = 'bberry';
	/**
	 * The Palm mobile
	 */
	const Palm = 'palm';
	/**
	 * The Windows Mobile
	 */
	const WindowsMobile = 'winmo';
	
	/**
	 * The iPad
	 */
	const IPad = 'ipad';
	/**
	 * The Googlebot search engine Spider
	 */
	const Googlebot = 'gbot';
	/**
	 * @ignore
	 */
	private function UserAgent() {}
	/**
	 * @ignore
	 */
	static function LoadInformation()
	{
		$agt = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
		$_SESSION['_NIsIE'] = false;
		
		// Devices
		if($name = self::LoadMobile($agt))
			$device = self::Mobile;
		elseif($name = self::LoadSlate($agt))
			$device = self::Slate;
		else
			$device = self::PC;
		
		// Browsers
		if(preg_match('!chrome/([0-9.]+) !', $agt, $version))
        	$browser = 'ch';
        elseif(strpos($agt, 'konqueror') !== false || strpos($agt, 'safari') !== false)
        {
        	preg_match('!version/([0-9.]+) !', $agt, $version);
        	$browser = 'sa';
        }
        elseif(strpos($agt, 'gecko') !== false && preg_match('!firefox/([0-9.]+)!', $agt, $version))
        	$browser = 'ff';
        elseif(preg_match('!opera[ /]([0-9.]+) !', $agt, $version))
        	$browser = 'op';
        elseif(preg_match('!msie ([0-9.]+);!', $agt, $version))
        {
        	$browser = 'ie';
        	$_SESSION['_NIsIE'] = true;
        	if($version[1] == 6)
        		$_SESSION['_NIE6'] = true;
        }
        elseif(preg_match('!links \(([0-9.]+);!', $agt, $version))
        	$browser = 'li';
        elseif(empty($name))
        {
        	$browser = 'other';
			$device = self::Spider;
        	if(preg_match('!googlebot/([0-9.]+)[ ;]!', $agt, $version))
        		$name = self::Googlebot;
        }
        
        $_SESSION['_NBrowserVersion'] = $version[1];
        
        $os = self::LoadOS($agt);
        
        $_SESSION['_NUserAgent'] = array($device, empty($name) ? $browser : $name, $version[1]);
        $_SESSION['_NBrowser'] = $browser;
        $_SESSION['_NOS'] = $os;
	}
	/**
	 * @ignore
	 */
	private static function LoadMobile($agt)
	{
		if(strpos($agt, 'iphone') !== false)
			return self::IPhone;
		elseif(strpos($agt, 'android') !== false)
			return self::Android;
//		elseif(strpos($agt, 'opera mini') !== false)
//			return self::OperaMini;
		elseif(strpos($agt, 'blackberry') !== false)
			return self::Blackberry;
		elseif(preg_match('/pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine/', $agt))
			return self::Palm;
		elseif(preg_match('/iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile/', $agt))
			return self::WindowsMobile;
		else
		{
			$other = 'other';
			
			if(preg_match('/mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo/', $agt))
				return $other;
			$accept = isset($_SERVER['HTTP_ACCEPT']) ? strtolower($_SERVER['HTTP_ACCEPT']) : '';
			if((strpos($accept,'text/vnd.wap.wml')>0) || (strpos($accept,'application/vnd.wap.xhtml+xml')>0))
				return $other;
			if(isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']))
				return $other;
				
			return false;
		}
			

		/*
	case (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i',$user_agent)); // check if any of the values listed create a match on the user agent - these are some of the most common terms used in agents to identify them as being mobile devices - the i at the end makes it case insensitive
      $mobile_browser = true; // set mobile browser to true
      $status = 'Mobile matched on piped preg_match';
    break; // break out and skip the rest if we've preg_match on the user agent returned true 

    case ((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0)); // is the device showing signs of support for text/vnd.wap.wml or application/vnd.wap.xhtml+xml
      $mobile_browser = true; // set mobile browser to true
      $status = 'Mobile matched on content accept header';
    break; // break out and skip the rest if we've had a match on the content accept headers

    case (isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE'])); // is the device giving us a HTTP_X_WAP_PROFILE or HTTP_PROFILE header - only mobile devices would do this
      $mobile_browser = true; // set mobile browser to true
      $status = 'Mobile matched on profile headers being set';
    break; // break out and skip the final step if we've had a return true on the mobile specfic headers

    case (in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','hiba'=>'hiba','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',))); // check against a list of trimmed user agents to see if we find a match
      $mobile_browser = true; // set mobile browser to true
      $status = 'Mobile matched on in_array';
    break; // break even though it's the last statement in the switch so there's nothing to break away from but it seems better to include it than exclude it
		*/
	}
	/**
	 * @ignore
	 */
	private static function LoadSlate($agt)
	{
		if(strpos($agt, 'ipad') !== false)
        	return self::IPad;
        else
        	return false;
	}
	/**
	 * @ignore
	 */
	private static function LoadOS($agt)
	{
		if(strpos($agt, 'win') !== false || strpos($agt, '16bit') !== false)
        	return 'win';
        elseif(strpos($agt, 'mac') !== false)
        	return 'mac';
        elseif(strpos($agt, 'inux') !== false)
        	return 'lin';
        elseif(strpos($agt, 'unix') !== false)
        	return 'unix';
        else
        	return 'other';
	}
	/**
	 * Returns the user's device type
	 * @return mixed
	 */
	public static function GetDevice()
	{
		return $_SESSION['_NUserAgent'][0];
	}
	/**
	 * Returns the name of the client
	 * @return mixed
	 */
	public static function GetName()
	{
		return $_SESSION['_NUserAgent'][1];
	}
	/**
	 * Returns the version of the client software
	 * @return mixed
	 */
	public static function GetVersion()
	{
		return $_SESSION['_NUserAgent'][2];
	}
	/**
	 * Returns an array consisting of the device, name, and version
	 * @return array
	 */
	public static function GetInfo()
	{
		return array_combine(array('Device', 'Name', 'Version'), $_SESSION['_NUserAgent']);
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