<?php

class Accordion extends Panel
{
	public $AccordionParts;
	private $SelectedIndex;
	
	function Accordion($left, $top, $width, $height)
	{
		parent::Panel($left, $top, $width, $height, $this);
		$this->AccordionParts = &$this->Controls;
		$this->AccordionParts->AddFunctionName = "AddAccordionPart";
	}
	function SetSelectedIndex($index)
	{
		if($this->GetSelectedIndex() != $index)
		{
			$this->SelectedIndex = $index;
			//QueueClientFunction($this, "AddAccordionPart", array("'{$this->AccordionParts[$this->SelectedIndex]->DistinctId}'"));
			QueueClientFunction($this, "ExpandAccordionPart", array("'$this->DistinctId'",  "'{$this->AccordionParts[$this->SelectedIndex]->DistinctId}'"), true, Priority::Low);
		}
	}
	function GetSelectedIndex()
	{
		return (($this->SelectedIndex == null)?-1:$this->SelectedIndex);
	}
	function AddAccordionPart($accordionPart)
	{
		$tmpCount = $this->AccordionParts->Count();
		if($tmpCount > 0)
			$tmpPart = $this->AccordionParts->Item[$tmpCount - 1];
		$tmpTop = ($tmpCount > 0)?$tmpPart->GetTop() + $tmpPart->TopPart->GetBottom():0;
		$accordionPart->SetTop($tmpTop);
		$accordionPart->SetWidth($this->GetWidth());
		$accordionPart->TopPart->Click = new ClientEvent("ExpandAccordionPart('$this->DistinctId', '$accordionPart->DistinctId')");
		$accordionPart->SetHeight($this->GetHeight() - $tmpTop);
		$this->AccordionParts->Add($accordionPart, true, true);
		QueueClientFunction($this, "AddAccordionPart", array("'$this->DistinctId'", "'{$accordionPart->DistinctId}'"), false);
		if($tmpCount == 0)
			$this->SetSelectedIndex(0);
		//AddScript("AddAccordionPart('$this->DistinctId', '{$accordionPart->DistinctId}')");
		//AddScript("ExpandAccordionPart('$this->DistinctId', '{$accordionPart->DistinctId}', '{$accordionPart->BottomPart->DistinctId}')");
	}
	function Show()
	{
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/Accordion.js");
		parent::Show();
	}
}
?>