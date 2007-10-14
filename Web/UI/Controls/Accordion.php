<?php
/**
 * @package Web.UI.Controls
 */
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
			QueueClientFunction($this, "ExpandAccordionPart", array("'$this->Id'",  "'{$this->AccordionParts[$this->SelectedIndex]->Id}'"), true, Priority::Low);
		}
	}
	function GetSelectedIndex()
	{
		return (($this->SelectedIndex == null)?-1:$this->SelectedIndex);
	}
	function AddAccordionPart($accordionPart)
	{
		$tmpCount = $this->AccordionParts->Count();
		if(is_string($accordionPart))
			$accordionPart = new AccordionPart($accordionPart);
		$accordionPart->TitlePanel->Click = new ClientEvent("ExpandAccordionPart('$this->Id', '$accordionPart->Id')");
		$this->AccordionParts->Add($accordionPart, true, true);
		QueueClientFunction($this, "AddAccordionPart", array("'$this->Id'", "'{$accordionPart->Id}'"), false);
		if($tmpCount == 0)
			$this->SetSelectedIndex(0);
	}
	function InsertAccordionPart($accordionPart, $index)
	{
		$tmpCount = $this->AccordionParts->Count();
		if(is_string($accordionPart))
			$accordionPart = new AccordionPart($accordionPart);
		$accordionPart->TitlePanel->Click = new ClientEvent("ExpandAccordionPart('$this->Id', '$accordionPart->Id')");
		$this->AccordionParts->Insert($accordionPart, $index);
		QueueClientFunction($this, "AddAccordionPart", array("'$this->Id'", "'{$accordionPart->Id}'"), false);
		if($tmpCount == 0)
			$this->SetSelectedIndex(0);
	}
	function Show()
	{
		AddNolohScriptSrc('Accordion.js');
		parent::Show();
	}
}
?>