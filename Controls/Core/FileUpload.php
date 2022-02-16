<?php
/**
 * FileUpload class
 *
 * A FileUpload is a Control that enables a user to upload a file to the server.
 * 
 * <pre>
 * // Instantiate a new FileUpload and add it
 * $this->Controls->Add($fileUpload = new FileUpload());
 * // Instantiate a new Button and add it
 * $this->Controls->Add($button = new Button('Submit', 50);
 * // Say that clicking on the Button will upload the selected file to the server
 * $button->Click->Uploads->Add($fileUpload);
 * </pre>
 * 
 * @package Controls/Core
 */
class FileUpload extends Control
{
	/**
	 * After a file has been successfully uploaded, this property will contain a File object corresponding to the uploaded, temporary file. If it is not saved, it will be automatically deleted. 
	 * @var File
	 */
	public $File;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends FileUpload
	 * @param integer $left The left coordinate of this element
	 * @param integer $top The top coordinate of this element
	 * @param integer $width The width of this element
	 * @param integer $height The height of this element
	 * @return FileUpload
	 */
	function __construct($left = 0, $top = 0, $width = 300, $height = 24)  
	{
		parent::__construct($left, $top, $width, $height);
		$this->SetCSSClass();
	}
	/**
	 * @ignore
	 */
	function SetCSSClass($cssClass=null)
	{
		parent::SetCSSClass('NFileUpload'.$cssClass);
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
	/**
	 * @ignore
	 */
	function Show()
	{
		AddNolohScriptSrc('FileUpload.js', true);
		$initialProperties = parent::Show() . '\'marginWidth\',0,\'marginHeight\',0,\'frameBorder\',0,\'scrolling\',\'no\',\'name\',\''.$this->Id.'\',\'src\',\''.$_SERVER['PHP_SELF'].'?_NFileUpload='.$this->Id.'&_NApp='.$GLOBALS['_NApp'].'&_NWidth='.$this->GetWidth().'&_NHeight='.$this->GetHeight().'\'';
		NolohInternal::Show('IFRAME', $initialProperties, $this);
	}
	/**
	 * @ignore
	 */
	static function ShowInside($id, $width, $height)
	{
		echo '
			<BODY onLoad="parent._NRdyBox(\'',$id,'\');">
				<FORM id="frm" action="',$_SERVER['PHP_SELF'],'?_NFileUpload=',$id,'&_NApp=',$GLOBALS['_NApp'],'&_NWidth=',$width,'&_NHeight=',$height,'" method="post" enctype="multipart/form-data">
			   		<INPUT name="_NFile" type="file" style="', UserAgent::IsIE()?'position:absolute; ':'', 'width:',$width,'px; height:',$height,'px;"></INPUT>
			  	</FORM>
			</BODY>
		';
		if(isset($_FILES['_NFile']) && $_FILES['_NFile']['tmp_name']!='')
		{
			rename($_FILES['_NFile']['tmp_name'], $_FILES['_NFile']['tmp_name'].'N');
			$_SESSION['_NFiles'][$_GET['_NFileUpload']] = $_FILES['_NFile'];
			$_SESSION['_NFiles'][$_GET['_NFileUpload']]['tmp_name'] .= 'N';
		}
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShowIndent($indent);
		echo $indent, '<INPUT type="file" ', $str, "></INPUT>\n";
	}
}

?>