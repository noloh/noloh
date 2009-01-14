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
	
	function TabPage($tabName='TabPage')
	{
		parent::Panel(0, 0, '100%', '100%');
		$this->SetRolloverTab($tabName);
	}
	public function SetRolloverTab($rolloverTab = null)
	{
		if(is_string($rolloverTab))
			$rolloverTab = new RolloverTab($rolloverTab);
		$this->RolloverTab = $rolloverTab;
	}
	public function GetRolloverTab(){return $this->RolloverTab;}
	public function SetText($text)	{$this->RolloverTab->SetText($text);}
	public function GetText()		{return $this->RolloverTab->GetText();}
}
?>