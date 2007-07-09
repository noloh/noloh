<?php 

class GroupedInputControl extends Control
{
	private $GroupName;
	private $Checked;
	protected $Value;
	public $Caption;
	
	function GroupedInputControl($text="", $left = 0, $top = 0, $width = 50, $height = 20)
	{
		$this->Caption = new Label($text);
		parent::Control($left, $top, $width, $height);
		$this->Caption->Cursor = Cursor::Hand;
		//$this->Caption->Click = new ClientEvent("document.getElementById('$this->DistinctId').click()");
		//$this->Caption->Click = new ClientEvent("var obj=document.getElementById('$this->DistinctId');var val=obj.checked;obj.click();if(val!=obj.checked&&obj.onchange!=null) obj.onchange.call();");
		$this->Caption->Click = new ClientEvent("CaptionClick('$this->DistinctId')");
		$this->GroupName = $this->DistinctId;
	}
	function GetText()
	{
		return $this->Caption->Text;
	}
	function SetText($newText)
	{
		$this->Caption->Text = $newText;
	}
	function GetValue()
	{
		return ($this->Value != null)? $this->Value: $this->Text;
	}
	function SetValue($value)
	{
		$this->Value = $value;
	}
	function GetGroupName()
	{
		return $this->GroupName;
	}
	function SetGroupName($newGroupName)
	{
		$this->GroupName = $newGroupName;
		NolohInternal::SetProperty("name", $newGroupName, $this);
		//$this->HtmlName = $newGroupName;
	}
	function GetChecked()
	{
		return $this->Checked == null ? false : true;
	}
	function SetChecked($bool)
	{
		$this->Checked = $bool ? true : null;
		NolohInternal::SetProperty("checked", $bool, $this);
	}
	function SetLeft($newLeft)
	{
		parent::SetLeft($newLeft);
		$this->Caption->SetLeft($newLeft + 20);
	}
	function SetTop($newTop)
	{
		parent::SetTop($newTop);
		$this->Caption->SetTop($newTop);
	}
	function GetWidth()
	{
		return $this->Caption->GetWidth() + 20;
	}
	function SetWidth($newWidth)
	{
		parent::SetWidth(null);
		$this->Caption->SetWidth($newWidth - ($newWidth >= 20?20:0));
	}
	function GetHeight()
	{
		return $this->Caption->GetHeight();
	}
	function SetHeight($newHeight)
	{
		parent::SetHeight(null);
		$this->Caption->SetHeight($newHeight);
	}
	function SetParentId($newParent)
	{
		parent::SetParentId($newParent);
		$this->Caption->SetParentId($newParent);
	}
//	function SetCSSClass($className)
//	{
//		$this->Caption->CSSClass = $className;
//	}
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === null)
			return ",'onclick','".$this->GetEventString("Click")."this.blur();'";
		return parent::GetEventString($eventTypeAsString);
	}
	function Show()
	{
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/GroupedInputControl.js");
		if(GetBrowser()=="ie")
			NolohInternal::SetProperty("defaultChecked", $this->Checked, $this);
		$parentShow = parent::Show();
		$this->Caption->Show();
		return $parentShow;
	}
	function Hide()
	{
		$this->Caption->Hide();
		parent::Hide();
	}
}

?>