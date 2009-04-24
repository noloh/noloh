<?php
/**
 * Tab class
 *
 * A Tab is a an object that consists of three images, a left image, a center image, and a right iamge. 
 * This allows a Tab to Scale properly with it's Width and makes it perfect for situations where you need
 * Tab like object that scales with variable text. Tabs are usually used in conjuction with RolloverTabs,
 * but they can be used independently.
 * 
 * @package Controls/Auxiliary
 */
class Tab extends Panel
{
	/**
	 * A string to the source, or an Image object used for the left part of this Tab.
	 * @var Image 
	 */
	public $LeftImage;
	/**
	 * A string to the source, or an Image object used for the center part of this Tab.
	 * @var Image 
	 */
	public $MainImage;
	/**
	 * A string to the source, or an Image object used for the right part of this Tab.
	 * @var Image 
	 */
	public $RightImage;
	/**
	 * Constructor of Tab
	 * 
	 * @param mixed string|Image $leftImage A string to the source, or an Image object used for the left part of this Tab.
	 * @param mixed string|Image $mainImage A string to the source, or an Image object used for the center part of this Tab.
	 * @param mixed string|Image $rightImage A string to the source, or an Image object used for the right part of this Tab.
	 */
	function Tab($leftImage=null, $mainImage=null, $rightImage=null)
	{
		$width = 0;
		if($leftImage)
		{
			if($leftImage instanceof Image)
				$this->LeftImage = $leftImage;
			else
				$this->LeftImage = new Image($leftImage);
			$width += $this->LeftImage->GetWidth();
		}
		if($mainImage)
		{
			if($mainImage instanceof Image)
				$this->MainImage = $mainImage;
			else
				$this->MainImage = new Image($mainImage);
			$width += $this->MainImage->GetWidth();
		}
		if($rightImage)
		{
			if($rightImage instanceof Image)
				$this->RightImage = $rightImage;
			else
				$this->RightImage = new Image($rightImage);
			$width += $this->RightImage->GetWidth();
		}
		parent::Panel(0,0, $width, $this->MainImage->GetHeight());		
		$this->MainImage->Shifts[] = Shift::WidthWith($this);
		
		if($this->LeftImage)
			$this->Controls->Add($this->LeftImage);
		$this->Controls->Add($this->MainImage);
		if($this->RightImage)
		{
			$this->RightImage->Shifts[] = Shift::LeftWith($this, Shift::Width);
			$this->Controls->Add($this->RightImage);
		}
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