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
	* The Orientation of the Tab
	* 
	* @var System::Horizontal|System::Vertical
	*/
	private $Orientation;
	/**
	 * Constructor of Tab
	 * 
	 * @param mixed string|Image $leftImage A string to the source, or an Image object used for the left part of this Tab.
	 * @param mixed string|Image $mainImage A string to the source, or an Image object used for the center part of this Tab.
	 * @param mixed string|Image $rightImage A string to the source, or an Image object used for the right part of this Tab.
	 */
	function Tab($leftImage=null, $mainImage=null, $rightImage=null, $orientation=System::Horizontal)
	{
		parent::Panel(0, 0, null, null);	
		if($leftImage)
		{
			$this->LeftImage = ($leftImage instanceof Image)?$leftImage:new Image($leftImage);
			$this->Controls->Add($this->LeftImage);
		}
		if($mainImage)
		{
			$this->MainImage = ($mainImage instanceof Image)?$mainImage:new Image($mainImage);
			$this->Controls->Add($this->MainImage);
		}
		if($rightImage)
		{
			$this->RightImage = ($rightImage instanceof Image)?$rightImage:new Image($rightImage);
			$this->Controls->Add($this->RightImage);
		}	
		$this->SetOrientation($orientation);
	}
	/**
	* Sets the Orientation of the Tab for scaling
	* 
	* @param System::Horizontal|System::Vertical $orientation
	*/
	function SetOrientation($orientation)
	{
		$changed = $orientation != $this->Orientation;
		$funcs = null;
		$magnitude = 0;
		if($orientation == System::Horizontal)
		{
			if($this->LeftImage)
				$magnitude += $this->LeftImage->GetWidth();
			if($this->MainImage)
			{
				$magnitude += $this->MainImage->GetWidth();
				$this->MainImage->Shifts['tab'] = Shift::WidthWith($this);
			}
			if($this->RightImage)
			{
				$this->RightImage->Shifts['tab'] = Shift::LeftWith($this, Shift::Width);
				$magnitude += $this->RightImage->GetWidth();
			}
			$func = array('SetWidth', 'SetHeight');
			$other = $this->Controls[0]->GetHeight();
		}
		else
		{
			if($this->LeftImage)
				$magnitude += $this->LeftImage->GetHeight();
			if($this->MainImage)
			{
				$magnitude += $this->MainImage->GetHeight();
				$this->MainImage->Shifts['tab'] = Shift::HeightWith($this);
			}
			if($this->RightImage)
			{
				$magnitude += $this->RightImage->GetHeight();
				$this->RightImage->Shifts['tab'] = Shift::TopWith($this, Shift::Height);
			}
			$func = array('SetHeight', 'SetWidth');
			$other = $this->Controls[0]->GetWidth();
		}
		$this->Orientation = $orientation;
		if($changed)
		{
			$this->{$func[0]}($magnitude);
			$this->{$func[1]}($other);
		}
	}
	/**
	* Gets the Orientation of the Tab for scaling
	* @return System::Horizontal|System::Vertical
	*/
	function GetOrientation()	{return $this->Orientation;}
	/**
	 * @ignore
	 */
	function SetWidth($width)
	{
		parent::SetWidth($width);
		if($this->Orientation == System::Horizontal)
		{
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
		else
		{
			if($this->LeftImage)
				$this->LeftImage->SetWidth($width);
			if($this->MainImage)
			{
				$this->MainImage->Left = 0;
				$this->MainImage->SetWidth($width);
			}
			if($this->RightImage)
			{
				$this->RightImage->Left = 0;
				$this->RightImage->SetWidth($width);
			}
		}
	}
	/**
	 * @ignore
	 */
	function SetHeight($height)
	{
		parent::SetHeight($height);
		if($this->Orientation == System::Vertical)
		{
			$difference = 0;
			if($this->LeftImage)
				$difference += $this->LeftImage->GetHeight();
			if($this->RightImage)
				$difference += $this->RightImage->GetHeight();
			$this->MainImage->SetHeight($height - $difference);
			$top = $this->LeftImage?$this->LeftImage->GetBottom():0;
			$this->MainImage->SetTop($top);
			if($this->RightImage)
				$this->RightImage->SetTop($this->MainImage->GetBottom());
		}
		else
		{
			if($this->LeftImage)
				$this->LeftImage->SetHeight($height);
			if($this->MainImage)
			{
				$this->MainImage->Top = 0;
				$this->MainImage->SetHeight($height);
			}
			if($this->RightImage)
			{
				$this->RightImage->Top = 0;
				$this->RightImage->SetHeight($height);
			}
		}
	}
}
?>