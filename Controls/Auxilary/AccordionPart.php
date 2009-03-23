<?php
/**
 * AccordianPart class
 * 
 * Each section of an Accordion is an AccordionPart. 
 * AccordionParts should be treated like a Panel when adding or removing object.
 * 
 * Example Usage:
 * <pre>
 * $accordion = new Accordion();
 * $accordion->AccordionParts->Add(new AccordionPart('Section 1'));
 * </pre>
 * 
 * @package Controls/Auxiliary
 */
class AccordionPart extends CollapsePanel implements Groupable
{
	/**
	 * Constructor
	 * @param string $title The title you wish to display in the titlebar.
	 * @param int $titleHeight The height of the titlebar.
	 */
	function AccordionPart($title, $titleHeight = 28)
	{
		parent::CollapsePanel($title, 0, 0, '100%', 28, $titleHeight);
		$this->SetCollapsed(true);
//		$this->SetTogglesOff(false);
		$select = Control::GetSelect();
//		$this->ToggleButton->SetTogglesOff(false);
		$this->Layout = Layout::Relative;
		
		$select['System']['Collapse'] = new ClientEvent("_NAccPtExpd('$this->Id');_NClpsPnlTgl('$this->Id');");
		//$select['System'] = new ClientEvent("_NAccPtExpd('$this->Id');_NSetProperty('{$this->TitlePanel->Controls['Glossy']->Id}', 'Selected', true);");
		//$deselect['System'] = new ClientEvent("_NClpsPnlTgl('{$this->Id}', false);_NSetProperty('{$this->TitlePanel->Controls['Glossy']->Id}', 'Selected', false);");
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		parent::Show();
		AddNolohScriptSrc('Accordion.js');
		
	}
}
?>