<?php
/**
 * @package Web.UI.Controls
 */
class TabPage extends Panel 
{
	private $RolloverTab;
	
	function TabPage($tabName='TabPage', $left = 0, $top = 0, $width = null, $height = null)
	{
		parent::Panel($left, $top, $width, $height);
		$this->Text = $tabName;
		$this->SetRolloverTab();
	}
	public function SetRolloverTab($rolloverTab = null)
	{
		if($rolloverTab == null)
			$rolloverTab = new RolloverTab($this->Text, null, null);
		$this->RolloverTab = $rolloverTab;
	}
	public function GetRolloverTab(){return $this->RolloverTab;}
}
?>