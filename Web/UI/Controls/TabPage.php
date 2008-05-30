<?php
/**
 * @package Web.UI.Controls
 */
class TabPage extends Panel 
{
	private $RolloverTab;
	
	function TabPage($tabName='TabPage')
	{
		parent::Panel(null, null, null, null);
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