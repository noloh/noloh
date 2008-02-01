<?php
class ControlPair extends Container
{
	const Horizontal = 0, Vertical = 1;
	private $Object1;
	private $Object2;
	private $Margin = 5;
	private $Layout;
	
	/*TODO
	Allow for setting of Pixel/Ratio, SetRatio()
	Automatic Label and TextBox if not objects given
	GetText(), SetText(), GetValue(), and SetValue()
	*/
	function ControlPair($firstObj, $secondObj, $layout=self::Horizontal, $left=0, $top=0, $margin)
	{
		parent::Container();
		$this->Control1 = $firstObj;
		$this->Object2 = $secondObj;
		$this->Layout = $layout;
		$this->Margin = $margin;
		$this->SetLeft($left);
		$this->SetTop($top);
		$this->Controls->AddRange($firstObj, $secondObj);
	}
	function SetControl1($obj)
	{
		$tmpLeft = $this->GetLeft();
		$tmpTop = $this->GetTop();
		$this->Controls->Remove($this->Control1);
		$this->Control1 = $obj;
		$this->SetLeft($tmpTop);
		$this->Controls->Add($this->Control1);
	}
	function SetObject2($obj)
	{
		$this->Controls->Remove($this->Object2);
		$this->Object2 = $obj;
		$this->SetLeft($this->GetLeft());
		$this->SetTop($this->GetTop());
		$this->Controls->Add($this->Object2);
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
	function GetObject2()	{return $this->Object2;}
	function SetLeft($left)
	{
		if($this->Layout == self::Horizontal)
		{
			$this->Control1->SetLeft(0 + $this->Control1->GetLeft());
			$this->Object2->SetLeft($this->Control1->GetRight() + $this->Margin + $this->Object2->GetLeft());
		}
		else
		{
			$this->Control1->SetLeft(0 + $this->Control1->GetLeft());
			$this->Object2->SetLeft(0 + $this->Object2->GetLeft());
		}
	}
	function SetTop($top)
	{
		if($this->Layout == self::Horizontal)
		{
			$this->Control1->SetTop(0 + $this->Control1->GetTop());
			$this->Object2->SetTop(0 + $this->Object2->GetTop());
		}
		else
		{
			$this->Control1->SetTop(0 + $this->Control1->GetTop());
			$this->Object2->SetTop($this->Control1->GetTop() + $this->Margin + $this->Object2->GetTop());
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
			return $this->Control1->GetWidth() + $this->Object2->GetWidth() + $this->Margin;
		else
			if(($obj1Width = $this->Control1->GetWidth()) > ($obj2Width = $this->Object2->GetWidth()))
				return $obj1Width;
			else
				return $obj2Width;
	}
	function GetHeight()
	{
		if($this->Layout == self::Vertical)
			return $this->Control1->GetHeight() + $this->Object2->GetHeight() + $this->Margin;
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
}
?>
