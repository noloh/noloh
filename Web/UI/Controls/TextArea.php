<?php

class TextArea extends Control 
{
	private $MaxLength;
	
	function TextArea($text = null, $left = 0, $top = 0, $width = 200, $height = 100, $maxLength = -1)  
	{
		parent::Control($left, $top, $width, $height);
		$this->SetMaxLength($maxLength);
		if($text != null)
			$this->SetText($text);
	}
	
	function GetMaxLength()
	{
		return $this->MaxLength;
	}
	
	function SetMaxLength($newMaxLength)
	{
		$this->MaxLength = $newMaxLength;
		NolohInternal::SetProperty("MaxLength", $newMaxLength, $this);
	}
	
	function SetText($newText)
	{
		parent::SetText($newText);
		QueueClientFunction($this, "SetTextAreaText", array("'$this->DistinctId'", "'".preg_replace("(\r\n|\n|\r)", "<Nendl>", $newText)."'"));
	}
	
	function GetEventString($whatEventTypeAsString)
	{
		if($whatEventTypeAsString === null)
			return ",'onchange','".$this->GetEventString("Change")."'" .
				(GetBrowser()=="ie" 
				?
					",'onkeypress','doKeyPress(\"$this->DistinctId\",this.MaxLength);'" .
					",'onpaste','doPaste(\"$this->DistinctId\",this.MaxLength);'"
				:
					",'onkeypress','doKeyPress(event);'");

		$preStr = "";
		if($whatEventTypeAsString == "Change")
			$preStr = "_NSave(\"$this->DistinctId\",\"value\");";
		return $preStr . parent::GetEventString($whatEventTypeAsString);
	}
	
	function Show()
	{
		$initialProperties = parent::Show();
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/".(GetBrowser() == "ie"?"IE":"Mozilla")."TextAreaScripts.js");
		$initialProperties .= $this->GetEventString(null);
		NolohInternal::Show("TEXTAREA", $initialProperties, $this);
		if(GetBrowser() != "ie")
			AddScript("document.getElementById('$this->DistinctId').addEventListener('input',doInput,false)");
	}
}

?>