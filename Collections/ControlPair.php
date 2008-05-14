<?php
/**
 * @package Web.UI.Controls
 */
/**
 * ControlPair class
 * 
 * This class needs a description...
 */
class ControlPair extends Container
{
	const Horizontal = 0, Vertical = 1;
	private $Control1;
	private $Control2;
	private $Margin = 5;
	private $Layout;
	
	/*TODO
	Allow for setting of Pixel/Ratio, SetRatio()
	*/
	function ControlPair($firstControl, $secondControl=null, $left=0, $top=0, $layout=ControlPair::Horizontal, $margin = 0)
	{
		parent::Container();
		if(is_string($firstControl))
			$firstControl = new Label($firstControl, $left, $top, System::Auto);
		$this->Control1 = $firstControl;
		if($secondControl === null)
			$secondControl = new TextBox(0, 0);
		$this->Control2 = $secondControl;
		$this->Layout = $layout;
		$this->Margin = $margin;
		$this->SetLeft($left);
		$this->SetTop($top);
		$this->Controls->AddRange($firstControl, $secondControl);
	}
	function SetControl1($obj)
	{
		$tmpLeft = $this->GetLeft();
		//$tmpTop = $this->GetTop();
		$this->Controls->Remove($this->Control1);
		$this->Control1 = $obj;
		$this->SetLeft($tmpLeft);
		$this->Controls->Add($this->Control1);
	}
	function SetControl2($obj)
	{
		$this->Controls->Remove($this->Control2);
		$this->Control2 = $obj;
		$this->SetLeft($this->GetLeft());
		$this->SetTop($this->GetTop());
		$this->Controls->Add($this->Control2);
	}
	function SetMargin($margin)
	{
		if($this->Margin != $margin)
		{
			$this->Margin = $margin;
			if($this->Layout == self::Horizontal)
				$this->SetLeft($this->GetTop());
			else
				$this->SetTop($this->GetTop());
		}
	}
	function GetMargin()	{return $this->Margin;}
	function GetControl1()	{return $this->Control1;}
	function GetControl2()	{return $this->Control2;}
	function SetLeft($left)
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
	}
	function SetTop($top)
	{
		if($this->Layout == self::Horizontal)
		{
			$this->Control1->SetTop($top + $this->Control1->GetTop());
			$this->Control2->SetTop($top + $this->Control2->GetTop());
		}
		else
		{
			$this->Control1->SetTop($top + $this->Control1->GetTop());
			$this->Control2->SetTop($this->Control1->GetTop() + $this->Margin + $this->Control2->GetTop());
		}
	}
	function GetLeft()
	{
		return $this->Control1->GetLeft();
	}
	function GetTop()
	{
		return $this->Control1->GetTop();
	}
	function GetRight()
	{
		return $this->GetLeft() + $this->GetWidth();
	}
	function GetBottom()
	{
		return $this->GetTop() + $this->GetHeight();
	}
	function GetWidth()
	{
		if($this->Layout == self::Horizontal)
			return $this->Control1->GetWidth() + $this->Control2->GetWidth() + $this->Margin;
		else
			if(($obj1Width = $this->Control1->GetWidth()) > ($obj2Width = $this->Control2->GetWidth()))
				return $obj1Width;
			else
				return $obj2Width;
	}
	function GetHeight()
	{
		if($this->Layout == self::Vertical)
			return $this->Control1->GetHeight() + $this->Control2->GetHeight() + $this->Margin;
		else
			if(($obj1Height = $this->Control1->GetHeight()) > ($obj2Height = $this->Control2->GetHeight()))
				return $obj1Height;
			else
				return $obj2Height;
	}
	function SetLayout($layout)
	{
		if($this->Layout != $layout)
		{
			$this->Layout = $layout;
			$this->SetTop($this->GetTop());
			$this->SetLeft($this->GetLeft());
		}
	}
	function GetValue()	{return $this->Control2->GetText();}
	function SetValue($value)	{$this->Control2->SetText($value);}
	function GetText()	{return $this->Control1->GetText();}
	function SetText($text)	{$this->Control1->SetText($text);}
	function SetCSSClass($className)
	{
		$this->Control1->SetCSSClass($className);
	}
}
?>
