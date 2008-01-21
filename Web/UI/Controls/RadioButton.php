<?php
/**
 * @package Web.UI.Controls
 */
class RadioButton extends GroupedInputControl implements Groupable 
{
	function RadioButton($text='', $left = 0, $top = 0, $width = 50, $height = 20)
	{
		parent::GroupedInputControl($text, $left, $top, $width, $height);
	}
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString == 'Click')
//		if($eventTypeAsString == 'Click' || $eventTypeAsString == 'Change')
			return '_NRBSave("'.$this->Id.'");' . parent::GetEventString($eventTypeAsString);
		return parent::GetEventString($eventTypeAsString);
	}
	function Show()
	{
        parent::Show();
		$initialProperties = "'id','".$this->Id."I','type','radio','defaultChecked',".($this->Checked?'true':'false').parent::GetEventString(null);
		if(GetBrowser()=='ie')
			NolohInternal::Show('<INPUT name="'.($this->GroupName != null?$this->GroupName:$this->Id).'">', $initialProperties, $this, $this->Id);
		else
        {
            if($this->GroupName != null)
                $initialProperties .= ",'name','$this->GroupName'";
			NolohInternal::Show('INPUT', $initialProperties, $this, $this->Id);
        }
	}
}
	
?>