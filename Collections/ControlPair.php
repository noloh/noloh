<?php
/**
 * ControlPair class
 *
 * A ControlPair is a Container showing two Controls next to each other, either vertically or horizontally.
 *
 * @package Collections
 */
class ControlPair extends Panel implements ArrayAccess
{
	/**
	 * Represents that the two Controls should be horizontally next to each other, i.e., side by side
	 * @deprecated Use Layout::Horizontal instead
	 */
	const Horizontal = 0;
	/**
	 * Represents that the two Controls should be vertically next to each other, i.e., top to bottom
	 * @deprecated Use Layout::Vertical instead
	 */
	const Vertical = 1;

	private $First;
	private $Second;
	private $Orientation;
	private $Margin;

	/*TODO
	Allow for setting of Pixel/Ratio, SetRatio()
	*/

	/**
	 * Constructor.
	 * If a string is passed as $firstControl, it will instantiate a new Label for you, and if
	 * null is passed as $secondControl, it will instantiate a new TextBox for you. Otherwise,
	 * if Controls are passed in, it will simply use the parameters.
	 * Be sure to call this from the constructor of any class that extends ControlPair.
	 * @param mixed $firstControl If it is a string, the first Control will be a Label
	 * @param mixed $secondControl If it is null, the second Control will be a TextBox
	 * @param integer $left
	 * @param integer $top
	 * @param Layout::Horizontal|Layout::Vertical $orientation
	 * @param integer $margin
	 * @return ControlPair
	 */
	function __construct($firstControl, $secondControl=null, $left=0, $top=0, $orientation=Layout::Horizontal, $margin = 0)
	{
		parent::__construct($left, $top, null, null);
		if(!is_object($firstControl))
			$firstControl = new Label($firstControl, 0, 0, null, null);
		$this->SetFirst($firstControl);
		$this->SetMargin($margin);
		if($secondControl === null)
			$secondControl = new TextBox(0, 0);
		elseif(is_string($secondControl))
			$secondControl = new $secondControl(0, 0);
		$this->SetSecond($secondControl);
		$this->SetOrientation($orientation);
	}
	/**
	 * Returns the First Control
	 * @return Control
	 */
	function GetFirst()
	{
		return $this->First;
	}
	/**
	 * Sets the First Control
	 * @param Control $obj
	 * @return Control
	 */
	function SetFirst($obj)
	{
		$previous = $this->First;
		if($obj->HasProperty('Layout'))
			$obj->Layout = Layout::Relative;
			
		$this->First = $obj;
		if($previous)
		{
			$this->Controls->Remove($previous);
			$this->Controls->Insert($obj, 0);
			$orientation = $this->Orientation;
			$this->Orientation = null;
			$this->SetOrientation($orientation);
		}
		else
			$this->Controls->Add($obj);
		return $obj; 

	}
	/**
	 * Returns the Second Control
	 * @return Control
	 */
	function GetSecond()	{return $this->Second;}
	/**
	 * Sets the Second Control
	 * @param Control $obj
	 * @return Control
	 */
	function SetSecond($obj)
	{
		$isSet = isset($this->Second);
		if($obj->HasProperty('Layout'))
			$obj->Layout = Layout::Relative;
		if($this->Second)
			$this->Controls->Remove($this->Second);
		$this->Controls->Add($obj);
		$this->Second = $obj;
		if($isSet)
		{
			$orientation = $this->Orientation;
			$this->Orientation = null;
			$this->SetOrientation($orientation);
		}
		return $obj;
	}
	/**
	 * Returns the spacing between the two Controls
	 * @return integer
	 */
	function GetMargin()
	{
		if(isset($this->Margin))
			if($this->Orientation == Layout::Horizontal)
				return $this->Margin->GetWidth();
			else
				return $this->Margin->GetHeight();
		return 0;
	}
	/**
	 * Sets the spacing between the two Controls
	 * @param integer $margin
	 * @return integer
	 */
/*	function SetMargin($margin)
	{
		$notSet = !isset($this->Margin);
		if($this->GetMargin() !== $margin || $notSet)
		{
//			System::Log($margin);
			if($notSet)
			{
				$this->Margin = new Label('', 0, 0, $margin, $margin);
				$this->Margin->Layout = Layout::Relative;
				$this->Controls->Add($this->Margin);
			}
			if($this->Orientation == Layout::Vertical)
				$this->OrganizeMarginVer($margin);
			else
				$this->OrganizeMarginHor($margin);
		}
		return $margin;
	}*/
	function SetMargin($margin)
	{
		$isSet = isset($this->Margin);
		if($this->GetMargin() !== $margin || !$isSet)
		{
			if($isSet)
				if($this->Orientation == Layout::Horizontal)
					$this->OrganizeMarginHor($margin);
				else
					$this->OrganizeMarginVer($margin);
			else
			{
				$this->Margin = new Label('', 0, 0, $margin, $margin);
				$this->Margin->Layout = Layout::Relative;
				$this->Controls->Add($this->Margin);
			}
		}
		return $margin;
	}
	private function OrganizeMarginHor($width)
	{
		$this->Margin->SetWidth($width);
		$this->Margin->SetHeight(1);
	}
	private function OrganizeMarginVer($height)
	{
		$this->Margin->SetHeight($height);
		$this->Margin->SetWidth('100%');
	}
	/**
	 * Returns how the two Controls will appear next to each other
	 * @return Layout::Horizontal|Layout::Vertical
	 */
	function GetOrientation()
	{
		return $this->Orientation;
	}
	/**
	 * Sets how the two Controls will appear next to each other
	 * @param Layout::Horizontal|Layout::Vertical $orientation
	 */
	function SetOrientation($orientation)
	{
		if($this->Orientation !== $orientation)
		{
//			$original = $this->Orientation;
			$this->Margin->CSSFloat = 'left';
			if($orientation == Layout::Horizontal)
			{
				$this->First->CSSFloat = 'left';
				$this->Second->CSSFloat = 'left';
				$this->OrganizeMarginHor($this->Margin->GetHeight());
				$this->Margin->CSSClear = 'none';
			}
			else
			{
				$this->Margin->CSSClear = 'right';
				$this->Margin->CSSFloat = '';
				$this->First->CSSFloat = '';
				$this->First->CSSClear = 'left';
				$this->Second->CSSFloat = '';
				$this->OrganizeMarginVer($this->Margin->GetWidth());
			}
			$this->Orientation = $orientation;
		}
		return $orientation;
	}
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
		if($this->Orientation == Layout::Horizontal)
			return $this->First->GetWidth() + $this->Second->GetWidth() + $this->Margin->GetWidth();
		else
			if(($obj1Width = $this->First->GetWidth()) > ($obj2Width = $this->Second->GetWidth()))
				return $obj1Width;
			else
				return $obj2Width;
	}
	/**
	 * @ignore
	 */
	function GetHeight()
	{
		if($this->Orientation == Layout::Vertical)
			return $this->First->GetHeight() + $this->Second->GetHeight() + $this->Margin->GetHeight();
		else
			if(($obj1Height = $this->First->GetHeight()) > ($obj2Height = $this->Second->GetHeight()))
				return $obj1Height;
			else
				return $obj2Height;
	}
	/**
	 * Returns the Value property of the Second Control
	 * @return string
	 */
	function GetValue()	{return $this->Second->GetValue();}
	/**
	 * Sets the Value property of the Second Control
	 * @param string $value
	 */
	function SetValue($value)	{$this->Second->SetValue($value);}
	/**
	 * Returns the Text property of the First Control
	 * @return string
	 */
	function GetText()	{return $this->First->GetText();}
	/**
	 * Sets the Text property of the First Control
	 * @param string $text
	 */
	function SetText($text)	{$this->First->SetText($text);}
	/**
	 * Returns the First Control
	 * @return Control
	 * @deprecated Use GetFirst() instead
	 */
	function GetControl1()
	{
		return $this->GetFirst();
	}
	/**
	 * Sets the First Control
	 * @param Control $obj
	 * @deprecated Use SetFirst() instead
	 */
	function SetControl1($obj)
	{
		return $this->SetFirst($obj);
	}
	/**
	 * Returns the Second Control
	 * @return Control
	 * @deprecated Use GetSecond() instead
	 */
	function GetControl2()
	{
		return $this->GetSecond();
	}
	/**
	 * Sets the Second Control
	 * @param Control $obj
	 * @deprecated Use SetSecond() instead
	 */
	function SetControl2($obj)
	{
		return $this->SetSecond($obj);
	}
	/**
	 * @ignore
	 */
	function offsetExists($key)
	{
		return ($key ? $this->Second : $this->First) != null;
	}
	/**
	 * @ignore
	 */
	function offsetGet($index)
	{
		return key ? $this->GetSecond() : $this->GetFirst();
	}
	/**
	 * @ignore
	 */
	function offsetSet($index, $val)
	{
		return $key ? $this->SetSecond($val) : $this->SetFirst($val);
	}
	/**
	 * @ignore
	 */
	function offsetUnset($index)
	{
		return $key ? $this->SetSecond(null) : $this->SetFirst(null);
	}
	/**
	 * @ignore
	 */
	/*function SetCSSClass($className)
	{
		$this->First->SetCSSClass($className);
	}*/
}
?>