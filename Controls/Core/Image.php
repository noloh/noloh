<?php
/**
 * Image class
 *
 * A Control for an Image. An Image can either be used to diplay a graphic or be used as a custom button. It can also
 * be used to render your own images using PHP's image magic functions by calling Conjure. Conjure can be used, to give 
 * but one example, for rendering a captua.
 * 
 * Example 1: Instantiating and Adding an Image
 *
 * <pre>
 * function Foo()
 * {
 *    //Instatiates $tmpImage as a new Image, with the src of SomePicture.gif, and a left, 
 *    //and top of 10px.
 *    $tmpImage = new Image("Images/SomePicture.gif", 10, 10);
 *    $this->Controls->Add($tmpImage); //Adds a button to the Controls of some Container
 * }     	
 * </pre>
 * 
 * @package Controls/Core
 */
class Image extends Control 
{
	private $Src;
    private $Magician;
	private $IE6PNGFix;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Image
 	 * Example
 	 *	<pre> $tempVar = new Image("Images/NOLOHLogo.gif", 0, 10);</pre>
 	 * @param string $path
	 * @param integer $left
	 * @param integer $top
	 * @param integer $width
	 * @param integer $height
	 */
	function Image($path='', $left = 0, $top = 0, $width = System::Auto, $height = System::Auto)
	{
		parent::Control($left, $top, null, null);
		if(!empty($path))
			$this->SetPath($path);
		$this->SetWidth($width);
		$this->SetHeight($height);
	}
	/**
	 * Gets the path of the Image
	 * @return string
 	 */
	function GetPath()
	{
		return $this->Src;
	}
	/**
	 * Gets the path of the Image
	 * @return string
	 * @deprecated Use Path instead
	 */
	function GetSrc()	{return $this->GetPath();}
	/**
	 * Sets the path of the Image.
	 * The path is relative to your main file 
	 * @param string $path
	 * @param boolean $adjustSize
	 * @return string 
	 */
	function SetPath($path, $adjustSize=false)
	{
		//if(!is_file($newSrc))
		//	BloodyMurder('The Src ' . $newSrc . ' does not exist.');
		$this->Src = $path;
		if($this->Magician)
			$this->SetMagicianSrc();
		elseif(UserAgent::IsIE6())
		{
			if(preg_match('/\.png$/i', $path))
			{
				NolohInternal::SetProperty('style.filter', 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' . $path . '",sizingMethod="scale")', $this);
				//NolohInternal::SetProperty('style.display', 'inline-block', $this);
				if(!$this->IE6PNGFix)
					NolohInternal::SetProperty('src', System::ImagePath() . 'Blank.gif', $this);
				$this->IE6PNGFix = true;
			}
			else
			{
				if($this->IE6PNGFix)
					NolohInternal::SetProperty('style.filter', '', $this);
				NolohInternal::SetProperty('src', $path, $this);
				$this->IE6PNGFix = null;
			}
		}
		else
			NolohInternal::SetProperty('src', $path, $this);
        //NolohInternal::SetProperty('src', $this->Magician == null ? $newSrc : ($_SERVER['PHP_SELF'].'?NOLOHImage='.GetAbsolutePath($this->Src).'&Class='.$this->Magician[0].'&Function='.$this->Magician[1].'&Params='.implode(',', array_slice($this->Magician, 2))), $this);
		if($adjustSize)
		{
			$this->SetWidth(System::Auto);
			$this->SetHeight(System::Auto);
		}
		/*if (NOLOHConfig::NOLOHURL && preg_match('/^' . Server::ImagePath() . '(.*)$/', $newSrc, $matches)) 
		{
			$newSrc = NOLOHConfig::NOLOHURL . '/Images/' . $matches[1];
			NolohInternal::SetProperty('src', $newSrc, $this);
		}*/
		return $path;
	}
	/**
	 * Sets the path of the Image
	 * The path is relative to your main file 
	 * @param string $path
	 * @param boolean $adjustSize
	 * @return string 
	 * @deprecated Use Path instead
	 */
	function SetSrc($path, $adjustSize=false)	{return $this->SetPath($path, $adjustSize);}
	/**
	 * @ignore
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
	 * @ignore
	 */
	function SetWidth($width)
	{
		if($width !== null && !is_numeric($width))
		{
			if(substr($width, -1) != '%')
			{
				$imageSize = getimagesize(GetAbsolutePath($this->Src));
				if($width == System::Auto)
					$width = $imageSize[0];
				else
				{
					$width = intval($width)/100;
					$width = round($width * $imageSize[0]);
				}
			}
		}
		if($this->Magician != null)
			$this->SetMagicianSrc();
		parent::SetWidth($width);
	}
	/**
	 * @ignore
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
	 * @ignore
	 */
	function SetHeight($height)
	{
		if($height !== null && !is_numeric($height))
		{
			if(substr($height, -1) != '%')
			{
				$imageSize = getimagesize(GetAbsolutePath($this->Src));
				if($height == System::Auto)
					$height = $imageSize[1];
				else
				{
					$height = intval($height)/100;
					$height = round($height * $imageSize[1]);
				}
			}
		}
		if($this->Magician != null)
			$this->SetMagicianSrc();
		parent::SetHeight($height);
	}
	/**
	 * Conjure can be used to render your own images on the fly, e.g., for creating captuas. It lets you specify a callback function, which MUST
	 * be static, whose first parameter is the image resource, and subsequent parameters can be anything you define. One can then call PHP's image 
	 * magic functions on the image resource. Consider the following example:
	 * <pre>
	 * class Example
	 * {
	 *  function Example()
	 *  {
	 *   // Instantiate a new Image
	 *   $image = new Image('me.jpg');
	 *   // Conjure a magician for performing the image magic, passing in the parameters 255, 0, 0, which will correspond to red in our function
	 *   $image->Conjure('Example', 'FillImage', 255, 0, 0);
	 *  }
	 *  function FillImage($resource, $red, $green, $blue)
	 *  {
	 *   // Create a color using PHP's imagecollorallocate function
	 *   $col = imagecolorallocate($resource, $red, $green, $blue);
	 *   // Fill in the image with this color, using PHP's imagefill function
	 *   imagefill($resource, 5, 5, $col);
	 *  }
	 * }
	 * </pre>
	 * @param string $className
	 * @param string $functionName
	 * @param mixed,... $paramsAsDotDotDot
	 */
    function Conjure($className, $functionName, $paramsAsDotDotDot = null)
    {
		$this->Magician = func_get_args();
		$this->SetMagicianPath();
        //NolohInternal::SetProperty('src', $_SERVER['PHP_SELF'].'?NOLOHImage='.GetAbsolutePath($this->Src).'&Class='.$className.'&Function='.$functionName.'&Params='.implode(',', array_slice($this->Magician, 2)), $this);
        //$this->Magician = array($className, $functionName);
    }
	/**
	 * @ignore
	 */
	private function SetMagicianPath()
	{
		if($this->Src)
			NolohInternal::SetProperty('src', $_SERVER['PHP_SELF'].'?_NImage='.GetAbsolutePath($this->Src).'&_NClass='.$this->Magician[0].'&_NFunction='.$this->Magician[1].'&_NParams='.urlencode(implode(',', array_slice($this->Magician, 2))), $this);
		else
			NolohInternal::SetProperty('src', $_SERVER['PHP_SELF'].'?_NImage='.GetAbsolutePath($this->Src).'&_NClass='.$this->Magician[0].'&_NFunction='.$this->Magician[1].'&_NParams='.urlencode(implode(',', array_slice($this->Magician, 2))).'&_NWidth='.$this->GetWidth().'&_NHeight='.$this->GetHeight(), $this);
	}
	/**
	 * @ignore
	 */
	private function SetMagicianSrc()	{$this->SetMagicianPath();}
	/**
	 * @ignore
	 */
	function Show()
	{
		NolohInternal::Show('IMG', parent::Show(), $this);
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		echo '<IMG src="', $this->Src, '"', parent::SearchEngineShow(true);
		if($this->ToolTip===null && $this->Text===null)
			echo ' alt="',$this->ToolTip,' ',$this->Text,'"';
		echo '>';
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShow($indent);
		if($str !== false)
			echo $indent, '<IMG src="', $this->Src, '"', $this->ToolTip===null?'':(' alt="'.$this->ToolTip.'"'), $str?(' '.$str):'', ">\n";
	}
	/**
	 * @ignore 
	 */
	static function MagicGeneration($src, $class, $function, $params, $width=300, $height=200)
	{
		if($src != '')
		{
			$splitString = explode('.', $src);
			$extension = strtolower($splitString[count($splitString)-1]);
			if($extension == 'jpg')
				$extension = 'jpeg';
			elseif($extension == 'bmp')
				$extension = 'wbmp';
			//eval('if(imagetypes() & IMG_'.strtoupper($extension).')' .
			//	'$im = imagecreatefrom'.$extension.'($src);');
			if(imagetypes() & constant('IMG_'.strtoupper($extension)))
				$im = call_user_func('imagecreatefrom'.$extension, $src);
		}
		else
		{
			$extension = 'png';
			$im = imagecreatetruecolor($width, $height);
			$white = imagecolorallocate($im, 255, 255, 255);
			imagefill($im, 0, 0, $white);
		}
		if($im)
		{
			call_user_func_array(array($class, $function), array_merge(array($im), explode(',', urldecode($params))));
			header('Content-type: image/'.$extension);
			call_user_func('image'.$extension, $im);
			imagedestroy($im);
		}
	}
	/*
	 * ShiftColor can be used to dynamically rotate the colors of your image. This is useful for skinning objects.
	 * ShiftColor maintains all transparency and gradients. For instance, if you have a gradient image that has a blue base you can change
	 * that image to be based on whichever color you wish and it will maintain the look and feel of the image.
	 * 
	 * ShiftColor works with gif, png, and jpeg.
	 * <pre>
	 * class Example
	 * {
	 *  function Example()
	 *  {
	 *		// Instantiate a new Image
	 *		$image = new Image('titlebar.gif');
	 *	 	Image::ShiftColor($image, '#CC0000');
	 *  }
	 * }
	 * </pre>
	 * @param image|array $image The image or array of images that will have their colors rotated
	 * @param string $toColor The color that you would like the image to be based on.
	 * @param string $fromColor The color that you would like the rotation to start from. This is useful if you wish to preserve certain colors
	 * or have a different starting based. By default the color range begins from the darkest color in your image.
	 */
	/**
	 * @ignore
	 */
	static function ShiftColor($image, $toColor, $fromColor=null)
	{
		
	}
}
?>