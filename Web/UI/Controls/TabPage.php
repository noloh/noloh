<?php

class TabPage extends Panel 
{
	private $MyRolloverTab;
	
	function TabPage($whatName="TabPage", $whatLeft = 0, $whatTop = 0, $whatWidth = 100, $whatHeight = 100)
	{
		parent::Panel($whatLeft, $whatTop, $whatWidth, $whatHeight);
		$this->Text = $whatName;
		$this->SetRolloverTab();
		/*if(GetBrowser() == "ie")
		{
			//$this->Controls->Add(new IFrame("",0,0, &$whatWidth, &$whatHeight));
			$temp =  new Label("", 0,0);
			$this->BgColor = "white";
			$temp->BgColor = &$this->BgColor;
			$temp->Height = &$this->Height;
			$temp->Width = &$this->Width;
			$this->Controls->Add($temp);
		}
		else
			$this->BgColor = "white";*/
		$this->BackColor = "white";
	}
	public function SetRolloverTab(RolloverTab $whatRolloverTab = null)
	{
		if($whatRolloverTab == null)
			$whatRolloverTab = new RolloverTab($this->Text, null, null);
		$this->MyRolloverTab = $whatRolloverTab;
	}
	public function GetRolloverTab(){return $this->MyRolloverTab;}
}

?>