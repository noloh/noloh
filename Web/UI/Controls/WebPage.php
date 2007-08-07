<?php
/**
 * @package UI
 * @subpackage Controls
 */
class WebPage extends Component
{
	public $CSSFiles;
	public $AlternativePath;
	public $ReflectOS;
	public $Controls;
	private $Title;
	private $Width;
	private $Height;
	private $BackColor;
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
	
	function WebPage($title = 'Unititled Document')
	{
		parent::Component();
		if(isset($_GET['NWidth']))
		{
			$this->Width = $_GET['NWidth'];
			$this->Height = $_GET['NHeight'];
		}
		$this->Controls = new ArrayList();
		$this->Controls->ParentId = $this->Id;
		$this->SetTitle($title);
		$this->ReflectOS = false;
		$this->CSSFiles = new ImplicitArrayList($this, 'AddCSSFile', 'RemoveCSSFileAt', 'ClearCSSFiles');
		$this->CSSFiles->Add(NOLOHConfig::GetNOLOHPath().'Web/UI/NStyles.css');
		
		$this->LoadImg = new Image(NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/noloh_ani_small.gif', 1, 1);
		$this->LoadImg->CSSClass = 'NLoad';
		$this->LoadImg->SetParentId($this->Id);
		$this->LoadLbl = new Label(' Loading...', 31, 4);
		$this->LoadLbl->SetParentId($this->Id);
		$this->LoadLbl->Opacity = 70;
		$this->LoadLbl->CSSClass = 'NLoad NLoadLbl';
		unset($_SESSION['NOLOHPropertyQueue'][$this->LoadLbl->Id]['style.zIndex'],$_SESSION['NOLOHPropertyQueue'][$this->LoadImg->Id]['style.zIndex']);
		//$this->LoadImg->ZIndex = $this->LoadLbl->ZIndex = null;
		//AddScript("document.getElementById('{$this->LoadLbl->Id}').style.zIndex=document.getElementById('{$this->LoadImg->Id}').style.zIndex=999999");
		//require_once($_SERVER['DOCUMENT_ROOT'] ."/NOLOH/Javascripts/GetBrowserAndOs.php");
		//require_once(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/GetBrowserAndOs.php");
		
		//SetOperatingSystem();
		//SetBrowser();
		DeclareGlobal('ReflectOS', $this->ReflectOS);
		//$this->Controls->Add(new PostBackForm());
		/*
		//$this->JSIframe = new Iframe("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
		$this->JSIframe = new Iframe();
		//$this->JSIframe->ClientVisible = "NoDisplay";
		$this->JSIframe->Width = 0;
		$this->JSIframe->Height = 0;
		$this->Controls->Add($this->JSIframe); 
		*/
		
		//$this->LoadViewState();
	}
	
	function AddCSSFile($path)
	{
		$initialProperties = "'id','".hash('md5',$path)."','rel','stylesheet','type','text/css','href','$path'";
		NolohInternal::Show('LINK', $initialProperties, $this, 'NHead');
		$this->CSSFiles->Add($path, true, true);
	}
	
	function RemoveCSSFileAt($index)
	{
		if($index != -1)
		{
			$path = $this->CSSFiles[$index];
			$this->CSSFiles->RemoveAt($index, true);
			AddScript("_NRemStyle('".hash('md5',$path)."','".NOLOHConfig::GetNOLOHPath()."')");
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
		AddScript("document.title='$title'");
	}
	
	function GetWidth()
	{
		return $this->Width;
	}
	
	function GetHeight()
	{
		return $this->Height;
	}
	
	function GetBackColor()
	{
		return $this->BackColor;
	}
	
	function SetBackColor($newBackColor)
	{
		$this->BackColor = $newBackColor;
		QueueClientFunction($this, "document.bgColor='$newBackColor'; void", array(0));
	}
	
	function SetScrollLeft($scrollLeft)
	{
		//NolohInternal::SetProperty("scrollLeft", $scrollLeft, $this);
		QueueClientFunction($this, "document.documentElement.scrollLeft=$scrollLeft;void", array(0));
	}
	
	function SetScrollTop($scrollTop)
	{
		QueueClientFunction($this, "document.documentElement.scrollTop=$scrollTop;void", array(0));
		//NolohInternal::SetProperty("scrollTop", $scrollTop, $this);
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
    <IFRAME id='NBackButton' style='display:none;' src='javascript:false;'></IFRAME>
  </BODY>":"
  <BODY id='N1'>
  </BODY>")."
</HTML>

<SCRIPT type='text/javascript'>
  var head= document.getElementById('NHead');
  var script= document.createElement('SCRIPT');
  script.type = 'text/javascript';
  script.src = (document.URL.indexOf('#/')==-1 ? document.URL.replace(location.hash,'')+(document.URL.indexOf('?')==-1?'?':'&') : document.URL.replace('#/',document.URL.indexOf('?')==-1?'?':'&')+'&') 
               + 'NOLOHVisit=0&NWidth=' + document.documentElement.clientWidth + '&NHeight=' + document.documentElement.clientHeight;
  head.appendChild(script);
</SCRIPT>");
	}
	
	function Show()
	{
		parent::Show();
		//Alert($this->Width);
		/*if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie 6') !== false)
			AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/IE6clientviewstate.js");
		else*/
			//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/" . 
			//	(GetBrowser() == "ie" ? "IEclientviewstate.js" : "Mozillaclientviewstate.js"));
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/generalfunctions.js");
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/" . 
		//	(GetBrowser() == "ie" ? "IEShift.js" : "MozillaShift.js"));
		AddNolohScriptSrc('ClientViewState.js', true);
		AddNolohScriptSrc('GeneralFunctions.js');
		AddNolohScriptSrc('Shift.js', true);
		if(!isset($_POST['NoSkeleton']) || GetBrowser()!='ie')
			AddScript("_NInit('{$this->LoadLbl->Id}','{$this->LoadImg->Id}')", Priority::High);
		//elseif(!isset($_POST['NoSkeleton']))
		//	AddScript("if(!_NInit('{$this->LoadLbl->Id}','{$this->LoadImg->Id}')) {return;}", Priority::High);
		AddScript("SaveControl('$this->Id')");
		/*
		parent::Show();
		print(stripslashes("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\">\n<HTML>\n<!-- Powered by NOLOH Alpha3 -->\n<!--      www.noloh.com      -->\n<HEAD id='NHead'>\n  <META HTTP-EQUIV='Pragma' CONTENT='no-cache'>\n  <TITLE>$this->Title</TITLE>\n"));
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/" . 
			(GetBrowser() == "ie" ? "IEclientviewstate.js" : "Mozillaclientviewstate.js"));
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/generalfunctions.js");
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/" . 
			(GetBrowser() == "ie" ? "IEShift.js" : "MozillaShift.js"));
		$ScriptTags = "<SCRIPT type='text/javascript' src='".$_SERVER["PHP_SELF"].(GetBrowser()=="op"?"?":"")."'></SCRIPT>";
		if(isset($_SESSION['UnlockNOLOHDebug']) && $_SESSION['UnlockNOLOHDebug'] == 'mddevmddev')
		{
			print("  <SCRIPT type='text/javascript'>\n".$_SESSION["NOLOHSrcScript"]."\n  </SCRIPT>\n");
			$_SESSION['NOLOHSrcScript'] = "";
		}
		print("  <NOSCRIPT><META http-equiv='refresh' content='0;url=".(empty($this->AlternativePath)?
			"http://216.254.66.6/NOLOHBeta/Errors/UnsupportedBrowser.html":"$this->AlternativePath")."'></NOSCRIPT>\n</HEAD>\n<BODY ID='$this->Id'>\n");
		print("  <DIV id='NOLOHCSSFiles'></DIV>\n");
		AddScript("_NInit('{$this->LoadLbl->Id}','{$this->LoadImg->Id}')", Priority::High);
		AddScript("SaveControl('$this->Id')");
		print("</BODY>\n");
		if(isset($_SESSION['UnlockNOLOHDebug']) && $_SESSION['UnlockNOLOHDebug'] == 'mddevmddev')
		{
			NolohInternal::ShowQueue();
			NolohInternal::FunctionQueue();
			NolohInternal::SetPropertyQueue();
			print("<SCRIPT type='text/javascript'>".$_SESSION["NOLOHSrcScript"].$_SESSION['NOLOHScript'][0].$_SESSION['NOLOHScript'][1].$_SESSION['NOLOHScript'][2].";</SCRIPT>\n$ScriptTags\n");
			$_SESSION['NOLOHSrcScript'] = "";
			$_SESSION['NOLOHScript'] = array("", "", "");
		}
		print("</HTML>\n$ScriptTags\n");
		*/
	}
	
	function SearchEngineShow()
	{
		print($this->Title . ' ');
		foreach($this->Controls as $control)
			$control->SearchEngineShow();
	}
	
	function GetAddId()
	{
		return $this->Id;
	}
}

?>