<?php
/**
 * @package Controls/Auxilary
 */
/**
 * TabPage class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
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