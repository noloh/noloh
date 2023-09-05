<?php
/**
 * Configuration class
 *
 * A Configuration object is used together with Application::Start to set some application-wide,
 * global settings. Alternatively, an associative array (indexed by the names of the properties,
 * in the same case and format) can be used for an equivalent purpose.
 * 
 * @package System
 */
class Configuration extends Base implements Singleton
{
	/**
	 *  A possible value for the TimeoutAction property, Alert indicates that a simple alert will be displayed.
	 */
	const Alert = 'Alert';
	/**
	 *  A possible value for the TimeoutAction property, Confirm indicates that the user will be prompted if he wishes to continue;
	 */
	const Confirm = 'Confirm';
	/**
	 * The name of the WebPage class that serves as the inital start-up point of your application
	 * @var string 
	 */
	public $StartClass;
	/**
	 * If a user's browser is not supported, or he does not have JavaScript enabled, 
	 * this will be the URL of the error page to which he is navigated.
	 * A value of null will use NOLOH's alternative rendering to create a more degraded, 
	 * non-JavaScript application
	 * @var string
	 */
	public $UnsupportedURL;
	/**
	 * Allows a developer to specify a URL that mobile users will be redirected to for their mobile-specific app.
	 * @var string
	 */
	public $MobileAppURL;
	/**
	 * Specified how URL tokens are displayed
	 * @var URL::Display|URL::Encrypt|URL::Disable
	 */
	public $URLTokenMode = URL::Display;
	/**
	 * Specifies the number of days until token search trails file expires
	 * @var integer
	 */
	public $TokenTrailsExpiration = 14;
	/**
	 * Specifies the level of error-handling: true gives specific errors for developers, false gives generic errors for users, and System::Unhandled does not fail gracefully but crashes
	 * @var boolean|System::Unhandled
	 */
	private $DebugMode = true;
	/**
	 * Error message displayed when not in debug mode.
	 * @var string
	 */
	public $DebugModeError = 'An application error has occurred.';
	/**
	 * @ignore
	 */
	public $DefaultUnit = 'px';
	/**
	 * Whether your script name is shown in the url.
	 * ex. http://www.noloh.com/index.php vs. http://www.noloh.com/
	 * @var boolean|System::Auto
	 */
	public $ShowURLFilename = 'Auto';
	/**
	 * Whether search engine spiders get directed to an HTTPS version of your application
	 * @var boolean
	 */
	public $SpiderSSL = false;
	/**
	 * Specificies a URL to a reset CSS stylesheet, in case you wish to use one other than NOLOH's default CSS reset.
	 * @var string
	 */
	public $CSSReset;
	/**
	 * Specificies a URL to a reset CSS stylesheet for older Internet Explorers, in case you wish to use one other than NOLOH's default.
	 * @var string
	 */
	public $CSSResetLegacyIE;
	/**
	 * The number of seconds it takes for the application to time out.
	 * @var integer
	 */
	public $TimeoutDuration = 0;
	/**
	 * The action that will be taken when the Application times out according to the TimeoutDuration parameter.
	 * @var null|Configuration::Alert|Configuration::Prompt
	 */
	public $TimeoutAction = Configuration::Alert;
	/**
	 * Whether an application that implements MobileApp is zoomable via gestures
	 * @var boolean
	 */
	public $Zoomable = true;
	/**
	 * Whether or not the application will be obvious about using NOLOH. Note: this is not guaranteed; there are still ways to tell.
	 * @var boolean
	 */
	public $ExposeNOLOH = true;
	/**
	 * Whether or not a query of mtime will be added to sources. This helps with caching.
	 * @var boolean
	 */
	public $AddMTimeToExternals = false;
	/**
	 * Whether or not HTTPS is forced
	 * @var boolean
	 */
	public $ForceSecureProtocol = false;
	/**
	 * Array storing the callback function to be passed from app to Core for validation of mobile sso. The relevant function will be called as static and must return a boolean value.
	 * @var array('Class', 'Function')
	 */
	public $MobileSsoValidationCallBack = array();
	/**
	* Specify an Encryption Key to be used in scenarios where necessary. Ex. DataConnections.
	*
	*/
	public $EncryptionKeyPath = null;
	/**
	 * Constructor
	 * @return Configuration
	 */
	public function __construct()
	{
		parent::__construct();
		if (Configuration::That())
		{
			foreach (get_object_vars(Configuration::That()) as $name => $value)
			{
				$this->$name = $value;
			}
		}
		$args = func_get_args();
		$argLength = func_num_args();
        $firstIndex = 0;
		if($argLength)
        {
            if($args[0] === 'Auto')
            {
                $this->DetectStartClass();
                $firstIndex = 1;
            }
        }
        else
            $this->DetectStartClass();
		for($i=$firstIndex; $i<$argLength; ++$i)
		{
			if(is_array($args[$i]))
				foreach($args[$i] as $name => $value)
					$this->$name = $value;
			elseif($args[$i] instanceof Configuration)
				foreach(get_object_vars($args[$i]) as $name => $value)
					$this->$name = $value;
			else 
			{
				if(!isset($setStartupLegacy))
					$setStartupLegacy = array('StartClass', 'UnsupportedURL', 'URLTokenMode', 'TokenTrailsExpiration', 'DebugMode');
				$this->{$setStartupLegacy[$i]} = $args[$i];
			}
		}

		$GLOBALS['_NDebugMode'] = $this->DebugMode;
		$GLOBALS['_NConfiguration'] = $this;
	}
    private function DetectStartClass()
    {
        $classes = get_declared_classes();
        $classLength = count($classes);
        for ($i = $classLength - 1; $i; --$i)
		{
			if (is_subclass_of($classes[$i], 'WebPage') ||
				is_subclass_of($classes[$i], 'RESTRouter'))
			{
				$this->StartClass = $classes[$i];
				break;
			}
		}
    }
    /**
     * @ignore
     */
	public function GetClientInitParams()
    {
		$arr = array();
		
		/*$arr['DebugMode'] = (isset($GLOBALS['_NDebugMode'])
			? (is_bool($GLOBALS['_NDebugMode']) 
				? ($GLOBALS['_NDebugMode']?'true':'false')
				: ('"'.$GLOBALS['_NDebugMode'].'"'))
			: 'null');*/
		$debugMode = isset($GLOBALS['_NDebugMode']) ? $GLOBALS['_NDebugMode'] : null;
		if($debugMode !== true)
			$arr['DebugMode'] = $debugMode;
		
		$factor = 1000 / 500; // Where 500 is the _NURLCheck
		$serverTimeout = intval(ini_get('session.gc_maxlifetime'));
		$paddedServerTimeout = $serverTimeout > 240 ? ($serverTimeout - 60) : (($serverTimeout * 3) / 4);
		if($this->TimeoutDuration)
		{
			ClientScript::AddNOLOHSource('Timeout.js');
			$timeoutDuration = $serverDuration>$this->TimeoutDuration ? $serverDuration : $this->TimeoutDuration;
			$timeoutTicks = $paddedServerTimeout < $timeoutDuration ? $paddedServerTimeout : $timeoutDuration;
			$arr['TimeoutAction'] = $this->TimeoutAction;
		}
		else
			$timeoutTicks = $paddedServerTimeout;
		if(isset($timeoutDuration))
			$arr['TimeoutDuration'] = floor($timeoutDuration * $factor);
		// 2760 = (1440 - 60) * 2. 1440 is default maxlifetime, 60 is default padding for that number, 2 is default factor
		if($timeoutTicks !== 2760)
			$arr['TimeoutTicks'] = floor($timeoutTicks * $factor);
		
		
		/*$defaults = array('DebugMode' => 'true');
		foreach($defaults as $key => $val)
			if($arr[$key] === $defaults[$key])
				unset($arr[$key]);*/
		return $arr;
    }

	/**
	 * @ignore
	 */
    public function GetDebugMode()
	{
		return $this->DebugMode;
	}
	/**
	 * @ignore
	 */
	public function SetDebugMode($debugMode)
	{
		$GLOBALS['_NDebugMode'] = $debugMode;
		$this->DebugMode = $debugMode;
	}
	/**
	 * Returns the instance of Configuration currently in use. The name is a pun on the "this" concept. See also Singleton interface.
	 * @return Configuration
	 */
	static function That()
	{
		return isset($_SESSION['_NConfiguration']) ? $_SESSION['_NConfiguration'] : $GLOBALS['_NConfiguration'];
	}
}
?>
