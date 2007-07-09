<?php

class RadioButton extends GroupedInputControl
{
	function RadioButton($text="", $left = 0, $top = 0, $width = 50, $height = 20)
	{
		parent::GroupedInputControl($text, $left, $top, $width, $height);
	}
	function GetEventString($eventTypeAsString)
	{
		$preStr = "";
		if($eventTypeAsString == "Click")
			$preStr = "RadioButtonSave(\\\"$this->DistinctId\\\");";
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
	function Show()
	{
		$initialProperties = parent::Show();
		$initialProperties .= ",'type','radio'";
		$initialProperties .= parent::GetEventString(null);
		NolohInternal::Show("INPUT", $initialProperties, $this);
		return $initialProperties;
	}
}
	
?>