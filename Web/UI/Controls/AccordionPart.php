<?php
/**
 * @package Web.UI.Controls
 */
class AccordionPart extends Panel
{
	private $TitlePanel;
	private $BodyPanel;
	
	function AccordionPart($title, $titleHeight = 28)
	{
		$this->BodyPanel = new Panel(0, 0, '100%', 50);
		parent::Panel(0, 0, null, null);
//		if($_SESSION['NOLOHIE6'])
//			$this->CSSMargin_Bottom = '-20px';
		//$this->CSSClass = 'NAccordTest';
//		$this->CSSMargin = '0px';
//		$this->CSSMargin_Top = '20px;';
//		$this->CSSPadding = '0px;';
		$this->PositionType = 1;
		$this->TitlePanel = new Panel(0, 0, null, $titleHeight);
//		$this->TitlePanel->CSSClass = 'NAccordionTitle';
//		$tmpGlossy = new Image(NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Std/glossy.png', 0, 0, '100%', $titleHeight);
		$tmpGlossy = new RolloverImage(NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Std/HeadBlue.gif', NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Std/HeadOrange.gif', 0, 0, '100%', $titleHeight);
		$tmpTitleLabel = new Label($title, 0, 0, null, null);
		$tmpTitleLabel->CSSClass = 'NAccordionText';
		$this->TitlePanel->Controls['Glossy'] = $tmpGlossy;
		$this->TitlePanel->Controls['Text'] = $tmpTitleLabel;
		//$this->TitlePanel->Controls->AddRange($tmpGlossy, $tmpTitleLabel);
		$this->TitlePanel->ParentId = $this->Id;
		$this->TitlePanel->PositionType = 1;
		//$this->BodyPanel->CSSMargin = '0px';
		//$this->BodyPanel->CSSPadding = '0px';
		
		$this->BodyPanel->ParentId = $this->Id; 
		$this->BodyPanel->PositionType = 1;
		$this->SetWidth('100%');
		$this->Controls = &$this->BodyPanel->Controls;
	}	
	function SetText($text)
	{
		$this->TitlePanel->Controls['Text']->SetText($text);
	}
	function GetText()
	{
		return $this->TitlePanel->Controls['Text']->GetText();
	}
	function GetTitlePanel()
	{
		return $this->TitlePanel;
	}
	function GetDataBind()
	{
		return $this->GetEvent('DataBind');
	}
	function SetDataBind($newEvent)
	{
		$this->SetEvent($newEvent, 'DataBind');
		$this->BodyPanel->Scroll = new ClientEvent("N_ScrollCheck('{$this->BodyPanel->Id}');");
	}
	function SetScrolling($scrollType)
	{
		$this->BodyPanel->SetScrolling($scrollType);
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
		QueueClientFunction($this, 'SetAccordionPart', array("'$this->Id'", "'{$this->TitlePanel->Id}'", "'{$this->BodyPanel->Id}'"));
	}
}
?>