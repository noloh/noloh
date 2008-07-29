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
	
	function Tab($leftImageSrc=null, $mainImageSrc, $rightImageSrc=null)
	{
		$width = 0;
		if($leftImageSrc)
		{
			if($leftImageSrc instanceof Image)
				$this->LeftImage = $leftImageSrc;
			else
				$this->LeftImage = new Image($leftImageSrc);
			$width += $this->LeftImage->GetWidth();
		}
		if($mainImageSrc)
		{
			if($mainImageSrc instanceof Image)
				$this->MainImage = $mainImageSrc;
			else
				$this->MainImage = new Image($mainImageSrc);
			$width += $this->MainImage->GetWidth();
		}
		if($rightImageSrc)
		{
			if($rightImageSrc instanceof Image)
				$this->RightImage = $rightImageSrc;
			else
				$this->RightImage = new Image($rightImageSrc);
			$width += $this->RightImage->GetWidth();
		}
		parent::Panel(0,0, $width, $this->MainImage->GetHeight());		
		$this->MainImage->Shifts[] = Shift::With($this, Shift::Width);
		$this->RightImage->Shifts[] = Shift::With($this, Shift::Left);
		$this->Controls->AddRange($this->LeftImage, $this->MainImage, $this->RightImage);	
	}
	/**
	 * @ignore
	 */
	function SetWidth($width)
	{
		parent::SetWidth($width);	
		$difference = 0;
		if($this->LeftImage)
			$difference += $this->LeftImage->GetWidth();
		if($this->RightImage)
			$difference += $this->RightImage->GetWidth();
		$this->MainImage->SetWidth($width - $difference);
		$left = $this->LeftImage?$this->LeftImage->GetRight():0;
		$this->MainImage->SetLeft($left);
		if($this->RightImage)
			$this->RightImage->SetLeft($this->MainImage->GetRight());
	}
	/**
	 * @ignore
	 */
	function SetHeight($height)
	{
		parent::SetHeight($height);
		if($this->LeftImage)
			$this->LeftImage->SetHeight($height);
		$this->MainImage->SetHeight($height);
		if($this->RightImage)
			$this->RightImage->SetHeight($height);
	}
}
?>