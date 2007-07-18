<?php

class CheckBox extends GroupedInputControl
{
	function CheckBox($text="", $left = 0, $top = 0, $width = 50, $height = 20)
	{
		parent::GroupedInputControl($text, $left, $top, $width, $height);
	}
	function GetEventString($whatEventTypeAsString)
	{
		$preStr = "";
		if($whatEventTypeAsString == "Click")
			$preStr = "_NSave(\"$this->Id\",\"checked\");";
		return $preStr . parent::GetEventString($whatEventTypeAsString);
	}
	function Show()
	{
		$initialProperties = parent::Show();
		$initialProperties .= ",'type','checkbox'";
		$initialProperties .= parent::GetEventString(null);
		NolohInternal::Show("INPUT", $initialProperties, $this);
		return $initialProperties;
	}
}
	
?>