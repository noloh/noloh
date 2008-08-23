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
	/**
	 * NOLOH's loading Image that is displayed when a ServerEvent is taking place
	 * @var Image
	 */
	protected $LoadImg;
	/**
	 * NOLOH's loading Label that is displayed when a ServerEvent is taking place
	 * @var Label
	 */
	protected $LoadLbl;
	
	private $Title;
	private $Width;
	private $Height;
	private $BackColor;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends WebPage.
	 * @param string $title The WebPage's title, i.e., the text appearing in the browser's title bar across the top
	 * @param string $keywords Keywords can be used by search engines to help better archive your application, but are not necessary to take advantage of NOLOH's built-in SearchEngineFriendly capabilities.
	 * @param string $description Description can be used by search engines to help better archive your application, but are not necessary to take advantage of NOLOH's built-in SearchEngineFriendly capabilities.
	 * @return WebPage
	 */
	function WebPage($title = 'Unititled Document', $keywords = '', $description = '')
	{
		if($_SESSION['_NVisit'] === -1)
			throw new Exception($title, $GLOBALS['_NApp']);
		parent::Component();
		parent::Show();
		$_SESSION['_NStartUpPageId'] = $this->Id;
		$this->Width = $GLOBALS['_NWidth'];
		$this->Height = $GLOBALS['_NHeight'];
		$this->Controls = new ArrayList();
		$this->Controls->ParentId = $this->Id;
		$this->Title = $title;
		$this->Keywords = $keywords;
		$this->Description = $description;
//		$this->ReflectOS = false;
		$this->CSSFiles = new ImplicitArrayList($this, 'AddCSSFile', 'RemoveCSSFileAt', 'ClearCSSFiles');
		$this->CSSFiles->Add(NOLOHConfig::GetNOLOHPath().'Controls/NStyles.css');
		$this->LoadImg = new Image(NOLOHConfig::GetNOLOHPath().'Images/loading.gif', 1, 1, 30, 30);
		$this->LoadImg->CSSClass = 'NLoad';
		$this->LoadImg->SetParentId($this->Id);
		$this->LoadLbl = new Label('&nbsp;Loading...', 31, 7);
		$this->LoadLbl->SetParentId($this->Id);
		$this->LoadLbl->Opacity = 70;
		$this->LoadLbl->CSSClass = 'NLoad NLoadLbl';
		unset($_SESSION['_NPropertyQueue'][$this->LoadLbl->Id]['style.zIndex'],$_SESSION['_NPropertyQueue'][$this->LoadImg->Id]['style.zIndex']);
		$unload = parent::GetEvent('Unload');
		$unload['User'] = new ClientEvent('');
		$unload['System'] = new ServerEvent(null, 'isset', true);
		AddNolohScriptSrc('ClientViewState.js', true);
		switch(GetBrowser())
		{
			case 'ie': case 'sa': 			AddNolohScriptSrc('FindPositionIESa.js'); break;
			case 'ff': 						AddNolohScriptSrc('FindPositionFF.js'); break;
			case 'op': 						AddNolohScriptSrc('FindPositionOp.js');
		}
		AddNolohScriptSrc('GeneralFunctions.js');
		if(!isset($_POST['NoSkeleton']) || !UserAgent::IsIE())
			AddScript('_NInit(\''.$this->LoadLbl->Id.'\',\''.$this->LoadImg->Id.'\')', Priority::High);
		AddScript('SaveControl(\''.$this->Id.'\')');
		//$this->LoadImg->ZIndex = $this->LoadLbl->ZIndex = null;
		//AddScript("document.getElementById('{$this->LoadLbl->Id}').style.zIndex=document.getElementById('{$this->LoadImg->Id}').style.zIndex=999999");
		//require_once($_SERVER['DOCUMENT_ROOT'] ."/NOLOH/Javascripts/GetBrowserAndOs.php");
		//require_once(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/GetBrowserAndOs.php");
		
		//SetOperatingSystem();
		//SetBrowser();
//		SetGlobal('ReflectOS', $this->ReflectOS);
		//$this->Controls->Add(new PostBackForm());
		/*
		//$this->JSIframe = new Iframe("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
		$this->JSIframe = new Iframe();
		//$this->JSIframe->Visible = 0;
		$this->JSIframe->Width = 0;
		$this->JSIframe->Height = 0;
		$this->Controls->Add($this->JSIframe); 
		*/
		
		//$this->LoadViewState();
	}
	/**
	 * @ignore
	 */
	function AddCSSFile($path)
	{
		$initialProperties = '\'id\',\''.hash('md5',$path).'\',\'rel\',\'stylesheet\',\'type\',\'text/css\',\'href\',\''.$path.'\'';
		NolohInternal::Show('LINK', $initialProperties, $this, 'NHead');
		$this->CSSFiles->Add($path, true, true);
	}
	/**
	 * @ignore
	 */
	function RemoveCSSFileAt($index)
	{
		if($index != -1)
		{
			$path = $this->CSSFiles[$index];
			$this->CSSFiles->RemoveAt($index, true);
			AddScript('_NRemStyle(\''.hash('md5',$path).'\',\''.NOLOHConfig::GetNOLOHPath().'\')');
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
		QueueClientFunction($this, 'document.documentElement.scrollLeft='.$scrollLeft.';BodyScrollState', array());
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
		QueueClientFunction($this, 'document.documentElement.scrollTop='.$scrollTop.';BodyScrollState', array());
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
		QueueClientFunction($this, 'NOLOHChangeByObj',array('window','\''.Event::$Conversion[$eventType].'\'','\''.$this->GetEvent($eventType)->GetEventString($eventType, $this->Id).'\''));
	}
	/**
	 * Returns the instance of WebPage that was used with SetStartupPage. The name is a pun on the "this" concept.
	 * @return WebPage
	 */
	static function That()
	{
		return GetComponentById($_SESSION['_NStartUpPageId']);
	}
	/**
	 * @ignore
	 */
	static function SkeletalShow($title, $unsupportedURL)
	{
		if(defined('FORCE_GZIP'))
			ob_start('ob_gzhandler');
		echo 
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">

<!-- Powered by NOLOH -->
<!--  www.noloh.com  -->

<HTML>
  <HEAD id="NHead">
    <TITLE>', $title, '</TITLE>
    <NOSCRIPT><META http-equiv="refresh" content="0',
			$unsupportedURL === null ?
				//'http://www.noloh.com/Errors/UnsupportedBrowser.html' : 
				'' : 
				(';url='.$unsupportedURL.''),
  '"></NOSCRIPT>
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
  document.cookie = "NAppCookie=; expires=Thu, 1 Jan 1970, 00:00:00 UTC; path=/";', 
$_SESSION['_NIE6'] ? '
  function _NIe6InitIframeLoad()
  {
  	if (req.readyState==4)
  	{
	    var head = document.getElementById("NHead");
	    var script = document.createElement("SCRIPT");
	    script.type = "text/javascript";
	    script.text = req.responseText;
	    head.appendChild(script);
  	}
  }
  
  req = new ActiveXObject("Microsoft.XMLHTTP");
  req.onreadystatechange = _NIe6InitIframeLoad;
  req.open("POST", (document.URL.indexOf("#/")==-1 ? document.URL.replace(location.hash,"")+(document.URL.indexOf("?")==-1?"?":"&") : document.URL.replace("#/",document.URL.indexOf("?")==-1?"?":"&")+"&")
               + "NOLOHVisit=0&NApp=" + _NApp + "&NWidth=" + document.documentElement.clientWidth + "&NHeight=" + document.documentElement.clientHeight, true);
  req.send("");'
: '
  var head = document.getElementById("NHead");
  var script = document.createElement("SCRIPT");
  script.type = "text/javascript";
  script.src = (document.URL.indexOf("#/")==-1 ? document.URL.replace(location.hash,"")+(document.URL.indexOf("?")==-1?"?":"&") : document.URL.replace("#/",document.URL.indexOf("?")==-1?"?":"&")+"&")
               + "NOLOHVisit=0&NApp=" + _NApp + "&NWidth=" + document.documentElement.clientWidth + "&NHeight=" + document.documentElement.clientHeight;
  head.appendChild(script);', '
</SCRIPT>';
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow($tokenLinks)
	{
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"><HTML><HEAD><TITLE>', $this->Title, '</TITLE>',
			'<META name="keywords" content="', is_file($this->Keywords)?file_get_contents($this->Keywords):$this->Keywords, '"></META>',
			'<META name="description" content="', is_file($this->Description)?file_get_contents($this->Description):$this->Description,
			'"></META></HEAD><BODY lang="en">';
		foreach($this->Controls as $control)
			$control->SearchEngineShow();
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
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"><HTML><HEAD><TITLE>', $this->Title, '</TITLE></HEAD><BODY lang="en">';
		foreach($this->Controls as $control)
			$control->SearchEngineShow();
		echo '</BODY></HTML>';
	}
	/**
	 * @ignore
	 */
	function GetAddId()
	{
		return $this->Id;
	}
}

?>