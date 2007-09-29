<?php
/**
 * @package Web.UI.Controls
 */
class AccordionPart extends Panel
{
	private $TitlePanel;
	private $BottomPart;
	
	function AccordionPart($title, $titleHeight = 28)
	{
		
		parent::Panel(0, 0, null, null);
		$this->PositionType = 1;
		$this->TitlePanel = new Panel(0, 0, null, $titleHeight);
		$this->TitlePanel->CSSClass = 'NAccordionTitle';
		$tmpGlossy = new Image(NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Std/glossy.png', 0, 0, '100%', $titleHeight);
		$tmpTitleLabel = new Label($title, 0, 0, null, null);
		$tmpTitleLabel->CSSClass = 'NAccordionText';
		$this->TitlePanel->Controls->AddRange($tmpGlossy, $tmpTitleLabel);
		$this->TitlePanel->ParentId = $this->Id;
		$this->TitlePanel->PositionType = 1;
		//$this->TopPart->SetHeight($topPartHeight);
		$this->BottomPart = new Panel(0, 0, '100%', 50);
		$this->BottomPart->ParentId = $this->Id; 
		$this->BottomPart->PositionType = 1;
		//$this->BottomPart->SetTop($this->TopPart->Bottom);
		$this->SetWidth('100%');
		$this->Controls = &$this->BottomPart->Controls;
		/*$this->Controls->Add($this->TopPart);
		$this->Controls->Add($this->BottomPart);*/
	}	
	function GetTitlePanel()
	{
		return $this->TitlePanel;
	}
	function SetWidth($width)
	{
		parent::SetWidth($width);
		$this->TitlePanel->SetWidth($width);
	}
	function SetTopPartHeight($topPartHeight)
	{
		$this->TopPart->Height = $topPartHeight;
	}
	function Show()
	{
		parent::Show();
		AddNolohScriptSrc('Accordion.js');
		QueueClientFunction($this, 'SetAccordionPart', array("'$this->Id'", "'{$this->TitlePanel->Id}'", "'{$this->BottomPart->Id}'"));
	}
}
?>