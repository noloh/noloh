<?php
/**
 * CheckControl class
 *
 * A CheckControl is a Control that can visually be checked on or off. For example, {@see CheckBox} and {@see RadioButton}
 * both extend CheckControl, and it is CheckControl's purpose to provide functionality that is common to both CheckBox and RadioButton,
 * as well as for proper organization and inheritance. It is not recommended that you extend CheckControl directly, instead, you should 
 * extend CheckBox or RadioButton.
 * 
 * @package Controls/Core
 */
abstract class CheckControl extends Control
{
	/**
	 * The Label showing the Text of this Control
	 * @var Label
	 */
	public $Caption;
	private $GroupName;
	private $Checked;
	protected $Value;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends CheckControl
	 * @param mixed $text The Text of this element. If it a Control, it will be used as the Caption. If it is an Item or a string, a Label will be instantiated as the Caption with the specified Text.
	 * @param integer $left The left coordinate of this element
	 * @param integer $top The top coordinate of this element
	 * @param integer $width The width of this element
	 * @param integer $height The height of this element
	 * @return CheckControl
	 */
	function CheckControl($text='', $left = 0, $top = 0, $width = 50, $height = 20)
	{
        parent::Control($left, $top, $width, $height);
		//$this->Caption = is_object($text) ? $text : new Label(null, 23, 0, null, null);
		if(is_object($text) && !($text instanceof Item))
			$this->Caption = $text;
		else
		{
			$this->Caption = new Label(null, 23, 0, null, null);
			$this->SetText($text);
		}
		//parent::Control($left, $top, $width, $height);
		$this->Caption->Cursor = Cursor::Hand;
		$this->Caption->Click = new ClientEvent('_NGIClick("'.$this->Id.'I");');
        $this->Caption->SetParentId($this->Id);
//		$this->GroupName = $this->Id;
	}
	/**
	 * @ignore
	 */
	function GetText()
	{
		return $this->Caption->Text;
	}
	/**
	 * @ignore
	 */
	function SetText($newText)
	{
		if($newText instanceof Item)
		{
			$this->SetValue($newText->Value);
			$newText = $newText->Text;
		}
		$this->Caption->SetText($newText);
	}
	/**
	 * Returns the value of this CheckControl. If there is no value, the Text will be returned instead.
	 * @return string
	 */
	function GetValue()
	{
		return $this->Value != null ? $this->Value : $this->Text;
	}
	/**
	 * Sets the value of this CheckControl
	 * @param text $value
	 */
	function SetValue($value)
	{
		$this->Value = $value;
	}
	/**
	 * @ignore
	 */
	function GetGroupName()
	{
		return $this->GroupName;
	}
	/**
	 * @ignore
	 */
	function SetGroupName($newGroupName)
	{
		$this->GroupName = $newGroupName;
        //if($this->GetShowStatus !== 0)
            QueueClientFunction($this, 'NOLOHChange', array('"'.$this->Id.'I"', '"name"', '"'.$newGroupName.'"'));
		//NolohInternal::SetProperty('name', $newGroupName, $this);
		//$this->HtmlName = $newGroupName;
	}
	/**
	 * Returns whether or not this element is checked
	 * @return boolean
	 */
	function GetChecked()
	{
		return $this->GetSelected();
	}
	/**
	 * Sets whether or not this element is checked
	 * @param boolean $bool
	 */
	function SetChecked($bool)
	{
		$this->SetSelected($bool);
	}
	/**
	 * An alias for GetChecked
	 * @return boolean
	 */
	function GetSelected()
	{
		return $this->Checked != null;
	}
	/**
	 * An Alias for SetChecked
	 * @param boolean $bool
	 */
	function SetSelected($bool)
	{
		$newChecked = $bool ? true : null;
		if($this->Checked != $newChecked)
		{
			if($this->GroupName != null)
			{
				$group = GetComponentById($this->GroupName);
				if($bool)
					$group->Deselect();
			}
			if($this->GetShowStatus() !== 0)
				QueueClientFunction($this, 'NOLOHChange', array('"'.$this->Id.'I"', '"checked"', $bool?1:0));
			$this->Checked = $newChecked;
			if(!$this->Change->Blank())
				$this->Change->Exec();
			if($group && !$group->Change->Blank())
				$group->Change->Exec();
		}
	}
	
	/*
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
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === null)
			return ',\'onclick\',\''.$this->GetEventString('Click').'this.blur();\'';
		return parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		AddNolohScriptSrc('CheckControl.js');
        NolohInternal::Show('DIV', parent::Show().',\'style.overflow\',\'hidden\''/*.self::GetEventString(null)*/, $this);
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
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		$this->Caption->SearchEngineShow();
	}
}

?>