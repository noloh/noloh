<?php
/**
* @package Web.UI.Controls
* Image class file.
*/

/**
 * Image class
 *
 * A Control for an Image. An image can either be used to diplay a graphic, or be used as a custom button.
 *
 * 
 * Example 1: Instantiating and Adding an Image
 *
 * <code>
 *function Foo()
 *{
 *    //Instatiates $tmpImage as a new Image, with the src of SomePicture.gif, and a left, 
 *    //and top of 10px.
 *    $tmpImage = new Image("Images/SomePicture.gif", 10, 10);
 *    $this->Controls->Add($tmpImage); //Adds a button to the Controls of some Container
 *}     	
 *</code>
 * 
 * @property string $Src The source file of this image
 * 
 * {@inheritdoc }
 */
class Image extends Control 
{
	/**
	* Src, The source file of the image.
	* @var string
	*/
	private $Src;
	private $AltLoad;
	
	/**
	* Constructor.
	* for inherited components, be sure to call the parent constructor first
 	* so that the component properties and events are defined.
 	* Example
 	*	<code> $tempVar = new Image("Images/NOLOHLogo.gif", 0, 10);</code>
 	* @param string[optional]
	* @param integer[optiona]
	* @param integer[optional]
	* @param integer[optional] //The Width of the Image is determined automatically if not explicitly set
	* @param integer[optional] //The Height of the Image is determined automatically if not explicitly set
	*/
	function Image($src='', $left = 0, $top = 0, $width = System::Auto, $height = System::Auto)  
	{
		parent::Control($left, $top, null, null);
		if(!empty($src))
			$this->SetSrc($src);
		$this->SetWidth($width);
		$this->SetHeight($height);
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
	*The path is relative to your main file 
	*<b>!Important!</b> If Overriding, make sure to call parent::SetSrc($newSrc)
	*@param string $Src
	*@return string|Src
	*/
	function SetSrc($newSrc, $adjustSize=false)
	{
		$this->Src = $newSrc;
		$load = $this->GetLoad();
		if($load instanceof ServerEvent)
			NolohInternal::SetProperty('src', $_SERVER['PHP_SELF']."?NOLOHImage={$this->Src}&Class=" .
				get_class(GetComponentById($load->ObjsId))."&Function={$load->ExecuteFunction}", $this);
		else 
			NolohInternal::SetProperty('src', $newSrc, $this);
		if($adjustSize)
		{
			$this->SetWidth(System::Auto);
			$this->SetHeight(System::Auto);
		}
		return $newSrc;
	}
	/**
	*Gets the Width of the Image.
	*<b>Note:</b>Can also get as a property.
	*<code>$tmpVar = $this->Width;</code>
	*@param string $unit[optional] //Units you would like the width in, either px, or "%".
	*@return mixed
	*/
	function GetWidth($unit='px')
	{
		if($unit == '%')
		{
			$tmpImageSize = getimagesize(GetAbsolutePath($this->Src));
			return parent::GetWidth()/$tmpImageSize[0] * 100;
		}
		else
			return parent::GetWidth();
	}
	/**
	*Sets the Width of the Image.
	*<b>Note:</b>Can also be set as a property.
	*<code>$this->Width = 200;</code>
	*<b>!Important!</b> If Overriding, make sure to call parent::SetWidth($newWidth)
	*@param integer $Width
	*/
	function SetWidth($width)
	{
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
		parent::SetWidth($tmpWidth);
	}
	/**
	*Gets the Width of the Image.
	*<b>Note:</b>Can also get as a property.
	*<code>$tmpVar = $this->Height;</code>
	*@param string $unit[optional|] //Units you would like the height in, either px, or "%".
	*@return mixed
	*/
	function GetHeight($unit='px')
	{
		if($unit == '%')
		{
			$tmpImageSize = getimagesize(GetAbsolutePath($this->Src));
			return parent::GetHeight()/$tmpImageSize[1] * 100;
		}
		else
			return parent::GetHeight();
	}
	/**
	*Sets the Height of the Image.
	*<b>Note:</b>Can also be set as a property. 
	*<code>$this->Height = 200;</code>
	*<b>!Important!</b> If Overriding, make sure to call parent::SetHeight($newHeight)
	*@param integer $height
	*/
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
	/**
	 * @ignore
	 */
	function SetLoad($newLoad)
	{
		$this->AltLoad = $newLoad;
		if($newLoad instanceof ServerEvent)
			NolohInternal::SetProperty('src', $_SERVER['PHP_SELF']."?NOLOHImage={$this->Src}&Class=" .
				(is_object($newLoad->Source)?get_class($newLoad->Source->Dereference()):$newLoad->Src)."&Function={$newLoad->ExecuteFunction}", $this);
	}
	/**
	 * @ignore
	 */
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
		NolohInternal::Show('IMG', $initialProperties, $this);
	}
	/**
	 *@ignore 
	*/
	static function MagicGeneration($src, $class, $function)
	{
		$splitString = explode('.', $src);
		$extension = $splitString[count($splitString)-1];
		$imgtypes = imagetypes();
		if($extension == 'jpg')
			$extension = 'jpeg';
		if($extension == 'bmp')
			$extension = 'wbmp';
			
		eval('if(imagetypes() & IMG_'.strtoupper($extension).') {' .
			'$im = imagecreatefrom'.$extension.'($src);' .
			$class.'::'.$function.'($im);' .
			'header("Content-type: image/'.$extension.'");' . 
			'image'.$extension.'($im); }');
			
		imagedestroy($im);
	}
}
?>