<?php
/**
 * @package UI
 * @subpackage Controls
* Image class file.
*/
 
/**
 * Image class
 *
 * A Control for an Image
 *
 * Properties
 * - <b>Src</b>, string, 
 *   <br>Gets or Sets the image source file
 * 
 * You can use the Button as follows
 * <code>
 *
 *		function Foo()
 *		{
 *			$tempButton = new Image("Images/SomePicture.gif",10,10);
 * 			$this->Controls->Add($tempButton); //Adds a button to the Controls class of some Container
 *		}
 *		
 * </code>
 */
class Image extends Control 
{
	/**
	* Src, The source file of the image.
	* @var string
	*/
	private $Src;
	private $AltLoad;
	//private $Count = 1;
	
	//public $ToolTip;
	/**
	* Constructor.
	* for inherited components, be sure to call the parent constructor first
 	* so that the component properties and events are defined.
 	* Example
 	*	<code> $tempVar = new Image("Images/NOLOHLogo.gif", 0, 10);</code>
 	* @param string|optional
	* @param integer|optional
	* @param integer|optional
	* @param integer|optional //The Width of the Image is determined automatically if not explicitly set
	* @param integer|optional //The Height of the Image is determined automatically if not explicitly set
	*/
	function Image($whatSrc="", $whatLeft = 0, $whatTop = 0, $whatWidth = System::Auto, $whatHeight = System::Auto)  
	{
		parent::Control($whatLeft, $whatTop, null, null);
		if(!empty($whatSrc))
			$this->SetSrc($whatSrc);
		$this->SetWidth($whatWidth);
		$this->SetHeight($whatHeight);
	}
	/**
	* Gets the Src of the Image
	* <b>Note:</b>Can also be called as a property.
	*<code> $tempSrc = $this->Src;</code>
	* @return string|absolute path
 	*/
	function GetSrc()
	{
		return $this->Src;
	}
	/**
	*Sets the Src of the Image.
	*<b>Note:</b>Can also be set as a property.
	*<code>$this->Src = "Images/NewImage.gif";</code>
	*<b>!Important!</b> If Overriding, make sure to call parent::SetSrc($newSrc)
	*@param string|Src
	*@return string|Src
	*/
	function SetSrc($newSrc, $adjustSize=false)
	{
		$this->Src = $newSrc;
		$load = $this->GetLoad();
		if($load instanceof ServerEvent)
			NolohInternal::SetProperty("src", $_SERVER['PHP_SELF']."?NOLOHImage={$this->Src}&Class=" .
				get_class(GetComponentById($load->ObjsId))."&Function={$load->ExecuteFunction}", $this);
		else 
			NolohInternal::SetProperty("src", $newSrc, $this);
		if($adjustSize)
		{
			$this->SetWidth(System::Auto);
			$this->SetHeight(System::Auto);
		}
//		if(!is_numeric($this->Width) || !is_numeric($this->Height))
//		{
//			//$tempimagesize = getimagesize(GetAbsolutePath($this->Src));
//			//if($this->Width == null)
//			$this->SetWidth($this->Width);
//			//if($this->Height == null)
//			$this->SetHeight($this->Height);
//		}
		return $newSrc;
	}
	function GetWidth($unit="px")
	{
		if($unit == "%")
		{
			$tmpImageSize = getimagesize(GetAbsolutePath($this->Src));
			return parent::GetWidth()/$tmpImageSize[0] * 100;
		}
		else
			return parent::GetWidth();
	}
	function SetWidth($width)
	{
//		Alert($this->Count);
//		$this->Count +=1;
		$tmpWidth = $width;
		if(!is_numeric($tmpWidth))
		{
			$tmpImageSize = getimagesize(GetAbsolutePath($this->Src));
			if($tmpWidth == System::Auto)
				$tmpWidth = $tmpImageSize[0];
			else
			{
				$tmpWidth = intval($tmpWidth)/100;
				$tmpWidth = round($tmpWidth * $tmpImageSize[0]);
			}
		}
//		Alert($tmpWidth);
		parent::SetWidth($tmpWidth);
	}
	function GetHeight($unit="px")
	{
		if($unit == "%")
		{
			$tmpImageSize = getimagesize(GetAbsolutePath($this->Src));
			return parent::GetHeight()/$tmpImageSize[1] * 100;
		}
		else
			return parent::GetHeight();
	}
	function SetHeight($height)
	{
		$tmpHeight = $height;
		if(!is_numeric($tmpHeight))
		{
			$tmpImageSize = getimagesize(GetAbsolutePath($this->Src));
			if($tmpHeight == System::Auto)
				$tmpHeight = $tmpImageSize[1];
			else
			{
				$tmpHeight = intval($tmpHeight)/100;
				$tmpHeight = round($tmpHeight * $tmpImageSize[1]);
			}
		}
		parent::SetHeight($tmpHeight);
	}
	function SetLoad($newLoad)
	{
		$this->AltLoad = $newLoad;
		if($newLoad instanceof ServerEvent)
			NolohInternal::SetProperty("src", $_SERVER['PHP_SELF']."?NOLOHImage={$this->Src}&Class=" .
				(is_object($newLoad->Source)?get_class($newLoad->Source->Dereference()):$newLoad->Src)."&Function={$newLoad->ExecuteFunction}", $this);
	}
	function GetLoad()
	{
		return $this->AltLoad;
	}
	/**
	* @ignore
	*/
	function Show()
	{
		$initialProperties = parent::Show();
		NolohInternal::Show("IMG", $initialProperties, $this);
	}
	static function MagicGeneration($src, $class, $function)
	{
		$splitString = explode(".", $src);
		$extension = $splitString[count($splitString)-1];
		$imgtypes = imagetypes();
		if($extension == "jpg")
			$extension = "jpeg";
		if($extension == "bmp")
			$extension = "wbmp";
			
		eval('if(imagetypes() & IMG_'.strtoupper($extension).') {' .
			'$im = imagecreatefrom'.$extension.'($src);' .
			$class.'::'.$function.'($im);' .
			'header("Content-type: image/'.$extension.'");' . 
			'image'.$extension.'($im); }');
			
		imagedestroy($im);
	}
}
?>