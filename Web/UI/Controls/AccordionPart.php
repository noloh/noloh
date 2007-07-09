<?php
class AccordionPart extends Panel
{
	private $TopPart;
	private $BottomPart;
	
	function AccordionPart($whatTopPartHeight = 15)
	{
		parent::Panel();
		$this->TopPart = new Panel();
		$this->TopPart->SetHeight($whatTopPartHeight);
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
	function SetWidth($whatWidth)
	{
		parent::SetWidth($whatWidth);
		$this->TopPart->Width = $whatWidth - 2;
		$this->BottomPart->Width = $whatWidth - 2;
	}
	function SetTopPartHeight($whatTopPartHeight)
	{
		$this->TopPart->Height = $whatTopPartHeight;
	}
	function Show()
	{
		parent::Show();
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/Accordion.js");
		QueueClientFunction($this, "SetAccordionPart", array("'$this->DistinctId'", "'{$this->TopPart->DistinctId}'", "'{$this->BottomPart->DistinctId}'"));
		//AddScript("SetAccordionPart('$this->DistinctId', '{$this->TopPart->DistinctId}', '{$this->BottomPart->DistinctId}')", Priority::High);
	}
}
?>