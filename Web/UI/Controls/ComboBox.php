<?php

class ComboBox extends ListControl 
{
	private $SelectedIndex;
	
	function ComboBox($left = 0, $top = 0, $width = 83, $height = 20)
	{
		parent::ListControl($left, $top, $width, $height);
		//$this->SetSelectedIndex(0);
	}
	function GetSelectedIndex()
	{
		return ($this->SelectedIndex === null)?-1:$this->SelectedIndex;
	}
	function SetSelectedIndex($newIndex)
	{
		if($this->GetSelectedIndex() != $newIndex)
		{
			$this->SelectedIndex = $newIndex;
			parent::SetSelectedIndex($newIndex);
		}
	}
	function GetSelectedItem()
	{
		return $this->SelectedIndex != -1 ? $this->Items->Item[$this->SelectedIndex] : null;
	}
	function GetEventString($eventTypeAsString)
	{
		$preStr = "";
		if($eventTypeAsString == "Change")
			$preStr = "_NSave(\"$this->Id\",\"selectedIndex\");";
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
	function AddItem($item)
	{
		parent::AddItem($item);
		if($this->Items->Count == 1)
			$this->SetSelectedIndex(0);
	}
	function Show()
	{
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/ListControl.js");
		$initialProperties = parent::Show();
		$initialProperties .= $this->GetEventString(null);
		NolohInternal::Show("SELECT", $initialProperties, $this);
	}
}

?>