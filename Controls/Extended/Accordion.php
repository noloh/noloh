<?php
/**
 * Accordian class
 *
 * An Accordion is a Group of AccordionParts where at most one AccordionPart is expanded.
 * From a user interface perspective, it is a device for organizing information into collapsible sections.
 * Selecting an AccordionPart will deselect the previously selected AccordionPart.
 *
 * @package Controls/Extended
 */
class Accordion extends Panel
{
	/**
	* An ArrayList of AccordionParts that will be Shown when added, provided the Accordion has also been Shown.
	*
	* AccordionParts are an ArrayList and can be added, removed, or inserted. See ArrayList for more information.
	*
	* <pre>
	* //Adding an AccordionPart through a string
	* $accordion->AccordionParts->Add('Section 1');
	* //Adding multiple AccordionParts through strings
	* $accordion->AccordionParts->AddRange('Section 1', 'Section2');
	* //Adding an AccordionPart to AccordionParts:
	* $accordion->AccordionParts->Add(new AccordionPart('Section 1'));
	* //Adding multiple AccordionParts through AddRange()
	*  $accordion->AccordionParts->AddRange(new AccordionPart('Section 1'), new AccordionPart('Section 2'));
	* </pre>
	* @var ArrayList
	*/
	public $AccordionParts;
	private $SelectedIndex;
	private $PartGroup;
	/**
	 * Constructor
	 *
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 */
	function Accordion($left=0, $top=0, $width=200, $height=300)
	{
		parent::Panel($left, $top, $width, null, $this);
		$this->PartGroup = new Group();
		$this->PartGroup->ParentId = $this->Id;
		NolohInternal::SetProperty('TitleHeight', 0, $this);
		$this->AccordionParts = &$this->Controls;
		$this->AccordionParts->AddFunctionName = 'AddAccordionPart';
		$this->AccordionParts->RemoveAtFunctionName = 'RemoveAccordionPartAt';
		$this->SetHeight($height);
	}
	/**
	 * Selects an AccordionPart whose index in the AccordionParts ArrayList matches the parameter
	 * @param integer $index
	 */
	function SetSelectedIndex($index)
	{
		if($this->GetSelectedIndex() != $index)
			$this->AccordionParts[$index]->SetSelected(true);
	}
	/**
	 * Returns the index of the first selected AccordionPart
	 * @return integer
	 */
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
//		QueueClientFunction($this, '_NAccPtAdd', array("'$this->Id'", "'{$accordionPart->Id}'"), false);
		ClientScript::Queue($this, '_NAccPtAdd', array($this, $accordionPart), false);
//		if($count == 0)
//			$this->SetSelectedIndex(0);
	}
	/**
	 * @ignore
	 */
	function InsertAccordionPart($accordionPart, $index)
	{
		$count = $this->AccordionParts->Count();
		if(is_string($accordionPart))
			$accordionPart = new AccordionPart($accordionPart);
//		$accordionPart->TitlePanel->Click = new ClientEvent("_NAccPtExpd('$this->Id', '$accordionPart->Id');");
		$this->AccordionParts->Insert($accordionPart, $index);
//		QueueClientFunction($this, '_NAccPtAdd', array("'$this->Id'", "'{$accordionPart->Id}'"), false);
		ClientScript::Queue($this, '_NAccPtAdd', array($this, $accordionPart), false);
		if($count == 0)
			$this->SetSelectedIndex(0);
	}
	/**
	 * @ignore
	 */
	function RemoveAccordionPartAt($index)
	{
		ClientScript::Queue($this, '_NAccPtRm', array($this, $index), false, Priority::High);
//		QueueClientFunction($this, '_NAccPtRm', array("'$this->Id'", "$index"), false, Priority::High);
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
	function SetHeight($height)
	{
		parent::SetHeight($height);
		if(($selectedIndex = $this->GetSelectedIndex()) >= 0)
		{
			$accordionPart = $this->AccordionParts[$selectedIndex];
			ClientScript::Queue($this, "_NAccPtExpd('{$accordionPart->Id}');_NClpsPnlTgl('{$accordionPart->Id}');");
		}
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