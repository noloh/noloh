<?php
/**
 * Accordian class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Extended
 */
class Accordion extends Panel
{
	public $AccordionParts;
	private $SelectedIndex;
	private $PartGroup;
	
	function Accordion($left, $top, $width, $height)
	{
		parent::Panel($left, $top, $width, $height, $this);
		$this->PartGroup = new Group();
		$this->PartGroup->ParentId = $this->Id;
		NolohInternal::SetProperty('TitleHeight', 0, $this);
		$this->AccordionParts = &$this->Controls;
		$this->AccordionParts->AddFunctionName = 'AddAccordionPart';
		$this->AccordionParts->RemoveAtFunctionName = 'RemoveAccordionPartAt';
	}
	function SetSelectedIndex($index)
	{
		if($this->GetSelectedIndex() != $index)
			$this->AccordionParts[$index]->SetSelected(true);
	}
	function GetSelectedIndex()
	{
		return $this->PartGroup->GetSelectedIndex();
	}
	/**
	 * @ignore
	 */
	function AddAccordionPart($accordionPart)
	{
		$count = $this->AccordionParts->Count();
		if(is_string($accordionPart))
			$accordionPart = new AccordionPart($accordionPart);
		NolohInternal::SetProperty('Accord', $this->Id, $accordionPart);
//		$accordionPart->SetGroupName($this->PartGroup->Id);
		$this->PartGroup->Add($accordionPart);
		$this->AccordionParts->Add($accordionPart, true);
		QueueClientFunction($this, '_NAccPtAdd', array("'$this->Id'", "'{$accordionPart->Id}'"), false);
//		if($count == 0)
//			$this->SetSelectedIndex(0);
	}
	/**
	 * @ignore
	 */
	function InsertAccordionPart($accordionPart, $index)
	{
		$tmpCount = $this->AccordionParts->Count();
		if(is_string($accordionPart))
			$accordionPart = new AccordionPart($accordionPart);
//		$accordionPart->TitlePanel->Click = new ClientEvent("_NAccPtExpd('$this->Id', '$accordionPart->Id');");
		$this->AccordionParts->Insert($accordionPart, $index);
		QueueClientFunction($this, '_NAccPtAdd', array("'$this->Id'", "'{$accordionPart->Id}'"), false);
		if($tmpCount == 0)
			$this->SetSelectedIndex(0);
	}
	/**
	 * @ignore
	 */
	function RemoveAccordionPartAt($index)
	{
		QueueClientFunction($this, '_NAccPtRm', array("'$this->Id'", "$index"), false, Priority::High);
		$this->AccordionParts->RemoveAt($index, true);
		if($this->SelectedIndex == $index)
			$this->SetSelectedIndex(0, true);
	}
	/**
	 * @ignore
	 */
	function Skin($background=null, $titleImage=null, $arrow=null)
	{
		//$backColor
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		AddNolohScriptSrc('Accordion.js');
		AddNolohScriptSrc('Animation.js', true);
		parent::Show();
	}
}
?>