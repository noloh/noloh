<?php
/**
 * CollapsePanel
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Extended
 */
class CollapsePanel extends Panel
{
	private $TitlePanel;
	private $BodyPanel;
	private $ToggleButton;
	/*TODO Trigger Events when Expanded and Collapsed. Function to expand or collapse, such as Collapse(), Expand(), Toggle()*/
	
	function CollapsePanel($text='', $left=0, $top=0, $width=200, $height=200, $titleHeight=28)
	{
		parent::Panel($left, $top, null, null);
		$this->TitlePanel = new Panel(0, 0, null, $titleHeight);
		$this->BodyPanel = new Panel(0, 0, null, null);
		$this->TitlePanel->ParentId = $this->Id;
		$this->BodyPanel->ParentId = $this->Id; 
		$this->Controls = &$this->BodyPanel->Controls;
		$this->TitlePanel->Layout = $this->BodyPanel->Layout = Layout::Relative;
		$this->SetTitleBackground();
		$this->SetText($text);
		$this->TitlePanel->Click['Collapse'] = new ClientEvent('_NTglClpsePanel', $this->Id, $this->TitlePanel->Id, $this->BodyPanel->Id);
		$this->SetToggleButton();
		$this->SetWidth($width);
		$this->SetHeight($height);
	}
	/**
	 * Gets the Panel object for the body part of the CollapsePanel. This is the actual Panel that the Controls of CollapsePanel are added to.
	 * @return Panel
	 */
	function GetBodyPanel()	{return $this->BodyPanel;}
	function SetToggleButton($rolloverImage=null)
	{
		if($rolloverImage == null)
		{
			$imagePath = NOLOHConfig::GetNOLOHPath().'Images/Std/arrow_up.png';
			$rolloverImage = new RolloverImage($imagePath, $imagePath, null, 6);
			$rolloverImage->SelectedSrc = NOLOHConfig::GetNOLOHPath().'Images/Std/arrow_down.png';
			
		}
		if($rolloverImage->SelectedSrc != null)
		{
			$rolloverImage->SetSelected(true);
			$this->TitlePanel->Click['Collapse1'] = $rolloverImage->Click['Select'];
			unset($rolloverImage->Click['Select']);
		}
		$rolloverImage->SetTogglesOff(true);
		if($this->ToggleButton != null)
			$this->ToggleButton->ParentId = null;
		$this->ToggleButton = &$rolloverImage;
		$this->ToggleButton->ReflectAxis('x');
//		$this->ToggleButton->SetSelected(true);
		$this->ToggleButton->ParentId = $this->TitlePanel->Id;
	}
	function GetToggleButton()
	{
		return $this->ToggleButton;
	}
	/**
	 * @ignore
	 */
	function SetText($text)
	{
		if(!isset($this->TitlePanel->Controls['Text']))
		{
			$tmpTitleLabel = new Label($text, 0, 0, null, null);
			$tmpTitleLabel->Layout = Layout::Relative;
			$tmpTitleLabel->CSSClass = 'NAccordionText';	
			$this->TitlePanel->Controls['Text'] = $tmpTitleLabel;		
		}
		else
			$this->TitlePanel->Controls['Text']->SetText($text);
	}
	/**
	 * @ignore
	 */
	function GetText()
	{
		return $this->TitlePanel->Controls['Text']->GetText();
	}
	function SetTitleBackground($objectOrColor=null)
	{
		if($objectOrColor != null)
		{
			if(is_object($objectOrColor))
				$tmpGlossy = $objectOrColor;
			else
			{
				$this->TitlePanel->BackColor = $objectOrColor;
				if($this->TitlePanel->Controls['Glossy'] != null)
					unset($this->TitlePanel->Controls['Glossy']);
				return;
			}
		}
		else
			$tmpGlossy = new RolloverImage(NOLOHConfig::GetNOLOHPath().'Images/Std/HeadBlue.gif', NOLOHConfig::GetNOLOHPath().'Images/Std/HeadOrange.gif', 0, 0, '100%', $this->TitlePanel->GetHeight());
		$this->TitlePanel->Controls['Glossy'] = $tmpGlossy;
	}
	/*function SetWidth($width, $toggleMargin = 5)
	{
		parent::SetWidth($width);
		$this->ToggleButton->SetLeft($toggleMargin);
	}*/
	function SetCollapsed($bool)
	{
		$this->ToggleButton->SetSelected(false);
//		Alert($this->ToggleButton->Selected);
//		$this->TitlePanel->Click->Exec();
		QueueClientFunction($this, '_NTglClpsePanel', array('\''.$this->Id.'\'', '\''.$this->TitlePanel->Id.'\'', '\''.$this->BodyPanel->Id.'\'', $bool?'true':'false'));
	}
	/**
	 * @ignore
	 */
	function SetHeight($newHeight)
	{
		parent::SetHeight($newHeight);
		NolohInternal::SetProperty('Hgt', $this->GetHeight(), $this);
	}
	function GetTitleBackground()
	{
		if(isset($this->TitlePanel->Controls['Glossy']))
			return $this->TitlePanel->Controls['Glossy'];
		else
			return $this->TitlePanel->BackColor;
			
	}
	function GetTitlePanel()	{return $this->TitlePanel;}
	/**
	 * @ignore
	 */
	function Show()
	{
        parent::Show();
		AddNolohScriptSrc('CollapsePanel.js');
	}
}
?>