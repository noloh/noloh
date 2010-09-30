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
class Configuration extends Object implements Singleton
{
	public $StartClass;
	public $UnsupportedURL;
	public $URLTokenMode = URL::Display;
	public $TokenTrailsExpiration = 14;
	public $DebugMode = true;
	public $DefaultUnit = 'px';
	public $ShowURLFilename = 'Auto';
	public $SpiderSSL = false;
	public $CSSReset;
	public $CSSResetLegacyIE;
	
	/*
	 * Specifies which WebPage class will serve as the initial start-up point of your application
	 * @param string $className The name of the class that extends WebPage, as a string
	 * @param string $unsupportedURL If a user's browser is not supported, or he does not have JavaScript enabled, this will be the URL of the error page to which he is navigated. A value of null will use NOLOH's to create a more degraded, non-JavaScript application
	 * @param mixed $urlTokenMode Specifies how URL tokens are displayed. Possible values are URL::Display, URL::Encrypt, or URL::Disable
	 * @param integer $tokenTrailsExpiration Specifies the number of days until token search trails file expires. Please see Search Engine Friendly documentation for more information
	 * @param mixed $debugMode Specifies the level of error-handling: true gives specific errors for developers, false gives generic errors for users, and System::Unhandled does not fail gracefully but crashes
	 */
	/*public static function SetStartUpPage($className, $unsupportedURL=null, $urlTokenMode=URL::Display, $tokenTrailsExpiration=14, $debugMode=true)
	{
		new Application($className, $unsupportedURL, $urlTokenMode, $tokenTrailsExpiration, $debugMode);
	}*/
	
	/**
	 * Constructor
	 * 
	 * @return Configuration
	 */
	function Configuration()
	{
		parent::Object();
		$args = func_get_args();
		$argLength = func_num_args();
        $firstIndex = 0;
		if($argLength)
        {
            if($args[0] === System::Auto)
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
	}
    private function DetectStartClass()
    {
        $classes = get_declared_classes();
        $classLength = count($classes);
        for($i=$classLength-1; $i; --$i)
            if(is_subclass_of($classes[$i], 'WebPage'))
            {
                $this->StartClass = $classes[$i];
                break;
            }    
    }
	/**
	 * Returns the instance of Configuration currently in use. The name is a pun on the "this" concept. See also Singleton interface.
	 * @return Configuration
	 */
	static function That()
	{
		return $_SESSION['_NConfiguration'];
	}
}
?>