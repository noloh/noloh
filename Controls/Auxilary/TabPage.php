<?php
/**
 * TabPage class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Auxiliary
 */
class TabPage extends Panel 
{
	private $RolloverTab;
	
	function TabPage($tabName='TabPage', $rolloverTab = null)
	{
		parent::Panel(0, 0, '100%', '100%');
		$this->Text = $tabName;
		$this->SetRolloverTab($rolloverTab);
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