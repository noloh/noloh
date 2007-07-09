<?php

class PlusMinusSwitch extends Image
{	
	function PlusMinusSwitch($whatLeft=0, $whatTop=0, $whatWidth=9, $whatHeight=9)
	{
		parent::Image(NOLOHConfig::GetNOLOHPath()."Web/UI/Controls/Images/plus.gif", $whatLeft, $whatTop, $whatWidth, $whatHeight);
		//$this->BackColor = "#FFFFFF";
		//$this->Border = "1px solid #000000";
		//$this->Click = new ClientEvent("var Obj=document.getElementById(" . $this->DistinctId  . "); Obj.checked = !Obj.checked;");
		$this->Click = new ClientEvent('PlusMinusSwitchClick("' . $this->DistinctId . '")');
		//$this->Cursor = "default";
	}
	
	function Show()
	{
		parent::Show();
		//require_once($_SERVER['DOCUMENT_ROOT'] . "/NOLOH/Javascripts/PlusMinusSwitchScripts.js");	
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/PlusMinusSwitchScripts.js");
	}
	
	/*
	function Show($IndentLevel=0)
	{
		if($this->Checked == true)
			$this->Text = "-";
		else 
			$this->Text = "+";
		parent::Show($IndentLevel);
		$_SESSION['OnLoadScriptOnce'] .= "document.getElementById(" . $this->DistinctId . 
			").checked = " . $this->Checked . ";\n";
	}
	*/
}

?>