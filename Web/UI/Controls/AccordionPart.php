<?php
/**
 * @package Web.UI
 * @subpackage Controls
 */
class AccordionPart extends Panel
{
	private $TopPart;
	private $BottomPart;
	
	function AccordionPart($topPartHeight = 15)
	{
		parent::Panel();
		$this->TopPart = new Panel();
		$this->TopPart->SetHeight($topPartHeight);
		$this->BottomPart = new Panel();
		$this->BottomPart->SetTop($this->TopPart->Bottom);
		$this->Controls->Add($this->TopPart);
		$this->Controls->Add($this->BottomPart);
	}	
	function GetTopPart()
	{
		return $this->TopPart;
	}
	function GetBottomPart()
	{
		return $this->BottomPart;
	}
	function SetWidth($width)
	{
		parent::SetWidth($width);
		$this->TopPart->Width = $width - 2;
		$this->BottomPart->Width = $width - 2;
	}
	function SetTopPartHeight($topPartHeight)
	{
		$this->TopPart->Height = $topPartHeight;
	}
	function Show()
	{
		parent::Show();
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/Accordion.js");
		AddNolohScriptSrc('Accordion.js');
		QueueClientFunction($this, 'SetAccordionPart', array("'$this->Id'", "'{$this->TopPart->Id}'", "'{$this->BottomPart->Id}'"));
		//AddScript("SetAccordionPart('$this->Id', '{$this->TopPart->Id}', '{$this->BottomPart->Id}')", Priority::High);
	}
}
?>