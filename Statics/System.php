<?php
/**
 * System class
 *
 * The System class contains various constants used by various parts of NOLOH, especially constants having to do with the
 * physical properties of controls such as size. The System class can also act as a source of many miscellaneous static functions within NOLOH.
 * 
 * @package Statics
 */
final class System
{
	private static $BenchmarkStartTime = null;
	/**
	 * @ignore
	 */
	private function System(){}
	/**
	 * System::Auto is used to indicate that various properties should figured out their values automatically.
	 * For example:
	 * <pre>
	 * // Creates a label with automatic width and height
	 * $lbl = new Label("This is my string", 0, 0, System::Auto, System::Auto);
	 * // Will Alert the actual width, in pixels, after performing a calculation 
	 * // based on the string and font size
	 * Alert($lbl->Width);
	 * </pre>
	 */
	const Auto = 'Auto';
	/**
	 * System::AutoHtmlTrim is used to indicate that various properties should figured out their values automatically
	 * and any HTML in them should be trimmed out.
	 * For example:
	 * <pre>
	 * // Creates a label with automatic width and height and HTML Trimming
	 * $lbl = new Label("<b>This is my string</b>", 0, 0, System::AutoHtmlTrim, System::AutoHtmlTrim);
	 * // Will Alert the actual width, in pixels, after performing a calculation based on 
	 * // the string and font size, while not considering the bold tags as part of the string.
	 * Alert($lbl->Width);
	 * </pre>
	 */
	const AutoHtmlTrim = 'HtmlTrim';
	/**
	 * System::Full is used to indicate that various properties should expand to accomodate the full control.
	 * For example:
	 * <pre>
	 * // Instantiate a new Panel
	 * $pnl = new Panel();
	 * // Tells the Panel to not cut off its contents, hence, the panel's width and height will be largely ignored.
	 * $pnl->Scrolling = System::Full;
	 * </pre>
	 */
	const Full = 'Full';
	/**
	 * System::Vacuous is used in connection with {@link Control::SetVisible()} to indicate that the control will not
	 * take up space. This is now the same as false, but distinct from System::Cloak;
	 * <pre>
	 * // Add a new Button
	 * $this->Controls->Add($btn1 = new Button());
	 * // Add another new Button
	 * $this->Controls->Add($btn2 = new Button());
	 * // Position them both statically
	 * $btn1->Layout = $btn2->Layout = 2;
	 * // Make the first button vacuous
	 * $btn1->Visible = System::Vacuous;
	 * // Now $btn2 will be on the left side of the screen, not to the right of an invisible object
	 * </pre>
	 */
	const Vacuous = null;
	/**
	* System::Cloak is used in connection with {@link Control::SetVisible()} to indicate that the control will 
	* take up space. This is similar to false except that if either static or relative Layout is used, the
	* Control will actually occupy space.
	*/
	const Cloak = 'Cloak';
	/**
	 * System::Unhandled is used in connection with {@link SetStartUpPage} as the fifth, $debugMode, parameter to 
	 * indicate that NOLOH's error handling will be disabled and regular crashing behavior will occur in case of an error.
	 */
	const Unhandled = 'Unhandled';
	/**
	 * System::Horizontal is used in connection with the Scrolling property to indicate the presence of Horizontal scrollbars.
	 */
	const Horizontal = 'horizontal';
	/**
	 * System::Horizontal is used in connection with the Scrolling property to indicate the presence of Horizontal scrollbars.
	 */
	const Vertical = 'vertical';
	/**
	* @ignore
	* Possible Alternatives: Perpetual
	*/
	const Continuous = 2;
	/**
	 * @ignore
	 */
	const Kernel = 'Kernel';
	/**
	 * Adds one (or more) directories containing the php files that define your application class(es).
	 * For example, if you have a class Foo, defined in Components/Foo.php, then you need only call System::IncludePaths('Components') before you can use the Foo class, without needing to write your own include/require statements. Note, however, that you must follow this class and filename naming convention to use this method.
	 * NOLOH is smart enough to include these files on-demand only when they need to be used, and does NOT include every possible file in the beginning.
	 * For paths that are not absolute, they are assumed to be relative to your working directory (where the first php script that is requested by the browser is, typically the one that has your start-up WebPage class)
	 * @param string,... $pathsAsDotDotDot An unlimited number of parameters specifying paths to directories
	 */
	static function IncludePaths($pathsAsDotDotDot)
	{
		$separator = constant('PATH_SEPARATOR');
		$paths = explode($separator, get_include_path());
		$funcArgs = func_get_args();
		foreach ($funcArgs as $val)
		{
			$path = realpath($val);
			if ($path)
			{
				$paths[] = $path;
			}
		}
		set_include_path(implode($separator, $paths));
	}
	/**
	 * @ignore
	 */
	static function LogFormat($what, $addQuotes=false, $tier=0)
	{
		if(($isArray = is_array($what)) || $what instanceof Iterator)
		{
			$what = self::FormatArray($what, 'Array', $tier);
		}
		elseif (is_object($what))
		{
			if ($what instanceof stdClass)
			{
				$array = get_object_vars($what);
				return self::FormatArray($array, 'StdClass', $tier);
			}
			else
			{
				return (string) $what . ' ' . get_class($what) . ' object';
			}
		}
		elseif(!is_string($what) || $addQuotes)
			return ClientEvent::ClientFormat($what);
		return $what;
	}
	/**
	 * @return string
	 */
	static function FormatArray($what, $type, $tier)
	{
		$indent = '    ';
		$spacer = str_repeat($indent, $tier);
		$text = $type . "\n$spacer(\n";
		foreach ($what as $key => $val)
		{
			$text .= $indent . $spacer . $key . ' => ' . self::LogFormat($val, true, $tier + 1) . "\n";
		}
		return rtrim($text, ', ') . "$spacer)";
	}
	/**
	 * Styles a string of text by giving it a CSS class
	 * @param string $text The string to be styled
	 * @param string $class The name of the CSS class
	 * @return string
	 */
	static function Style($text, $class, $newLine = false)
	{
		$tag = is_string($newLine)?$newLine:'span';
		$styled = '';
		if($newLine === true)
			$styled .= '<br>';
		$styled .= '<' . $tag . ' class=\''.$class.'\'>'.$text.'</' . $tag . '>';
		return $styled;
	}
	/**
	 * Alert a string specified by the $msg variable.
	 * <pre>System::Alert("Hi, my name is Asher!");</pre>
	 * @param string $msg Message to be Alerted
	 * @return string
	 */
	static function Alert($msg)
	{
		AddScript('alert("' . str_replace(array('\\',"\n","\r",'"'),array('\\\\','\n','\r','\"'),$msg) . '")');
		return $msg;
	}
	/**
	 * Gets the absolute path of a localized path
	 * @param string $path
	 * @return string
	 */
	static function GetAbsolutePath($path)
	{
		if (isset($_SESSION['_NUserDir']) && strpos($path, System::AssetPath())===0)
			return System::RelativePath() . substr($path, strlen(System::AssetPath()));
	
		if ($path[0] == '\\' || $path[0] == '/')
			return realpath($_SERVER['DOCUMENT_ROOT'].$path);
		if (strpos($path, URL::GetProtocol() . '://') >= 0)
			return $path;
		else
			return realpath($path);
	}
	/**
	 * Returns a relative path showing how one would traverse from one directory to another
	 * @param string $fromDirectory
	 * @param string $toDirectory
	 * @param System::Auto|string $slash
	 * @return string
	 */
	static function GetRelativePath($fromDirectory, $toDirectory, $slash = System::Auto)
	{
		if($slash === self::Auto)
			$slash = strpos($GLOBALS['_NPath'], '/') === false ? '\\' : '/';
		$repSlash = $slash === '/' ? '\\' : '/';
		$fromDirectory = str_replace($repSlash, $slash, $fromDirectory);
		$toDirectory = str_replace($repSlash, $slash, $toDirectory);
		
		$fromDirectory = rtrim($fromDirectory, $slash);
		//HACK! Will add / to toDirectory if toDirectory is not empty, or / - Asher
		if(strlen($toDirectory) > 1)
			$toDirectory = rtrim($toDirectory, $slash) . $slash;
		$toLength = strlen($toDirectory);
		$fromLength = strlen($fromDirectory);
		
		$length = min($toLength, $fromLength);
		$lastMatchingSlash = 0;
		
		for($i=0; $i<$length && ($toDirectory[$i] === $fromDirectory[$i]) ; ++$i)
		{
			if($fromDirectory[$i] === $slash)
				$lastMatchingSlash = $i;
		}
		if($i === $fromLength && $toLength > $fromLength && $toDirectory[$i] === $slash)
		{
			$lastMatchingSlash = $i;
			$slashCount = 0;
		}
		else
			$slashCount = 1;
		for(++$i; $i<$fromLength; ++$i)
			if($fromDirectory[$i] === $slash)
			{
				++$slashCount;
				++$i;
			}
				
		return str_repeat('..'.$slash, $slashCount) . substr($toDirectory, $lastMatchingSlash + 1);
	}
	/**
	 * System::Log will log one or more values to a debug window, along with a system timestamp. This function is useful for debugging. It will return the first parameter passed in.
	 * <pre>System::Log($someVar, 'Hey', 17, array('Yes', 'even', 'arrays!'));</pre>
	 * @param mixed,... $what The information to be logged, as an unlimited number of parameters
	 * @return mixed
	 */
	static function Log($what)
	{
//		if($GLOBALS['_NDebugMode'])
		if(Configuration::That()->DebugMode !== false)
		{
			$stamp = date('h:i:s') . substr(microtime(), 1, 5);
			if(UserAgent::IsCLI())
			{
				$endLine = constant('PHP_EOL');
				$output = $stamp . ': ';
				if(($count = func_num_args()) > 1)
				{
					$output .= $endLine;
					$args = func_get_args();
					for($i=0; $i < $count; ++$i)
						$output .= ' * ' . self::LogFormat($args[$i]) . $endLine;
				}
				else 
					 $output .= self::LogFormat($what) . $endLine;
				echo $output;
				file_put_contents('emailtest.txt', $output);
			}
			else
			{
				$webPage = WebPage::That();
				$debugWindow = $webPage->DebugWindow;
				if($debugWindow)
				{
					$display = $debugWindow->Controls['Display'];
					$old = true;
				}
				else
				{
					$debugWindow = $webPage->DebugWindow = new WindowPanel('Debug', 500, 0, 400, 300);
					$display = $debugWindow->Controls['Display'] = new MarkupRegion('', 0, 0, '100%', '100%');
					//$display->CSSFontFamily = 'consolas, monospace';
					$old = false;
					$debugWindow->Buoyant = true;
				}
				$debugWindow->ParentId = $webPage->Id;
				$debugWindow->Visible = true;
				
				$display->Text .= ($old?'<BR>':'') . '<SPAN style="font-weight:bold; font-size: 8pt;">' . $stamp . '</SPAN>: ';
				
				if(($count = func_num_args()) > 1)
				{
					$display->Text .= '<UL>';
					$args = func_get_args();
					for($i=0; $i < $count; ++$i)
						$display->Text .= '<LI><PRE>' .  htmlspecialchars(self::LogFormat($args[$i])) . '</PRE></LI>';
					$display->Text .= '</UL>';
				}
				else 
					 $display->Text .= '<PRE>' . htmlspecialchars(self::LogFormat($what)) . '</PRE>';
				if(!isset($GLOBALS['_NDebugScrollAnim']))
				{
					Animate::ScrollTop($debugWindow->BodyPanel, Layout::Bottom);
					$GLOBALS['_NDebugScrollAnim'] = true;
				}
			}
		}
		return $what;
	}
	/**
	* System::Modal will Add and Modal a Control. Useful for when wanting to 
	* focus on a particular Control. Clicking on the outside of the Modal will 
	* close it. If no Container is specified, Modal will add to the WebPage.
	*
	* <code>
	* //Basic Usage
	* $first = new Panel(0, 0, 400, 400);
	* $first->BackColor = Color::Red;
	* System::Modal($first);
	* </code>
	* Alternatively you can Modal in a specified Panel
	* <code>
	* $this->Controls->Add($first = new Panel(0, 0, 600, 600))
	* 	->BackColor = Color::Red;
	* $second = new Panel(0, 0, 400, 400);
	* $second->BackColor = Color::Green;
	* System::Modal($second, $first);
	* </code>
	* 
	* @param Control $obj The Control you want to Modal
	* @param Panel $container An alternative Panel you wish to Modal in
	* @param mixed $backColor The BackColor of your Modal
	* @param mixed $duration The Duration your Modal opens/closes
	* @param mixed $opacity The Opacity of your Modal's background layer
	* @return Panel
	*/
	static function Modal($obj, $container=null, $backColor='#999999', $duration=500, $opacity=80)
	{
		$modal = new Panel(0, 0, '100%', '100%');
		$modal->Scrolling = System::Full;
		$backLabel = new Label('', null, null, '100%', '100%');
		$backLabel->ParentId = $modal->Id;
		$backLabel->BackColor = $backColor;
		$backLabel->Opacity = 0;
		$backLabel->SendToBack();
		Animate::Opacity($backLabel, $opacity, $duration);
		
		$close = new ServerEvent('Animate', 'Opacity', $modal, Animate::Oblivion, $duration);
		$obj->Leave[] = $close;
		$backLabel->Click = $close;
		$modal->Click->Liquid = true;
		$modal->DataValue = $backLabel;
		$modal->Controls->Add($obj);
		ClientScript::AddNOLOHSource('Layout.js');
		ClientScript::Queue($obj, 'HAlign', array($obj));
		ClientScript::Queue($obj, 'VAlign', array($obj));
		
		if($container && $container->HasProperty('Controls'))
			$container->Controls->Add($modal);
		else
		{
			$backLabel->Layout = Layout::Fixed;
			WebPage::That()->Controls->Add($modal);
		}
		return $modal;
	}
	function IsRESTful()
	{
		return isset($GLOBALS['_NREST']);
	}
	/**
	 * Produces an HTTP error with a specified status code and optional redirect.
	 * @param integer $statusCode
	 * @param string $urlRedirect
	 */
	static function HTTPError($statusCode = 410, $urlRedirect = null)
	{
		if(UserAgent::IsSpider() || !$urlRedirect)
		{
			$lookUp = array(
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Timeout',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request',
				415 => 'Unsupported Media Type',
				416 => 'Requested Range Not Satisfiable',
				417 => 'Expectation Failed',
				422 => 'Unprocessable Entity',
				423 => 'Locked',
				424 => 'Failed Dependency',
				425 => 'Unordered Collection',
				426 => 'Upgrade Required',
				449 => 'Retry With',
				450 => 'Blocked by Windows Parental Controls',
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Timeout',
				505 => 'HTTP Version Not Supported',
				506 => 'Variant Also Negotiates',
				507 => 'Insufficient Storage',
				509 => 'Bandwidth Limit Exceeded',
				510 => 'Not Extended'
			);
			header('HTTP/1.1 ' . $statusCode . ' ' . $lookUp[$statusCode], true, $statusCode);
			exit();
		}
		else
			//header('Location: ' . $urlRedirect);
			ClientScript::Add('location="'.$urlRedirect.'";');
	}
	/**
 	 * Returns the full system path to NOLOH
 	 * @return string
 	 */
	static function GetNOLOHPath()			{return self::NOLOHPath();}
	/**
 	 * Returns the relative system path to NOLOH
 	 * @return string
 	 */
	static function GetNOLOHRelativePath()	{return self::RelativePath();}
	
