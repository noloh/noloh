<?php
/**
 * @package Web.UI.Controls
 */
class Tab extends Panel
{
	public $LeftImage;
	public $MainImage;
	public $RightImage;
	
	function Tab($leftImageSrc='', $mainImageSrc='', $rightImageSrc='')
	{
		$this->LeftImage = new Image($leftImageSrc);
		$this->MainImage = new Image($mainImageSrc);
		$this->RightImage = new Image($rightImageSrc);
		parent::Panel(0,0,$this->LeftImage->Width + $this->MainImage->Width + $this->RightImage->Width, $this->LeftImage->Height);		
		$this->Controls->AddRange($this->LeftImage, $this->MainImage, $this->RightImage);	
	}	
	function SetWidth($width)
	{
		parent::SetWidth($width);	
		$this->MainImage->Width = $width - $this->LeftImage->Width - $this->RightImage->Width;
		$this->MainImage->Left = $this->LeftImage->Right;
		$this->RightImage->Left = $this->MainImage->Right;
	}
	function SetHeight($height)
	{
		parent::SetHeight($height);
		$this->LeftImage->Height = $height;
		$this->MainImage->Height = $height;
		$this->RightImage->Height = $height;
	}
}
?>