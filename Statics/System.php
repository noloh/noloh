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
	 * take up space. This is similar to false except that if either static or relative Layout is used, the
	 * control will not occupy space.
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
		$paths = explode(PATH_SEPARATOR, get_include_path());
		$funcArgs = func_get_args();
		$paths = array_merge($paths, $funcArgs);
	    set_include_path(implode(PATH_SEPARATOR, $paths));
	}
	/**
	 * @ignore
	 */
	static function LogFormat($what, $addQuotes=false)
	{
		if(($isArray = is_array($what)) || $what instanceof Iterator)
		{
			$text = ($isArray ? 'array' : get_class($what)) . '(';
			foreach($what as $key => $val)
				$text .= $key . ' => ' . self::LogFormat($val, true) . ', ';
			return rtrim($text,', ') . ')';
		}
		elseif(is_object($what))
			return (string)$what . ' ' . get_class($what) . ' object';
		elseif(!is_string($what) || $addQuotes)
			return ClientEvent::ClientFormat($what);
		return $what;
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
		$styled = '<' . $tag . ' class=\''.$class.'\'>'.$text.'</' . $tag . '>';
		if($newLine === true)
			$styled .= '<br/>';
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
		if(isset($_SESSION['_NUserDir']) && strpos($path, System::AssetPath())===0)
			return System::RelativePath() . substr($path, strlen(System::AssetPath()));
	
		if($path[0] == '\\' || $path[0] == '/')
			return realpath($_SERVER['DOCUMENT_ROOT'].$path);
		if(strpos($path, 'http://') >= 0)
			return $path;
		else
			return realpath($path);
	}
	/**
	 * Returns a relative path showing how one would traverse from one directory to another
	 * @param string $fromDirectory
	 * @param string $toDirectory
	 * @return string
	 */
	static function GetRelativePath($fromDirectory, $toDirectory)
	{
		$fromDirectory = rtrim($fromDirectory, '/');
		$toLength = strlen($toDirectory);
		$fromLength = strlen($fromDirectory);
		
		$length = min(array($toLength, $fromLength));
		$lastMatchingSlash = 0;
		
		for($i=0; ($i<$length && ($toDirectory[$i] === $fromDirectory[$i])) ; ++$i)
		{
			if($fromDirectory[$i] === '/')
				$lastMatchingSlash = $i;
		}
		if($i == $fromLength && $toLength > $fromLength && $toDirectory[$i] === '/')
		{
			$lastMatchingSlash = $i;
			$slashCount = 0;
		}
		else
			$slashCount = 1;
		for(; $i<$fromLength; ++$i)
			if($fromDirectory[$i] === '/')
				++$slashCount;
				
		return str_repeat('../', $slashCount) . substr($toDirectory, $lastMatchingSlash + 1);
	}
	/**
	 * System::Log will log one or more values to a debug window, along with a system timestamp. This function is useful for debugging. It will return the first parameter passed in.
	 * <pre>System::Log($someVar, 'Hey', 17, array('Yes', 'even', 'arrays!'));</pre>
	 * @param mixed,... $what The information to be logged, as an unlimited number of parameters
	 * @return mixed
	 */
	static function Log($what)
	{
		if($GLOBALS['_NDebugMode'])
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
				$display = $debugWindow->Controls['Display'] = new MarkupRegion('', 0, 0, null, null);
				//$display->CSSFontFamily = 'consolas, monospace';
				$old = false;
				$debugWindow->Buoyant = true;
			}
			$debugWindow->ParentId = $webPage->Id;
			$debugWindow->Visible = true;
			$stamp = date('h:i:s') . substr(microtime(), 1, 5);
			$display->Text .= ($old?'<BR>':'') . '<SPAN style="font-weight:bold; font-size: 8pt;">' . $stamp . '</SPAN>: ';
			if(($count = func_num_args()) > 1)
			{
				$display->Text .= '<UL>';
				$args = func_get_args();
				for($i=0; $i < $count; ++$i)
					$display->Text .= '<LI>' . self::LogFormat($args[$i]) . '</LI>';
				$display->Text .= '</UL>';
			}
			else 
				 $display->Text .= self::LogFormat($what);
			if(!isset($GLOBALS['_NDebugScrollAnim']))
			{
				Animate::ScrollTop($debugWindow->BodyPanel, Layout::Bottom);
				$GLOBALS['_NDebugScrollAnim'] = true;
			}
		}
		return $what;
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
	static function FullAppPath()			{return (!isset($_SERVER['HTTPS'])||$_SERVER['HTTPS']==='off'?'http://':'https://') . $_SERVER['HTTP_HOST'] . $_SESSION['_NURL'];}
}

?>