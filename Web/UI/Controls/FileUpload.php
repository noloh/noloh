<?php
/**
 * @package UI
 * @subpackage Controls
 */
class FileUpload extends Control
{
	public $File;

	function FileUpload($whatLeft = 0, $whatTop = 0, $whatWidth = 300, $whatHeight = 24)  
	{
		parent::Control($whatLeft, $whatTop, $whatWidth, $whatHeight);
		$this->SetCSSClass();
	}
	
	function SetCSSClass($cssClass=null)
	{
		parent::SetCSSClass("NFileUpload ".$cssClass);
	}
	/*
	function SetWidth($newWidth)
	{
		parent::SetWidth($newWidth);
		//NolohInternal::SetProperty("contentWindow.document.body.frm.NOLOHFile.style.width", $newWidth."px", $this);
	}
	
	function SetHeight($newHeight)
	{
		parent::SetHeight($newHeight);
		//NolohInternal::SetProperty("contentWindow.document.body.frm.NOLOHFile.style.height", $newHeight."px", $this);
	}
	*/
	function Show()
	{
		$initialProperties = parent::Show();
		$initialProperties .= ",'marginWidth',0,'marginHeight',0,'frameBorder',0,'scrolling','no','name','$this->Id','src','".$_SERVER['PHP_SELF']."?NOLOHFileUpload={$this->Id}&Width={$this->GetWidth()}&Height={$this->GetHeight()}'";
		NolohInternal::Show("IFRAME", $initialProperties, $this);
	}
	
	static function ShowInside($id, $width, $height)
	{
		print("
			<BODY onLoad='parent.ReadyBox(\"$id\");'>
				<FORM id='frm' action='".$_SERVER['PHP_SELF']."?NOLOHFileUpload=$id&Width=$width&Height=$height' method='post' enctype='multipart/form-data'>
			   		<INPUT name='NOLOHFile' type='file' style='" . (GetBrowser()=="ie"?"position:absolute; ":"") . "width:{$width}px; height:{$height}px;'></INPUT>
			  	</FORM>
			</BODY>
		");
	}
}

?>