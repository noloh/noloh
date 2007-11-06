<?php
/**
 * @package Web.UI.Controls
 */
class GroupedInputControl extends Control
{
	private $GroupName;
	private $Checked;
	protected $Value;
	public $Caption;
	
	function GroupedInputControl($text='', $left = 0, $top = 0, $width = 50, $height = 20)
	{
        parent::Control($left, $top, $width, $height);
		$this->Caption = is_object($text) ? $text : new Label(null, 23, 0, null, null);
		//parent::Control($left, $top, $width, $height);
		$this->Caption->Cursor = Cursor::Hand;
		$this->SetText($text);
		$this->Caption->Click = new ClientEvent('_NGIClick("'.$this->Id.'I")');
        $this->Caption->SetParentId($this->Id);
		$this->GroupName = $this->Id;
	}
	function GetText()
	{
		return $this->Caption->Text;
	}
	function SetText($newText)
	{
		if($newText instanceof Item)
		{
			$this->SetValue($newText->Value);
			$newText = $newText->Text;
		}
		$this->Caption->SetText($newText);
	}
	function GetValue()
	{
		return ($this->Value != null)? $this->Value : $this->Text;
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
        //if($this->GetShowStatus !== 0)
            QueueClientFunction($this, 'NOLOHChange', array('"'.$this->Id.'I"', '"name"', '"'.$newGroupName.'"'));
		//NolohInternal::SetProperty('name', $newGroupName, $this);
		//$this->HtmlName = $newGroupName;
	}
	function GetChecked()
	{
		return $this->Checked != null;
	}
	function SetChecked($bool)
	{
		$newChecked = $bool ? true : null;
		if($this->Checked != $newChecked)
		{
			$group = GetComponentById($this->GroupName);
			if($bool && $group instanceof RadioButtonGroup)
			{
				$selectedIndex = $group->GetSelectedIndex();
				if($selectedIndex != -1)
					$group->RadioButtons[$selectedIndex]->SetChecked(false);
			}
			$this->Checked = $newChecked;
			//NolohInternal::SetProperty('checked', $bool, $this);
			if($this->GetShowStatus !== 0)
				QueueClientFunction($this, 'NOLOHChange', array('"'.$this->Id.'I"', '"checked"', $bool?1:0));

			if(!$this->Change->Blank())
				$this->Change->Exec();
		}
	} /*
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
	}    */
//	function SetCSSClass($className)
//	{
//		$this->Caption->CSSClass = $className;
//	}
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === null)
			return ",'onclick','".$this->GetEventString('Click')."this.blur();'";
		return parent::GetEventString($eventTypeAsString);
	}
	function Show()
	{
		AddNolohScriptSrc('GroupedInputControl.js');
        NolohInternal::Show('DIV', parent::Show().",'style.overflow','hidden'"/*.self::GetEventString(null)*/, $this);
		//$this->Caption->Show();
		//return $parentShow;
	}
	/*function Bury()
	{
		$this->Caption->Bury();
		parent::Bury();
	}
	function Resurrect()
	{
		$this->Caption->Resurrect();
		parent::Resurrect();
	}*/
	function SearchEngineShow()
	{
		$this->Caption->SearchEngineShow();
	}
}

?>