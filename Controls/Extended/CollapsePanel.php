<?php
/**
 * CollapsePanel
 *
 * A CollapsePanel is a panel that can expand or collapse it's body section. This is usually initiated by clicking on
 * the title part of the CollapsePanel. A CollapsePanel also has a RolloverImage located in it's TitlePanel whose state corresponds
 * to whether the CollapsePanel is expanded or collapsed.
 * 
 * @package Controls/Extended
 */
class CollapsePanel extends Panel implements Groupable
{
	/**
	 * The title part of a CollapsePanel
	 * @var Panel 
	 */
	protected $TitlePanel;
	/**
	 * The body part of a CollapsePanel
	 * @var Panel
	 */
	protected $BodyPanel;
	/**
	 * The image usually located in the TitlePanel which indicates whether the CollapsePanel is expanded or collapsed.
	 * @var RolloverImage
	 */
	protected $ToggleButton;
	private $TogglesOff;
	
	/*TODO Trigger Events when Expanded and Collapsed. Function to expand or collapse, such as Collapse(), Expand(), Toggle()*/
	/**
	 * Constructor
	 * 
	 * @param string $text The text displayed in the TitlePanel
     * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 * @param integer $titleHeight The height of the TitlePanel
	 */
	function CollapsePanel($text='', $left=0, $top=0, $width=200, $height=200, $titleHeight=28)
	{
		$this->BodyPanel = new Panel(0, 0, '100%', null);
		parent::Panel($left, $top, null, null);
		parent::SetSelected(true);
		parent::SetScrolling(false);
		$select = parent::GetSelect();
		$select['User'] = new Event();
		$select['System'] = new Event();
		$deselect = parent::GetDeselect();
		$deselect['User'] = new Event();
		$deselect['System'] = new Event();
		
		$this->SetTogglesOff(true);
		$this->TitlePanel = new Panel(0, 0, null, $titleHeight);
		$this->TitlePanel->ParentId = $this->Id;
		$this->BodyPanel->ParentId = $this->Id; 
		$this->Controls = &$this->BodyPanel->Controls;
		//$this->BodyPanel->Shifts[] = Shift::With($this, Shift::Height);
		$this->TitlePanel->Layout = $this->BodyPanel->Layout = Layout::Relative;
		$this->SetTitleBackground();
		$this->SetText($text);
		$this->SetToggleButton();
		$this->TitlePanel->Click['Collapse'] = new ClientEvent("_NSetProperty('{$this->Id}','Selected', _N('{$this->Id}').Tgl?_N('{$this->Id}').Selected!=true:true);");
		$select['System']['Collapse'] = new ClientEvent("_NClpsPnlTgl('$this->Id');");
		$deselect['System']['Collapse'] = new ClientEvent("_NClpsPnlTgl('$this->Id', true);");
		$this->SetWidth($width);
		$this->SetHeight($height);
		$this->Shifts[] = Shift::SizeWith($this->BodyPanel);
		NolohInternal::SetProperty('Top', $this->TitlePanel->Id, $this);
		NolohInternal::SetProperty('Body', $this->BodyPanel->Id, $this);
	}
	/**
	 * Gets the Panel object for the body part of the CollapsePanel. This is the actual Panel that the Controls of CollapsePanel are added to.
	 * @return Panel
	 */
	function GetBodyPanel()	{return $this->BodyPanel;}
	/**
	 * Sets the RolloverImage that is used as the ToggleButton
	 * 
	 * @param RolloverImage $rolloverImage
	 */
	function SetToggleButton($rolloverImage=null)
	{
		if($rolloverImage == null)
		{
			$imagePath = System::ImagePath() . 'Std/arrow_up.png';
			$rolloverImage = new RolloverImage($imagePath, $imagePath, 5, 6);
			$rolloverImage->SelectedSrc = System::ImagePath() . 'Std/arrow_down.png';
			
		}
		if($rolloverImage->SelectedSrc != null)
		{
			$rolloverImage->SetTogglesOff(true);
			$rolloverImage->SetSelected($this->Selected);
			$select = parent::GetSelect();
			$deselect = parent::GetDeselect();
//			$deselect['System'][] = $select['System'][] = new ClientEvent("_NSetProperty('{$rolloverImage->Id}','Selected',  _N('{$this->Id}').Selected==true);");
			//unset($select['System']['Button']);
			//unset($deselect['System']['Button']);
			$select['System']['Button'] = new ClientEvent("_NSetProperty('{$rolloverImage->Id}','Selected',  _N('{$this->Id}').Selected==true);");
			$deselect['System']['Button'] = new ClientEvent("_NSetProperty('{$rolloverImage->Id}','Selected',  _N('{$this->Id}').Selected==true);");
//			$select['System'][] = new ClientEvent("console.log(_N('{$this->Id}').Selected); _NSetProperty('{$rolloverImage->Id}','Selected',  true);");
//			$deselect['System'][] = new ClientEvent("console.log(_N('{$this->Id}').Selected); _NSetProperty('{$rolloverImage->Id}','Selected',  false);");
		}
		$rolloverImage->Click->Enabled = false;
		
		if($this->ToggleButton != null)
			$this->ToggleButton->ParentId = null;
		$this->ToggleButton = &$rolloverImage;
		$this->ToggleButton->ReflectAxis('x');
		$this->ToggleButton->ParentId = $this->TitlePanel->Id;
	}
	/**
	 * Returns the RolloverImage that is used as the ToggleButton
	 * @return RolloverImage
	 */
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
	/**
	 * Sets the Background of the TitlePanel to a color an an object.
	 * @param object|string $objectOrColor
	 */
	function SetTitleBackground($objectOrColor=null)
	{
		if($objectOrColor != null)
		{
			if(is_object($objectOrColor))
				$glossy = $objectOrColor;
			else
			{
				$this->TitlePanel->BackColor = $objectOrColor;
				if($this->TitlePanel->Controls['Glossy'] != null)
					unset($this->TitlePanel->Controls['Glossy']);
				return;
			}
		}
		else
			$glossy = new RolloverImage(System::ImagePath() . 'Std/HeadBlue.gif', System::ImagePath() . 'Std/HeadOrange.gif', 0, 0, '100%', $this->TitlePanel->GetHeight());
		$this->TitlePanel->Controls['Glossy'] = $glossy;
	}
	/**
	 * Sets whether the CollapsePanel is collapsed or not.
	 * 
	 * This is an alias for !Selected
	 * @deprecated 
	 * @param boolean $bool
	 */
	function SetCollapsed($bool)
	{
		//System::Log('SetCollapsed');
		$this->ToggleButton->SetSelected(!$bool);
		if(!$this->GetShowStatus())
			NolohInternal::SetProperty('Animates', 1, $this);
		$this->SetSelected(!$bool);
		//QueueClientFunction($this, '_NClpsPnlTgl', array('\''.$this->Id.'\'', $bool?'true':'false'), true, Priority::Low);
	}
	/**
	 * @ignore
	 */
	function SetHeight($height)
	{
		parent::SetHeight($height);
		//if($height > $this->TitlePanel->GetHeight() /*&& $height != null*/)
			QueueClientFunction($this, '_NClpsPnlInHgt', array('\''.$this->Id.'\''/*, true, Priority::Low*/));
		//NolohInternal::SetProperty('Hgt', $height, $this);
	}
	/**
	 * @ignore
	 */
	function GetSelect()
	{
		$select = parent::GetSelect();
		return $select['User'];
	}
	/**
	 * @ignore
	 */
	function SetSelect($event)
	{
		$select = parent::GetSelect();
		$select['User'] = $event;
	}
	/**
	 * @ignore
	 */
	function GetDeselect()
	{
		$deselect = parent::GetDeselect();
		return $deselect['User'];
	}
	/**
	 * @ignore
	 */
	function SetDeselect($event)
	{
		$deselect = parent::GetDeselect();
		$deselect['User'] = $event;
	}
	/**
	 * Sets whether the CollapsePanel can Toggle itself being Selected, or whether something else must be deselected for it to deselect.
	 * 
	 * @param boolean $bool
	 */
	function SetTogglesOff($bool)		{NolohInternal::SetProperty('Tgl', ($this->TogglesOff = $bool), $this);}
	/**
	 * Returns whether the CollapsePanel can Toggle itself being Selected, or whether something else must be deselected for it to deselect.
	 * @return boolean
	 */
	function GetTogglesOff()			{return ($this->TogglesOff==true);}	
	/**
	 * Returns the Background of the TitlePanel.
	 * @return object|string
	 */
	function GetTitleBackground()
	{
		if(isset($this->TitlePanel->Controls['Glossy']))
			return $this->TitlePanel->Controls['Glossy'];
		else
			return $this->TitlePanel->BackColor;
			
	}
	/**
	 * Returns the CollapsePanel's TitlePanel
	 * @return Panel
	 */
	function GetTitlePanel()	{return $this->TitlePanel;}
	/**
	 * @ignore
	 */
	function SetScrolling($scrollType)
	{
		$this->BodyPanel->SetScrolling($scrollType);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
        parent::Show();
		AddNolohScriptSrc('Animation.js', true);
		AddNolohScriptSrc('CollapsePanel.js');
	}
}
?>