<?php

class WebPage extends Component
{
	public $CSSFiles;
	public $AlternativePath;
	public $ReflectOS;
	public $Controls;
	private $Title;
	private $Width;
	private $Height;
	//var $MetaInformation;
	//var $JSIframe;
	/**
	*ScrollLeft of the component
	*@var integer
	*/
	public $ScrollLeft;
	/**
	*ScrollTop of the component
	*@var integer
	*/
	public $ScrollTop;
	protected $LoadImg;
	protected $LoadLbl;
	
	function WebPage($title = "Unititled Document")
	{
		parent::Component();
		if(isset($_GET["NWidth"]))
		{
			$this->Width = $_GET["NWidth"];
			$this->Height = $_GET["NHeight"];
		}
		$this->Controls = new ArrayList();
		$this->Controls->ParentId = $this->DistinctId;
		$this->SetTitle($title);
		$this->ReflectOS = false;
		$this->CSSFiles = new ImplicitArrayList($this, "AddCSSFile", "RemoveCSSFileAt", "ClearCSSFiles");
		$this->CSSFiles->Add(NOLOHConfig::GetNOLOHPath()."Web/UI/NStyles.css");
		
		$this->Controls->Add($this->LoadImg = new Image(NOLOHConfig::GetNOLOHPath()."Web/UI/Controls/Images/noloh_ani_small.gif", 1, 1));
		$this->LoadImg->CSSClass = "NLoad";
		$this->Controls->Add($this->LoadLbl = new Label(" Loading...", 31, 4));
		$this->LoadLbl->Opacity = 70;
		$this->LoadLbl->CSSClass = "NLoad NLoadLbl";
		unset($_SESSION['NOLOHPropertyQueue'][$this->LoadLbl->DistinctId]["style.zIndex"],$_SESSION['NOLOHPropertyQueue'][$this->LoadImg->DistinctId]["style.zIndex"]);
		//$this->LoadImg->ZIndex = $this->LoadLbl->ZIndex = null;
		//AddScript("document.getElementById('{$this->LoadLbl->DistinctId}').style.zIndex=document.getElementById('{$this->LoadImg->DistinctId}').style.zIndex=999999");
		//require_once($_SERVER['DOCUMENT_ROOT'] ."/NOLOH/Javascripts/GetBrowserAndOs.php");
		//require_once(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/GetBrowserAndOs.php");
		
		//SetOperatingSystem();
		//SetBrowser();
		DeclareGlobal("ReflectOS", $this->ReflectOS);
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
		$initialProperties = "'id','".hash("md5",$path)."','rel','stylesheet','type','text/css','href','$path'";
		NolohInternal::Show("LINK", $initialProperties, $this, "NHead");
		$this->CSSFiles->Add($path, true, true);
	}
	
	function RemoveCSSFileAt($index)
	{
		if($index != -1)
		{
			$path = $this->CSSFiles[$index];
			$this->CSSFiles->RemoveAt($index, true);
			AddScript("_NRemStyle('".hash("md5",$path)."','".NOLOHConfig::GetNOLOHPath()."')");
		}
	}
	
	function ClearCSSFiles()
	{
		foreach($this->CSSFiles as $index => $path)
			$this->RemoveCSSFileAt($index);
		$this->CSSFiles->Clear(false, true);
	}
	
	function GetTitle()
	{
		return $this->Title;
	}
	
	function SetTitle($newTitle)
	{
		$this->Title = $newTitle;
		AddScript("document.title='$newTitle'");
	}
	
	function GetWidth()
	{
		return $this->Width;
	}
	
	function GetHeight()
	{
		return $this->Height;
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
				($unsupportedURL=="" ?
				"http://216.254.66.6/NOLOHBeta/Errors/UnsupportedBrowser.html" : 
				$unsupportedURL).
  "'></NOSCRIPT>
  </HEAD>".(GetBrowser()=="ie"?"
  <BODY>
    <DIV id='N1'></DIV>
    <IFRAME id='NBackButton' style='display:none;'></IFRAME>
  </BODY>":"
  <BODY id='N1'>
  </BODY>")."
</HTML>

<SCRIPT type='text/javascript'>
  var head= document.getElementById('NHead');
  var script= document.createElement('SCRIPT');
  script.type = 'text/javascript';
  script.src = (document.URL.indexOf('#')==-1 ? document.URL+'?' : document.URL.replace('#','?')+'&') 
               + 'NWidth=' + document.documentElement.clientWidth + '&NHeight=' + document.documentElement.clientHeight;
  head.appendChild(script);
</SCRIPT>");
	}
	
	function Show()
	{
		parent::Show();
		
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie 6') !== false)
			AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/IE6clientviewstate.js");
		else
			AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/" . 
				(GetBrowser() == "ie" ? "IEclientviewstate.js" : "Mozillaclientviewstate.js"));
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/generalfunctions.js");
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/" . 
			(GetBrowser() == "ie" ? "IEShift.js" : "MozillaShift.js"));
		AddScript("_NInit('{$this->LoadLbl->DistinctId}','{$this->LoadImg->DistinctId}')", Priority::High);
		AddScript("SaveControl('$this->DistinctId')");
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
			"http://216.254.66.6/NOLOHBeta/Errors/UnsupportedBrowser.html":"$this->AlternativePath")."'></NOSCRIPT>\n</HEAD>\n<BODY ID='$this->DistinctId'>\n");
		print("  <DIV id='NOLOHCSSFiles'></DIV>\n");
		AddScript("_NInit('{$this->LoadLbl->DistinctId}','{$this->LoadImg->DistinctId}')", Priority::High);
		AddScript("SaveControl('$this->DistinctId')");
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
	
	function GetAddId()
	{
		return $this->DistinctId;
	}
}

?>