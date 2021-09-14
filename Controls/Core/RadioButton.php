<?php
/**
 * RadioButton class
 *
 * A Control for a conventional web radio button. RadioButtons are used to present a user with a variety of choices from which
 * they can only Check one per Group. In this way, it is somewhat similar to a ComboBox, except that the RadioButton choices
 * all show immediately, whereas a user has to click down a ComboBox menu to view the choices. It differs from a CheckBox in 
 * that many CheckBoxes from the same Group can be simultaneously whereas RadioButtons only allow one Checked choice per Group.
 * 
 * @package Controls/Core
 */
class RadioButton extends CheckControl implements Groupable 
{
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends RadioButton
	 * @param string $text The Text of this element
	 * @param integer $left The Left of this element
	 * @param integer $top The Top of this element
	 * @param integer $width The Width of this element
	 * @param integer $height The Height of this element
	 * @return RadioButton
	 */
	function __construct($text='', $left = 0, $top = 0, $width = 50, $height = 20)
	{
		parent::__construct($text, $left, $top, $width, $height);
		//$this->Caption->Click = new ClientEvent('_N("'.$this->Id.'I").click();');
	}
	/**
	 * @ignore
	 *
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === 'Click')
//		if($eventTypeAsString == 'Click' || $eventTypeAsString == 'Change')
			return '_NRBSave("'.$this->Id.'");' . parent::GetEventString($eventTypeAsString);
		return parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		parent::Show();
		//AddNolohScriptSrc('RadioButton.js');
		$initialProperties = '\'type\',\'radio\',\'defaultChecked\','.($this->Checked?1:0).',\'onclick\',\'_NChkCtrlTgl("'.$this->Id.'",false);\'';
		if(UserAgent::IsIE() && UserAgent::GetVersion() < 9)
			NolohInternal::Show('<INPUT name="'.($this->GroupName?$this->GroupName:$this->Id).'">', $initialProperties, $this, $this->Id, $this->Id.'I');
		else
		{
			if($this->GroupName === null)
				$initialProperties .= ',\'name\',\''.$this->Id.'\'';
			NolohInternal::Show('INPUT', $initialProperties, $this, $this->Id, $this->Id.'I');
		}
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShowIndent($indent);
		if($str !== false)
			echo $indent, '<INPUT type="radio" ', $str, '>', $this->Text, "</INPUT>\n";
	}
}

?>