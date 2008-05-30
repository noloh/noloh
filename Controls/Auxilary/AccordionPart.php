<?php
/**
 * @package Controls/Auxilary
 */
/**
 * AccordianPart class
 * 
 * This class needs a description...
 */
class AccordionPart extends Panel implements Groupable
{
	private $TitlePanel;
	private $BodyPanel;
	
	function AccordionPart($title, $titleHeight = 28)
	{
		$this->BodyPanel = new Panel(0, 0, '100%', 50);
		parent::Panel(0, 0, null, null);
		$this->LayoutType = 1;
		$this->TitlePanel = new Panel(0, 0, null, $titleHeight);
		$tmpGlossy = new RolloverImage(NOLOHConfig::GetNOLOHPath().'Images/Std/HeadBlue.gif', NOLOHConfig::GetNOLOHPath().'Images/Std/HeadOrange.gif', 0, 0, '100%', $titleHeight);
		$tmpTitleLabel = new Label($title, 0, 0, null, null);
		$tmpTitleLabel->CSSClass = 'NAccordionText';
		$this->TitlePanel->Controls['Glossy'] = $tmpGlossy;
		$this->TitlePanel->Controls['Text'] = $tmpTitleLabel;
		//$this->TitlePanel->Controls->AddRange($tmpGlossy, $tmpTitleLabel);
		$this->TitlePanel->ParentId = $this->Id;
		$this->TitlePanel->LayoutType = 1;
		
		$this->BodyPanel->ParentId = $this->Id; 
		$this->BodyPanel->LayoutType = 1;
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
		$this->BodyPanel->Scroll = new ClientEvent("_NScrollCheck('{$this->BodyPanel->Id}');");
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
	//Select Event Functions
	function GetSelect()				{return $this->TitlePanel->Controls['Glossy']->GetSelect();}
	function SetSelect($newSelect)		{$this->TitlePanel->Controls['Glossy']->SetSelect($newSelect);}
	//Groupable Functions
	function GetGroupName()				{return $this->TitlePanel->Controls['Glossy']->GroupName;}
	function SetGroupName($groupName)	
	{
		$this->TitlePanel->Controls['Glossy']->SetGroupName($groupName);
	}
	function GetSelected()				{return $this->$this->TitlePanel->Controls['Glossy']->GetSelected();}
	function SetSelected($bool)
	{			
		$this->TitlePanel->Controls['Glossy']->SetSelected($bool);
	}
	function Show()
	{
		parent::Show();
		AddNolohScriptSrc('Accordion.js');
		QueueClientFunction($this, '_NSetAccordPt', array("'$this->Id'", "'{$this->TitlePanel->Id}'", "'{$this->BodyPanel->Id}'"));
	}
}
?>