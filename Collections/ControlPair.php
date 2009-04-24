<?php
/**
 * ControlPair class
 * 
 * A ControlPair is a Container showing two Controls next to each other, either vertically or horizontally. 
 * 
 * @package Collections
 */
class ControlPair extends Panel
{
	/**
	 * Represents that the two Controls should be horizontally next to each other, i.e., side by side
	 */
	const Horizontal = 0;
	/**
	 * Represents that the two Controls should be vertically next to each other, i.e., top to bottom
	 */
	const Vertical = 1;
	
	private $Control1;
	private $Control2;
	private $Orientation;
	private $Margin;
	
	/*TODO
	Allow for setting of Pixel/Ratio, SetRatio()
	*/
	
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends ControlPair.
	 * @param mixed $firstControl If it is a string, the first Control will be a Label
	 * @param mixed $secondControl If it is null, the second Control will be a TextBox
	 * @param integer $left
	 * @param integer $top
	 * @param ControlPair::Horizontal|ControlPair::Vertical $orientation
	 * @param integer $margin
	 * @return ControlPair
	 */
	function ControlPair($firstControl, $secondControl=null, $left=0, $top=0, $orientation=ControlPair::Horizontal, $margin = 0)
	{
		parent::Panel($left, $top, null, null);
		if(is_string($firstControl))
			$firstControl = new Label($firstControl, 0, 0, System::Auto);
		$this->SetControl1($firstControl);
//		$this->Control1 = $firstControl;
		$this->SetMargin($margin);
		if($secondControl === null)
			$secondControl = new TextBox(0, 0);
		$this->SetControl2($secondControl);
//		$this->Control2 = $secondControl;
		$this->SetOrientation($orientation);
		
//		$this->SetLeft($left);
//		$this->SetTop($top);
	}
	/**
	 * Returns the first Control
	 * @return Control
	 */
	function GetControl1()	
	{
		return $this->Control1;
	}
	/**
	 * Sets the first Control
	 * @param Control $obj
	 */
	function SetControl1($obj)
	{
//		$left = $this->GetLeft();
		$obj->Layout = Layout::Relative;
		if($this->Control1)
			$this->Controls->Remove($this->Control1);
		$this->Control1 = $obj;
//		$this->SetLeft($left);
		$this->Controls->Add($this->Control1);
	}
	/**
	 * Returns the second Control
	 * @return Control
	 */
	function GetControl2()	
	{
		return $this->Control2;
	}
	/**
	 * Sets the second Control
	 * @param Control $obj
	 */
	function SetControl2($obj)
	{
		$obj->Layout = Layout::Relative;
		if($this->Control2)
			$this->Controls->Remove($this->Control2);
		$this->Control2 = $obj;
//		$this->SetLeft($this->GetLeft());
//		$this->SetTop($this->GetTop());
		$this->Controls->Add($this->Control2);
	}
	/**
	 * Returns the spacing between the two Controls
	 * @return integer
	 */
	function GetMargin()	
	{
		if(isset($this->Margin))
			if($this->Orientation == self::Horizontal)
				return $this->Margin->GetWidth();
			else
				return $this->Margin->GetHeight();
		return 0;
	}
	/**
	 * Sets the spacing between the two Controls
	 * @param integer $margin
	 */
	function SetMargin($margin)
	{
		if($this->GetMargin() !== $margin || !isset($this->Margin))
		{
			if(!isset($this->Margin))
			{
				$this->Margin = new Label('', 0, 0, null, null);
				$this->Margin->Layout = Layout::Relative;
				$this->Controls->Add($this->Margin);
			}
			if($this->Orientation == self::Horizontal)
			{
				$this->Margin->SetWidth($margin);
				$this->Margin->SetHeight(1);
			}
			else
			{
				$this->Margin->SetHeight($margin);
				$this->Margin->SetWidth('100%');
			}
		}
		return $margin;
	}
	/**
	 * @ignore
	 */
	/*function GetLeft()
	{
		return $this->Control1->GetLeft();
	}*/
	/**
	 * @ignore
	 */
	/*function SetLeft($left)
	{
		if($this->Layout == self::Horizontal)
		{
			$this->Control1->SetLeft($left + $this->Control1->GetLeft());
			$this->Control2->SetLeft($this->Control1->GetRight() + $this->Margin + $this->Control2->GetLeft());
		}
		else
		{
			$this->Control1->SetLeft($left + $this->Control1->GetLeft());
			$this->Control2->SetLeft($left + $this->Control2->GetLeft());
		}
	}*/
	/**
	 * @ignore
	 */
	/*function GetTop()
	{
		return $this->Control1->GetTop();
	}*/
	/**
	 * @ignore
	 */
	/*function SetTop($top)
	{
		if($this->Layout == self::Horizontal)
		{
			$this->Control1->SetTop($top + $this->Control1->GetTop());
			$this->Control2->SetTop($top + $this->Control2->GetTop());
		}
		else
		{
			$this->Control1->SetTop($top + $this->Control1->GetTop());
			$this->Control2->SetTop($this->Control1->GetBottom() + $this->Margin + $this->Control2->GetTop());
		}
	}*/
	/**
	 * @ignore
	 */
	function GetRight()
	{
		return $this->GetLeft() + $this->GetWidth();
	}
	/**
	 * @ignore
	 */
	function GetBottom()
	{
		return $this->GetTop() + $this->GetHeight();
	}
	/**
	 * @ignore
	 */
	function GetWidth()
	{
		if($this->Orientation == self::Horizontal)
			return $this->Control1->GetWidth() + $this->Control2->GetWidth() + $this->Margin;
		else
			if(($obj1Width = $this->Control1->GetWidth()) > ($obj2Width = $this->Control2->GetWidth()))
				return $obj1Width;
			else
				return $obj2Width;
	}
	/**
	 * @ignore
	 */
	function GetHeight()
	{
		if($this->Orientation == self::Vertical)
			return $this->Control1->GetHeight() + $this->Control2->GetHeight() + $this->Margin;
		else
			if(($obj1Height = $this->Control1->GetHeight()) > ($obj2Height = $this->Control2->GetHeight()))
				return $obj1Height;
			else
				return $obj2Height;
	}
	/**
	 * @ignore
	 */
	function GetOrientation()
	{
		return $this->Orientation;
	}
	/**
	 * @ignore
	 */
	function SetOrientation($orientation)
	{
		if($this->Orientation !== $orientation)
		{
			$original = $this->Orientation;
//			System::Log($orientation);
			$this->Margin->CSSFloat = 'left';
			if($orientation == self::Horizontal)
			{
				$this->Control1->CSSFloat = 'left';
				$this->Control2->CSSFloat = 'left';
				
			}
			else
			{
//				$this->Margin->CSSFloat = 'left';
				$this->Control1->CSSFloat = '';
				$this->Control2->CSSFloat = 'left';
			}
			$margin = $this->GetMargin();
			$this->Orientation = $orientation;
			$this->SetMargin($margin);
//			}
//			else
//				$this->Orientation = $orientation;
		}
		/*if($this->Layout != $layout)
		{
			$this->Layout = $layout;
			$this->SetTop($this->GetTop());
			$this->SetLeft($this->GetLeft());
		}*/	
		return $this->Orientation;
	}
	/**
	 * @ignore
	 */
	function GetValue()	{return $this->Control2->GetText();}
	/**
	 * @ignore
	 */
	function SetValue($value)	{$this->Control2->SetText($value);}
	/**
	 * @ignore
	 */
	function GetText()	{return $this->Control1->GetText();}
	/**
	 * @ignore
	 */
	function SetText($text)	{$this->Control1->SetText($text);}
	/**
	 * @ignore
	 */
	/*function SetCSSClass($className)
	{
		$this->Control1->SetCSSClass($className);
	}*/
}
?>