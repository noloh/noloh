<?php
/**
 * @package Web.UI.Controls
 */
class WebPage extends Component
{
	public $CSSFiles;
	public $AlternativePath;
	public $ReflectOS;
	public $Controls;
	public $Keywords;
	public $Description;
	private $Title;
	private $Width;
	private $Height;
	private $BackColor;
    private $Unload;

	//var $MetaInformation;
	//var $JSIframe;
	/**
	*ScrollLeft of the component
	*@var integer
	*/
	//public $ScrollLeft;
	/**
	*ScrollTop of the component
	*@var integer
	*/
	//public $ScrollTop;
	protected $LoadImg;
	protected $LoadLbl;
	
	function WebPage($title = 'Unititled Document', $keywords = '', $description = '')
	{
		parent::Component();
		$this->Width = $GLOBALS['_NWidth'];
		$this->Height = $GLOBALS['_NHeight'];
		$this->Controls = new ArrayList();
		$this->Controls->ParentId = $this->Id;
		$this->SetTitle($title);
		$this->Keywords = $keywords;
		$this->Description = $description;
		$this->ReflectOS = false;
		$this->CSSFiles = new ImplicitArrayList($this, 'AddCSSFile', 'RemoveCSSFileAt', 'ClearCSSFiles');
		$this->CSSFiles->Add(NOLOHConfig::GetNOLOHPath().'Web/UI/NStyles.css');
		
		$this->LoadImg = new Image(NOLOHConfig::GetNOLOHPath().'Images/noloh_ani_small.gif', 1, 1);
		$this->LoadImg->CSSClass = 'NLoad';
		$this->LoadImg->SetParentId($this->Id);
		$this->LoadLbl = new Label(' Loading...', 31, 4);
		$this->LoadLbl->SetParentId($this->Id);
		$this->LoadLbl->Opacity = 70;
		$this->LoadLbl->CSSClass = 'NLoad NLoadLbl';
		unset($_SESSION['_NPropertyQueue'][$this->LoadLbl->Id]['style.zIndex'],$_SESSION['_NPropertyQueue'][$this->LoadImg->Id]['style.zIndex']);
		//$this->LoadImg->ZIndex = $this->LoadLbl->ZIndex = null;
		//AddScript("document.getElementById('{$this->LoadLbl->Id}').style.zIndex=document.getElementById('{$this->LoadImg->Id}').style.zIndex=999999");
		//require_once($_SERVER['DOCUMENT_ROOT'] ."/NOLOH/Javascripts/GetBrowserAndOs.php");
		//require_once(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/GetBrowserAndOs.php");
		
		//SetOperatingSystem();
		//SetBrowser();
		SetGlobal('ReflectOS', $this->ReflectOS);
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
	
	function AddCSSFile($path)
	{
		$initialProperties = '\'id\',\''.hash('md5',$path).'\',\'rel\',\'stylesheet\',\'type\',\'text/css\',\'href\',\''.$path.'\'';
		NolohInternal::Show('LINK', $initialProperties, $this, 'NHead');
		$this->CSSFiles->Add($path, true, true);
	}
	
	function RemoveCSSFileAt($index)
	{
		if($index != -1)
		{
			$path = $this->CSSFiles[$index];
			$this->CSSFiles->RemoveAt($index, true);
			AddScript('_NRemStyle(\''.hash('md5',$path).'\',\''.NOLOHConfig::GetNOLOHPath().'\')');
		}
	}
	
	function ClearCSSFiles()
	{
		foreach($this->CSSFiles as $index => $path)
			$this->RemoveCSSFileAt($index);
		$this->CSSFiles->Clear(true);
	}
	
	function GetTitle()
	{
		return $this->Title;
	}
	
	function SetTitle($title)
	{
		$this->Title = $title;
		AddScript('document.title="'.addslashes($title).'"');
	}
	
	function GetWidth()
	{
		return $this->Width;
	}
	
	function SetWidth($width)
	{
		$this->Width = $width;
		QueueClientFunction($this, 'resizeTo', array($this->Width, $this->Height));
	}
	
	function GetHeight()
	{
		return $this->Height;
	}
	
	function SetHeight($height)
	{
		$this->Height = $height;
		QueueClientFunction($this, 'resizeTo', array($this->Width, $this->Height));
	}
	
	function GetBackColor()
	{
		return $this->BackColor;
	}
	
	function SetBackColor($backColor)
	{
		$this->BackColor = $backColor;
		QueueClientFunction($this, 'document.bgColor=\''.$backColor.'\';void', array(0));
	}
	
	function SetScrollLeft($scrollLeft)
	{
		QueueClientFunction($this, 'document.documentElement.scrollLeft='.$scrollLeft.';BodyScrollState', array());
	}
	
	function SetScrollTop($scrollTop)
	{
		QueueClientFunction($this, 'document.documentElement.scrollTop='.$scrollTop.';BodyScrollState', array());
	}

    function GetUnload()
    {
        return $this->GetEvent('Unload');
    }

    function SetUnload($unloadEvent)
    {
        $this->SetEvent($unloadEvent, 'Unload');
    }

    function GetEvent($eventType)
	{
		return $this->$eventType != null
			? $this->$eventType
			: new Event(array(), array(array($this->Id, $eventType)));
	}

	function SetEvent($eventObj, $eventType)
	{
		$this->$eventType = $eventObj;
		$pair = array($this->Id, $eventType);
		if($eventObj != null && !in_array($pair, $eventObj->Handles))
			$eventObj->Handles[] = $pair;
		$this->UpdateEvent($eventType);
	}

	function UpdateEvent($eventType)
	{
        QueueClientFunction($this, 'NOLOHChangeByObj',array('window','\''.Event::$Conversion[$eventType].'\'','\''.$this->GetEventString($eventType).'\''));
	}

	function GetEventString($eventType)
	{
		return $this->$eventType != null
			? $this->$eventType->GetEventString($eventType, $this->Id)
			: '';
	}
	
	static function That()
	{
		return GetComponentById($_SESSION['_NStartUpPageId']);
	}
	
	static function SkeletalShow($unsupportedURL)
	{
		// Unsupported Browser Handling
		/*
		$OS = GetOperatingSystem();
		$Browser = GetBrowser();
		if(($OS == "win" && ($Browser != "ie" && $Browser != "moz")) ||
		   ($OS == "mac" && ($Browser != "moz")))
		   		header("Location: ".(empty($this->AlternativePath)?
				"http://216.254.66.6/NOLOHBeta/Errors/UnsupportedBrowser.html":"$this->AlternativePath"));
		*/	
		if(defined('FORCE_GZIP'))
			ob_start('ob_gzhandler');
		print(
"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\">

<!-- Powered by NOLOH -->
<!--  www.noloh.com  -->

<HTML>
  <HEAD id='NHead'>
    <META HTTP-EQUIV='Pragma' CONTENT='no-cache'>
    <TITLE>Loading NOLOH Application...</TITLE>
    <NOSCRIPT><META http-equiv='refresh' content='0;url=".
				($unsupportedURL=='' ?
				'http://www.noloh.com/Errors/UnsupportedBrowser.html' : 
				$unsupportedURL).
  "'></NOSCRIPT>
  </HEAD>".(GetBrowser()=='ie'?"
  <BODY>
    <DIV id='N1'></DIV>
    <IFRAME id='NBackButton' style='display:none;' src='javascript:false;'></IFRAME>":"
  <BODY id='N1'>
  ")."
    <DIV id='NAWH' style='position:absolute; visibility:hidden;'></DIV>
  </BODY>
</HTML>

<SCRIPT type='text/javascript'>".($_SESSION['_NIE6'] ? 
  "
  function _NIe6InitIframeLoad()
  {
  	if (req.readyState==4)
  	{
	    var head = document.getElementById('NHead');
	    var script = document.createElement('SCRIPT');
	    script.type = 'text/javascript';
	    script.text = req.responseText;
	    head.appendChild(script);
  	}
  }
  
  req = new ActiveXObject('Microsoft.XMLHTTP');
  req.onreadystatechange = _NIe6InitIframeLoad;
  req.open('POST', (document.URL.indexOf('#/')==-1 ? document.URL.replace(location.hash,'')+(document.URL.indexOf('?')==-1?'?':'&') : document.URL.replace('#/',document.URL.indexOf('?')==-1?'?':'&')+'&')
               + 'NOLOHVisit=0&NWidth=' + document.documentElement.clientWidth + '&NHeight=' + document.documentElement.clientHeight, true);
  req.send('');
  
  "
  :
  "
  var head = document.getElementById('NHead');
  var script = document.createElement('SCRIPT');
  script.type = 'text/javascript';
  script.src = (document.URL.indexOf('#/')==-1 ? document.URL.replace(location.hash,'')+(document.URL.indexOf('?')==-1?'?':'&') : document.URL.replace('#/',document.URL.indexOf('?')==-1?'?':'&')+'&')
               + 'NOLOHVisit=0&NWidth=' + document.documentElement.clientWidth + '&NHeight=' + document.documentElement.clientHeight;
  head.appendChild(script);"
  )."
</SCRIPT>");
	}
	
	function Show()
	{
		parent::Show();
		AddNolohScriptSrc('ClientViewState.js', true);
		switch(GetBrowser())
		{
			case 'ie': case 'sa': 			AddNolohScriptSrc('FindPositionIESa.js'); break;
			case 'ff': 						AddNolohScriptSrc('FindPositionFF.js'); break;
			case 'op': 						AddNolohScriptSrc('FindPositionOp.js');
		}
		AddNolohScriptSrc('GeneralFunctions.js');
		if(!isset($_POST['NoSkeleton']) || GetBrowser()!='ie')
			AddScript('_NInit(\''.$this->LoadLbl->Id.'\',\''.$this->LoadImg->Id.'\')', Priority::High);
		AddScript('SaveControl(\''.$this->Id.'\')');
	}

	function SearchEngineShow($tokenLinks)
	{
		print('<HTML><HEAD><TITLE>'.$this->Title.'</TITLE>' .
			'<META name="keywords" content="' . (is_file($this->Keywords)?file_get_contents($this->Keywords):$this->Keywords) . '"></META>' .
			'<META name="description" content="' . (is_file($this->Description)?file_get_contents($this->Description):$this->Description) . 
			'"></META></HEAD><BODY>');
		foreach($this->Controls as $control)
			$control->SearchEngineShow();
		print(' ' . $tokenLinks . ' <A href="http://www.noloh.com">Powered by NOLOH</A></BODY></HTML>');
	}
	
	function GetAddId()
	{
		return $this->Id;
	}
}

?>