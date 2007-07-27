<?php
/**
 * @package UI
 * @subpackage Controls
 */
class Tab extends Panel
{
	public $LeftImage;
	public $MainImage;
	public $RightImage;
	
	function Tab($leftImageSrc="", $mainImageSrc="", $rightImageSrc="")
	{
		$this->LeftImage = new Image($leftImageSrc);
		$this->MainImage = new Image($mainImageSrc);
		$this->RightImage = new Image($rightImageSrc);
		parent::Panel(0,0,$this->LeftImage->Width + $this->MainImage->Width + $this->RightImage->Width, $this->LeftImage->Height);
		
//		$this->SetWidth($this->LeftImage->Width + $this->MainImage->Width + $this->RightImage->Width);
//		$this->SetHeight($this->LeftImage->Height);
		$this->Controls->AddRange($this->LeftImage, $this->MainImage, $this->RightImage);	
	}	
	function SetWidth($whatWidth)
	{
		parent::SetWidth($whatWidth);	
		$this->MainImage->Width = $whatWidth - $this->LeftImage->Width - $this->RightImage->Width;
		$this->MainImage->Left = $this->LeftImage->Right;
		$this->RightImage->Left = $this->MainImage->Right;
	}
	function SetHeight($whatHeight)
	{
		parent::SetHeight($whatHeight);
		$this->LeftImage->Height = $whatHeight;
		$this->MainImage->Height = $whatHeight;
		$this->RightImage->Height = $whatHeight;
	}
}
?>