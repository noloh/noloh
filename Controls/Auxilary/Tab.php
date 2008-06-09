<?php
/**
 * Tab class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Auxiliary
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
		$this->MainImage->Shifts[] = Shift::With($this, Shift::Width);
		$this->RightImage->Shifts[] = Shift::With($this, Shift::Left);
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