	/**
 	 * @ignore
 	 */
	static function NOLOHPath()				{return $_SESSION['_NPath'];}
	/**
 	 * @ignore
 	 */
	static function RelativePath()			{return $_SESSION['_NRPath'];}
	/**
	 * @ignore
	 */
	static function AssetPath()				{return $_SESSION['_NRAPath'];}
	/**
	 * @ignore
	 */
	static function ImagePath()				{return self::AssetPath() . '/Images/';}
	/**
	 * @ignore
	 */
	static function FullAppPath()			{return URL::GetProtocol() . '://' . $_SERVER['HTTP_HOST'] . $_SESSION['_NURL'];}

	/**
	 * Allows one to clock the time operation(s) took. Call this before before beginning those operations.
	 */
	static function BeginBenchmarking()
	{
		self::$BenchmarkStartTime = microtime(true);
	}
	/**
	 * Returns the number of milliseconds that transpired since the call to BeginBenchmarking. 
	 * @return int
	 */
	static function Benchmark()
	{
		$stop = microtime(true);
		return (int)(1000 * ($stop - (self::$BenchmarkStartTime)));
	}
	/**
	 * Returns true if server operating system is Windows.
	 * @return bool
	 */
	static function IsWindows()
	{
		return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
	}
	/**
	 * Executes a shell command and returns the output of the command
	 * @param string $shellCommand The command to be run
	 * @param array $successCodes Defaults to an array with the code 0, but allows for custom success
	 * codes to be passed in
	 * @return string $output The output of the shell command
	 * @throws Exception
	 */
	static function Execute($shellCommand, $successCodes = array(0))
	{
		exec($shellCommand . ' 2>&1', $output, $returnCode);
		$output = implode(PHP_EOL, $output);

		if (!in_array($returnCode, $successCodes))
		{
			throw new Exception($output, $returnCode);
		}

		return $output;
	}
}

?>