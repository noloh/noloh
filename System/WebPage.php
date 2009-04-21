<?php
/**
 * WebPage class
 *
 * The WebPage class is the starting point of any NOLOH application. You must write a class that extends it and call the SetStartupPage function
 * with the name of your class as the parameter. 
 * @package System
 */
abstract class WebPage extends Component
{
	/**
	 * An ArrayList of the paths (as strings) of external stylesheets to be included with your application
	 * @var ArrayList
	 */
	public $CSSFiles;
	/**
	 * An ArrayList of Controls that will be displayed when Added
	 * @var ArrayList
	 */
	public $Controls;
	/**
	 * @ignore
	 */
	public $AlternativePath;
	/**
	 * @ignore
	 */
	public $Keywords;
	/**
	 * @ignore
	 */
	public $Description;
	/**
	 * @ignore
	 */
	public $DebugWindow;
	
	private $Title;
	private $Width;
	private $Height;
	private $BackColor;
	private $LoadIndicator;
	private $CSSPropertyArray;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends WebPage.
	 * @param string $title The WebPage's title, i.e., the text appearing in the browser's title bar across the top
	 * @param string $keywords Keywords can be used by search engines to help better archive your application, but are not necessary to take advantage of NOLOH's built-in SearchEngineFriendly capabilities.
	 * @param string $description Description can be used by search engines to help better archive your application, but are not necessary to take advantage of NOLOH's built-in SearchEngineFriendly capabilities.
	 * @param string $favIconPath A Path to an image to be used as the browser favicon
	 * @return WebPage
	 */
	function WebPage($title = 'Unititled Document', $keywords = '', $description = '', $favIconPath = null)
	{
		if($_SESSION['_NVisit'] === -1)
		{
			$GLOBALS['_NTitle'] = $title;
			$GLOBALS['_NFavIcon'] = $favIconPath;
			throw new Exception('Fatal cookie behavior.', $GLOBALS['_NApp']);
		}
		parent::Component();
		parent::Show();
		$_SESSION['_NStartUpPageId'] = $this->Id;
		$this->Width = isset($GLOBALS['_NWidth']) ? $GLOBALS['_NWidth'] : 1024;
		$this->Height = isset($GLOBALS['_NHeight']) ? $GLOBALS['_NHeight'] : 768;
		$this->Controls = new ArrayList();
		$this->Controls->ParentId = $this->Id;
		$this->Title = $title;
		$this->Keywords = $keywords;
		$this->Description = $description;
		$this->CSSFiles = new ImplicitArrayList($this, 'AddCSSFile', 'RemoveCSSFileAt', 'ClearCSSFiles');
		$this->CSSFiles->Add(System::AssetPath() .'/Controls/NStyles.css');
		$this->SetLoadIndicator($loadIndicator = new Label('Loading...', 7, 7, null, null));
		$loadIndicator->Opacity = 75;
		$loadIndicator->CSSClass = 'NLoadIndiLabel';
		unset($_SESSION['_NPropertyQueue'][$this->LoadIndicator->Id]['style.zIndex']);
		$unload = parent::GetEvent('Unload');
		$unload['User'] = new ClientEvent('');
		$unload['System'] = new ServerEvent(null, 'isset', true);
		AddNolohScriptSrc('GeneralFunctions.js');
		AddNolohScriptSrc('ClientViewState.js', true);
		switch(GetBrowser())
		{
			case 'ie': case 'sa': 			AddNolohScriptSrc('Mixed/FindPositionIESa.js'); break;
			case 'ff': 						AddNolohScriptSrc('Mixed/FindPositionFF.js'); break;
			case 'op': 						AddNolohScriptSrc('Mixed/FindPositionOp.js');
		}
		if(!isset($_POST['_NSkeletonless']) || !UserAgent::IsIE())
			AddScript('_NInit(\''.$this->LoadIndicator->Id.'\',' . ($GLOBALS['_NDebugMode']==='Full'?'"Full"':($GLOBALS['_NDebugMode']?'true':'false')) . ')', Priority::High);
		//AddScript('_NSaveControl(\''.$this->Id.'\')');
		$GLOBALS['_NFavIcon'] = $favIconPath;
	}
	/**
	 * @ignore
	 */
	function AddCSSFile($path)
	{
		$initialProperties = '\'rel\',\'stylesheet\',\'type\',\'text/css\',\'href\',\''.$path.'\'';
		NolohInternal::Show('LINK', $initialProperties, $this, 'NHead', hash('md5',$path));
		$this->CSSFiles->Add($path, true);
	}
	/**
	 * @ignore
	 */
	function RemoveCSSFileAt($index)
	{
		if($index !== -1)
		{
			$path = $this->CSSFiles[$index];
			$this->CSSFiles->RemoveAt($index, true);
			AddNolohScriptSrc('Style.js');
			AddScript('_NStyleRem(\''.hash('md5',$path).'\',\''.System::RelativePath().'\')');
		}
	}
	/**
	 * @ignore
	 */
	function ClearCSSFiles()
	{
		foreach($this->CSSFiles as $index => $path)
			$this->RemoveCSSFileAt($index);
		$this->CSSFiles->Clear(true);
	}
	/**
	 * Returns the WebPage's title, i.e., the text appearing in the browser's title bar across the top
	 * @return string
	 */
	function GetTitle()
	{
		return $this->Title;
	}
	/**
	 * Sets the WebPage's title, i.e., the text appearing in the browser's title bar across the top
	 * @param string $title
	 */
	function SetTitle($title)
	{
		if($this->Title !== $title)
		{
			$this->Title = $title;
			AddScript($_SESSION['_NIsIE']?('_NSetTitle("'.addslashes($title).'")'):('document.title="'.addslashes($title).'"'), Priority::High);
		}
	}
	/**
	 * Returns the horizontal size of the browser, in pixels
	 * @return integer
	 */
	function GetWidth()
	{
		return $this->Width;
	}
	/**
	 * Changes the horizontal size of the browser, in pixels
	 * @param integer $width
	 */
	function SetWidth($width)
	{
		$this->Width = $width;
		QueueClientFunction($this, 'resizeTo', array($this->Width, $this->Height));
	}
	/**
	 * Returns the vertical size of the browser, in pixels
	 * @return integer
	 */
	function GetHeight()
	{
		return $this->Height;
	}
	/**
	 * Changes the vertical size of the browser, in pixels
	 * @param integer $height
	 */
	function SetHeight($height)
	{
		$this->Height = $height;
		QueueClientFunction($this, 'resizeTo', array($this->Width, $this->Height));
	}
	/**
	 * Returns the background color of the WebPage. Can either be a string of hex like '#FF0000' or the name of a color like 'red'.
	 * @return string
	 */
	function GetBackColor()
	{
		return $this->BackColor;
	}
	/**
	 * Sets the background color of the WebPage. Can either be a string of hex like '#FF0000' or the name of a color like 'red'.
	 * @param string $backColor
	 */
	function SetBackColor($backColor)
	{
		$this->BackColor = $backColor;
		QueueClientFunction($this, 'document.bgColor=\''.$backColor.'\';void', array(0));
	}
	/**
	 * Returns the Control that NOLOH will display when a ServerEvent is launched as a visual indicator to the user.
	 * @return Control
	 */
	function GetLoadIndicator()
	{
		return $this->LoadIndicator;
	}
	/**
	 * Sets the Control that NOLOH will display when a ServerEvent is launched as a visual indicator to the user.
	 * @param Control $control
	 */
	function SetLoadIndicator($control)
	{
		if($this->LoadIndicator !== $control)
		{
			if($control == null)
				QueueClientFunction($this, '_NSetLoadIndi', array());
			elseif(!is_object($control))
				BloodyMurder('LoadIndicator must be an instance of Control. A ' . gettype($control) . ' was passed in instead.');
			elseif(!($control instanceof Control))
				BloodyMurder('LoadIndicator must be an instance of Control. A ' . get_class($control) . ' was passed in instead.');
			else
			{
				$control->ParentId = $this->Id;
				QueueClientFunction($this, '_NSetLoadIndi', array('"'.$control->Id.'"'), true, Priority::Low);
			}
			if($this->LoadIndicator)
				$this->LoadIndicator->ParentId = null;
			$this->LoadIndicator = $control;
		}
	}
	/**
	 * @ignore
	 */
	function CSSSwitch($browsers)
	{
		//Take in possible dotDotDotargs of items, or array of items, each of which has a name assoicated with a browser,
		//and value with a stylesheet
		//new Item('default', 'default.css', new Item('mac', 'mac.css'), etc..
	}
	/**
	 * @ignore
	 */
	function GetScrollLeft()
	{
		return 'document.documentElement.scrollLeft';
	}
	/**
	 * Changes the horizontal scroll position of the browser
	 * @param integer $scrollLeft
	 */
	function SetScrollLeft($scrollLeft)
	{
		$scrollLeft = $scrollLeft==Layout::Left?0: $scrollLeft==Layout::Right?9999: $scrollLeft;
		QueueClientFunction($this, 'document.documentElement.scrollLeft='.$scrollLeft.';_NBodyScrollState', array());
	}
	/**
	 * @ignore
	 */
	function GetScrollTop()
	{
		return 'document.documentElement.scrollTop';
	}
	/**
	 * Changes the vertical scroll position of the browser
	 * @param integer $scrollTop
	 */
	function SetScrollTop($scrollTop)
	{
		$scrollTop = $scrollTop==Layout::Top?0: $scrollTop==Layout::Bottom?9999: $scrollTop;
		QueueClientFunction($this, 'document.documentElement.scrollTop='.$scrollTop.';_NBodyScrollState', array());
	}
	/**
	 * Gets the Unload Event, which launches when someone navigates away from the application or closes their browser
	 * @return Event
	 */
    function GetUnload()
    {
		$unload = $this->GetEvent('Unload');
        return $unload['User'];
    }
	/**
	 * Sets the Unload event, which launches when someone navigates away from the application or closes teir browser
	 * @param Event $unloadEvent
	 */
    function SetUnload($unloadEvent)
    {
		$currentUnload = $this->GetEvent('Unload');
        $currentUnload['User'] = $unloadEvent;
    }
	/**
	 * @ignore
	 */
	function UpdateEvent($eventType)
	{
		$value = $this->GetEvent($eventType)->GetEventString($eventType, $this->Id);
		if($eventType === 'Tracker')
		{
			$value = preg_replace('/null/', 'location.href.indexOf("#/")==-1?location.href.replace(location.hash,""):location.href.replace("#/",location.href.indexOf("?")==-1?"?":"&")', $value, 1);
			QueueClientFunction($this, '_NChangeByObj', array('_N','\'Tracker\'','\''.$value.'\''), false);
			QueueClientFunction($this, 'eval', array('_N.Tracker'), true, Priority::Low);
		}
		else 
		{
			$property = Event::$Conversion[$eventType];
			QueueClientFunction($this, '_NChangeByObj', array('window','\''.$property.'\'','\''.$value.'\''), false);
		}
	}
	/**
	 * Returns the instance of WebPage that was used with SetStartupPage. The name is a pun on the "this" concept. See also Singleton interface.
	 * @return WebPage
	 */
	static function That()
	{
		return GetComponentById($_SESSION['_NStartUpPageId']);
	}
	/**
	 * @ignore
	 */
	static function SkeletalShow($title, $unsupportedURL, $favIcon)
	{
		header('Cache-Control: no-store');
		//header('Cache-Control: no-cache, must-revalidate, max-age=0');
		//header('Cache-Control: no-cache');
		//header('Pragma: no-cache');
		header('Content-Type: text/html');
		if(defined('FORCE_GZIP'))
			ob_start('ob_gzhandler');
		$symbol = empty($_GET) ? '?' : '&';
		$url = '(document.URL.indexOf("#/")==-1 ? document.URL.replace(location.hash,"")+"'.$symbol.'" : document.URL.replace("#/","'.$symbol.'")+"&")
               + "_NVisit=0&_NApp=" + _NApp + "&_NWidth=" + document.documentElement.clientWidth + "&_NHeight=" + document.documentElement.clientHeight';
		echo 
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">

<!-- Powered by NOLOH -->
<!--  www.noloh.com  -->

<HTML lang="en">
  <HEAD id="NHead">
    <TITLE>', $title, '</TITLE>
    <NOSCRIPT><META http-equiv="refresh" content="0',
			$unsupportedURL === null ?
				//'http://www.noloh.com/Errors/UnsupportedBrowser.html' : 
				'' : 
				(';url='.$unsupportedURL.''),
  '"></NOSCRIPT>', $favIcon?'
    <LINK rel="shortcut icon" href="'.$favIcon.'"></LINK>':'','
  </HEAD>',
UserAgent::IsIE() ? '
  <BODY>
    <DIV id="N1"></DIV>
    <IFRAME id="NBackButton" style="display:none;" src="javascript:false;"></IFRAME>'
: '
  <BODY id="N1">'
, '
    <DIV id="NAWH" style="position:absolute; visibility:hidden;"></DIV>
  </BODY>
</HTML>

<SCRIPT type="text/javascript">
  _NApp = ', $GLOBALS['_NApp'], ';
  document.cookie = "_NAppCookie=0;";', 
UserAgent::IsIE6() ? '
  function _NIe6Init()
  {
  	if(req.readyState == 4)
  	{
	    var script = document.createElement("SCRIPT");
	    script.type = "text/javascript";
	    script.text = req.responseText;
	    document.getElementById("NHead").appendChild(script);
  	}
  }
  
  req = new ActiveXObject("Microsoft.XMLHTTP");
  req.onreadystatechange = _NIe6Init;
  req.open("POST", ' . $url . ', true);
  req.send("");'
: '
  var script = document.createElement("SCRIPT");
  script.type = "text/javascript";
  script.src = ' . $url . ';
  document.getElementById("NHead").appendChild(script);', '
</SCRIPT>';
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow($tokenLinks)
	{
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"><HTML lang="en"><HEAD><TITLE>', $this->Title, '</TITLE>',
			'<META name="keywords" content="', is_file($this->Keywords)?file_get_contents($this->Keywords):$this->Keywords, '"></META>',
			'<META name="description" content="', is_file($this->Description)?file_get_contents($this->Description):$this->Description,
			'"></META></HEAD><BODY>';
		foreach($_SESSION['_NControlQueueRoot'] as $id => $show)
		{
			$obj = GetComponentById($id);
			if($show && $obj)
				$obj->SearchEngineShow();
		}
		echo ' <BR>', $tokenLinks, ' <A href="http://www.noloh.com">Powered by NOLOH</A></BODY></HTML>';
	}
	/**
	 * @ignore
	 */
	function NoScriptShow()
	{
		ob_end_clean();
		if(defined('FORCE_GZIP'));
			ob_start('ob_gzhandler');
		echo 
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">

<!-- Powered by NOLOH -->
<!--  www.noloh.com  -->

<HTML lang="en">
  <HEAD>
    <TITLE>', $this->Title, '</TITLE>';
	foreach($this->CSSFiles as $path)
		echo '
    <LINK rel="stylesheet" type="text/css" href="', $path, '"></LINK>';
	if($GLOBALS['_NFavIcon'])
		echo '
	<LINK rel="shortcut icon" href="' . $GLOBALS['_NFavIcon'] . '"></LINK>';
	echo '
  </HEAD>
  <BODY';
		if($this->BackColor)
			echo ' bgcolor="', $this->BackColor, '"';
		echo ">\n";
		foreach($_SESSION['_NControlQueueRoot'] as $id => $show)
		{
			$obj = GetComponentById($id);
			if($show && $obj)
				$obj->NoScriptShow('  ');
		}
		echo 
'  </BODY>
</HTML>';
		setcookie('_NAppCookie', false, 0, '/');
		ob_flush();
		if(isset($_SESSION['_NDataLinks']))
			foreach($_SESSION['_NDataLinks'] as $connection)
				$connection->Close();
		session_destroy();
	}
	/**
	 * @ignore
	 */
	function GetAddId()
	{
		return $this->Id;
	}
	/**
	 * @ignore
	 */
	function &__get($nm)
	{
		if(strpos($nm, 'CSS') === 0)
		{
			if($this->CSSPropertyArray == null)
				$this->CSSPropertyArray = array();
			$key = str_replace(array('_', 'CSS'), array('', ''), $nm);
			$key = strtolower($key[0]) . substr($key, 1);
			$ret = &$this->CSSPropertyArray[$key];
		}
		else 
			$ret = parent::__get($nm);
		return $ret;
	}
	/**
	 * @ignore
	 */
	function __set($nm, $val)
	{
		if(strpos($nm, 'CSS') === 0)
		{
			if($this->CSSPropertyArray == null)
				$this->CSSPropertyArray = array();
			$key = str_replace(array('_', 'CSS'), array('', ''), $nm);
			$key = strtolower($key[0]) . substr($key, 1);
			$this->CSSPropertyArray[$key] = $val;
			NolohInternal::SetProperty('style.'.$key, $val, $this);
			return $val;
		}
		else
			return parent::__set($nm, $val);
	}
}

?>