<?php
/**
 * @package Web.UI
 * @subpackage Controls
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
			//QueueClientFunction($this, "AddAccordionPart", array("'{$this->AccordionParts[$this->SelectedIndex]->Id}'"));
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
		if($tmpCount > 0)
			$tmpPart = $this->AccordionParts->Item[$tmpCount - 1];
		$tmpTop = ($tmpCount > 0)?$tmpPart->GetTop() + $tmpPart->TopPart->GetBottom():0;
		$accordionPart->SetTop($tmpTop);
		$accordionPart->SetWidth($this->GetWidth());
		$accordionPart->TopPart->Click = new ClientEvent("ExpandAccordionPart('$this->Id', '$accordionPart->Id')");
		$accordionPart->SetHeight($this->GetHeight() - $tmpTop);
		$this->AccordionParts->Add($accordionPart, true, true);
		QueueClientFunction($this, "AddAccordionPart", array("'$this->Id'", "'{$accordionPart->Id}'"), false);
		if($tmpCount == 0)
			$this->SetSelectedIndex(0);
		//AddScript("AddAccordionPart('$this->Id', '{$accordionPart->Id}')");
		//AddScript("ExpandAccordionPart('$this->Id', '{$accordionPart->Id}', '{$accordionPart->BottomPart->Id}')");
	}
	function Show()
	{
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/Accordion.js");
		AddNolohScriptSrc('Accordian.js');
		parent::Show();
	}
}
?>