<?php
/**
 * @package Web.UI.Controls
 */
class TabPage extends Panel 
{
	private $MyRolloverTab;
	
	function TabPage($tabName="TabPage", $left = 0, $top = 0, $width = null, $height = null)
	{
		parent::Panel($left, $top, $width, $height);
		$this->Text = $tabName;
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
//		$this->BackColor = "white";
	}
	public function SetRolloverTab(RolloverTab $rolloverTab = null)
	{
		if($rolloverTab == null)
			$rolloverTab = new RolloverTab($this->Text, null, null);
		$this->MyRolloverTab = $rolloverTab;
	}
	public function GetRolloverTab(){return $this->MyRolloverTab;}
}

?>