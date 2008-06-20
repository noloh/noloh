<?php
/**
 * Accordian class
 *
 * This class needs a description...
 * 
 * @package Controls/Extended
 */
class Accordion extends Panel
{
	public $AccordionParts;
	private $SelectedIndex;
	
	function Accordion($left, $top, $width, $height)
	{
		parent::Panel($left, $top, $width, $height, $this);
		$this->AccordionParts = &$this->Controls;
		$this->AccordionParts->AddFunctionName = 'AddAccordionPart';
		$this->AccordionParts->RemoveAtFunctionName = 'RemoveAccordionPartAt';
	}
	function SetSelectedIndex($index, $override=false)
	{
		if($this->GetSelectedIndex() != $index || $override)
		{
			$this->SelectedIndex = $index;
			QueueClientFunction($this, '_NExpandAccordPt', array("'$this->Id'",  "'{$this->AccordionParts[$this->SelectedIndex]->Id}'"), true, Priority::Low);
		}
	}
	function GetSelectedIndex()
	{
		return (($this->SelectedIndex == null)?-1:$this->SelectedIndex);
	}
	/**
	 * @ignore
	 */
	function AddAccordionPart($accordionPart)
	{
		$tmpCount = $this->AccordionParts->Count();
		if(is_string($accordionPart))
			$accordionPart = new AccordionPart($accordionPart);
		$accordionPart->TitlePanel->Click = new ClientEvent("_NExpandAccordPt('$this->Id', '$accordionPart->Id');");
		$this->AccordionParts->Add($accordionPart, true, true);
		QueueClientFunction($this, '_NAddAccordPt', array("'$this->Id'", "'{$accordionPart->Id}'"), false);
		if($tmpCount == 0)
			$this->SetSelectedIndex(0);
	}
	/**
	 * @ignore
	 */
	function InsertAccordionPart($accordionPart, $index)
	{
		$tmpCount = $this->AccordionParts->Count();
		if(is_string($accordionPart))
			$accordionPart = new AccordionPart($accordionPart);
		$accordionPart->TitlePanel->Click = new ClientEvent("_NExpandAccordPt('$this->Id', '$accordionPart->Id');");
		$this->AccordionParts->Insert($accordionPart, $index);
		QueueClientFunction($this, '_NAddAccordPt', array("'$this->Id'", "'{$accordionPart->Id}'"), false);
		if($tmpCount == 0)
			$this->SetSelectedIndex(0);
	}
	/**
	 * @ignore
	 */
	function RemoveAccordionPartAt($index)
	{
		QueueClientFunction($this, '_NRmAccordPt', array("'$this->Id'", "$index"), false, Priority::High);
		$this->AccordionParts->RemoveAt($index, true);
		if($this->SelectedIndex == $index)
			$this->SetSelectedIndex(0, true);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		AddNolohScriptSrc('Accordion.js');
		parent::Show();
	}
}
?>