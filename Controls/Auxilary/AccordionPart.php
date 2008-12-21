<?php
/**
 * AccordianPart class
 * 
 * This class needs a description...
 * 
 * @package Controls/Auxiliary
 */
class AccordionPart extends CollapsePanel implements Groupable
{
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