<?php
	
class ListBox extends ListControl
{
	private $SelectedIndices;

	function ListBox($left = 0, $top = 0, $width = 83, $height = 40)
	{
		parent::ListControl($left, $top, $width, $height);
		$this->SelectedIndices = array();
	}
	
	function GetSelectedIndex()
	{
		if($this->SelectedIndices == null)
			return -1;
		else 
			return min($this->SelectedIndices);
	}
	
	function SetSelectedIndex($index)
	{
		if(!in_array($index, $this->SelectedIndices))
		{
			$this->SelectedIndices[] = $index;
			parent::SetSelectedIndex($index);
		}
	}
	
	function GetSelectedIndices()
	{
		return $this->SelectedIndices;
	}
	
	function SetSelectedIndices($selectedIndices)
	{
		$this->SelectedIndices = array();
		foreach($selectedIndices as $idx)
			$this->SetSelectedIndex($idx);
	}
	
	function SetSelected($index, $bool)
	{
		if($bool)
			SetSelectedIndex($index);
		elseif(in_array($index, $this->SelectedIndices))
		{
			//NolohInternal::SetProperty("options[$whatIndex].selected", false, $this);
			//QueueClientFunction($this, "document.getElementById('$this->DistinctId').options[$index].selected=false;void", array(0));
			QueueClientFunction($this, "_NListDesel", array("'$this->DistinctId'", $index), false);
			//AddScript("document.getElementById('$this->DistinctId').options[$whatIndex].selected=false");
			unset($this->SelectedIndices[array_search($index, $this->SelectedIndices)]);
		}
	}
	
	function GetEventString($whatEventTypeAsString)
	{
		$preStr = "";
		if($whatEventTypeAsString == "Change")
			$preStr = "_NSave(\"$this->DistinctId\",\"selectedIndices\",ImplodeSelectedIndices(this.options));";
		return $preStr . parent::GetEventString($whatEventTypeAsString);
	}

	function Show()
	{
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/ListControl.js");
		$initialProperties = parent::Show();
		$initialProperties .= ",'multiple','true'";
		$initialProperties .= $this->GetEventString(null);
		NolohInternal::Show("SELECT", $initialProperties, $this);
	}
}

?>