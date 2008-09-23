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
	function RadioButton($text='', $left = 0, $top = 0, $width = 50, $height = 20)
	{
		parent::CheckControl($text, $left, $top, $width, $height);
	}
	/**
	 * @ignore
	 */
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
		$initialProperties = '\'id\',\''.$this->Id.'I\',\'type\',\'radio\',\'defaultChecked\','.($this->Checked?'true':'false').parent::GetEventString(null);
		if($_SESSION['_NIsIE'])
			NolohInternal::Show('<INPUT name="'.($this->GroupName != null?$this->GroupName:$this->Id).'">', $initialProperties, $this, $this->Id);
		else
        {
            if($this->GroupName === null)
                $initialProperties .= ',\'name\',\''.$this->Id.'\'';
			NolohInternal::Show('INPUT', $initialProperties, $this, $this->Id);
        }
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShow($indent);
		if($str !== false)
			echo $indent, '<INPUT type="radio" ', $str, '>', $this->Text, "</INPUT>\n";
	}
}
	
?>