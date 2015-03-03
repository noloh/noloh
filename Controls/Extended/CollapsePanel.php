<?php
/**
 * CollapsePanel
 *
 * A CollapsePanel is a Panel that can expand or collapse its body section. This is usually initiated by clicking on
 * the title part of the CollapsePanel. A CollapsePanel also has a RolloverImage located in its Title whose state corresponds
 * to whether the CollapsePanel is expanded or collapsed.
 *
 * @package Controls/Extended
 */
class CollapsePanel extends Panel implements Groupable
{
	/**
	* @ignore
	*/
	static $_InTitle = 'TitleHandler';
	/**
	 * @ignore
	 */
	protected $Title;
	/**
	 * @ignore
	 */
	protected $BodyPanel;
	/**
	 * @ignore
	 */
	protected $ToggleButton;
	private $TogglesOff;

	/*TODO Trigger Events when Expanded and Collapsed. Function to expand or collapse, such as Collapse(), Expand(), Toggle()*/
	/**
	 * Constructor
	 *
	 * @param string $text The text displayed in the Title
     * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 * @param integer $titleHeight The height of the Title
	 */
	function CollapsePanel($text='', $left=0, $top=0, $width=200, $height=200, $titleHeight=28)
	{
		$this->BodyPanel = new Panel(0, 0, '100%', null);
		parent::Panel($left, $top, null, null);
		parent::SetScrolling(false);
		$this->SetSelected(true);
		
		$select = parent::GetSelect();
		$select['User'] = new Event();
		$select['System'] = new Event();
		$deselect = parent::GetDeselect();
		$deselect['User'] = new Event();
		$deselect['System'] = new Event();

		$this->SetTogglesOff(true);
		$this->Title = new Panel(0, 0, null, $titleHeight);
		$this->Title->ParentId = $this->Id;
		$this->BodyPanel->ParentId = $this->Id;
		$this->Controls = &$this->BodyPanel->Controls;
		//$this->BodyPanel->Shifts[] = Shift::With($this, Shift::Height);
		$this->Title->Layout = $this->BodyPanel->Layout = Layout::Relative;
		$this->Title->BackObject = new RolloverImage(
			System::ImagePath() . 'Std/HeadBlue.gif', 
			System::ImagePath() . 'Std/HeadOrange.gif', 0, 0, '100%', $this->Title->GetHeight());

		$this->SetText($text);
		$this->SetToggleButton();
		$this->Title->Click['Collapse'] = new ClientEvent("_NSet('{$this->Id}','Selected', _N('{$this->Id}').Tgl?_N('{$this->Id}').Selected!=true:true);");
		$select['System']['Collapse'] = new ClientEvent("_NClpsPnlTgl('$this->Id');");
		$deselect['System']['Collapse'] = new ClientEvent("_NClpsPnlTgl('$this->Id', true);");
		$this->SetWidth($width);
		$this->SetHeight($height);
//		$this->Shifts[] = Shift::SizeWith($this->BodyPanel, Shift::Size, null, null, null, null);
		NolohInternal::SetProperty('Top', $this->Title->Id, $this);
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
//			$deselect['System'][] = $select['System'][] = new ClientEvent("_NSet('{$rolloverImage->Id}','Selected',  _N('{$this->Id}').Selected==true);");
			//unset($select['System']['Button']);
			//unset($deselect['System']['Button']);
			$select['System']['Button'] = new ClientEvent("_NSet('{$rolloverImage->Id}','Selected',  _N('{$this->Id}').Selected==true);");
			$deselect['System']['Button'] = new ClientEvent("_NSet('{$rolloverImage->Id}','Selected',  _N('{$this->Id}').Selected==true);");
//			$select['System'][] = new ClientEvent("console.log(_N('{$this->Id}').Selected); _NSet('{$rolloverImage->Id}','Selected',  true);");
//			$deselect['System'][] = new ClientEvent("console.log(_N('{$this->Id}').Selected); _NSet('{$rolloverImage->Id}','Selected',  false);");
		}
		$rolloverImage->Click->Enabled = false;

		if($this->ToggleButton != null)
			$this->ToggleButton->ParentId = null;
		$this->ToggleButton = &$rolloverImage;
		$this->ToggleButton->ReflectAxis('x');
		$this->ToggleButton->ParentId = $this->Title->Id;
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
		if(isset($this->Title->Controls['Text']))
		{
			if($text instanceof Control)
			{	
				$this->Title->Controls['Text']->Leave();
				$this->Title->Controls['Text'] = $text;
			}
			else
				$this->Title->Controls['Text']->SetText($text);
		}
		else
		{
			if($text instanceof Control)
				$title = $text;
			else
			{
				$title = new Label($text, 0, 0, null, null);
				$title->Layout = Layout::Relative;
				$title->CSSClass = 'NAccordionText';
			}
			$this->Title->Controls['Text'] = $title;
		}
	}
	/**
	 * @ignore
	 */
	function GetText()
	{
		return $this->Title->Controls['Text']->GetText();
	}
	/**
	 * Sets the Background of the Title to a color an an object.
	 * @param object|string $objectOrColor
	 * @deprecated Use $this->Title->BackColor or $this->Title->BackObject instead
	 */
	function SetTitleBackground($objectOrColor=null)
	{
		if(is_object($objectOrColor))
		{
			$this->Title->BackObject = $objectOrColor;
		}
		else
		{
			$this->Title->BackColor = $objectOrColor;
		}	
	}
	/**
	* @ignore
	*/
	function TitleHandler()
    {
		$args = func_get_args();
		$invocation = InnerSugar::$Invocation;
		$prop = strtolower(InnerSugar::$Tail);
//		$currentChain = strtolower(array_pop(InnerSugar::$Chain));

		if($invocation == InnerSugar::Set)
		{
			switch($prop)
			{
				case 'backobject':
					$object = $args[0];
//					System::Log('Here', $object);
					if(isset($this->Title->Controls['Back']))
						$this->Title->Controls['Back']->Leave();
					if($object instanceof Control)
					{		
						$this->Title->Controls['Back'] = $object;
						$select = parent::GetSelect();
						$select['System']['Collapse']['BackObj'] 
							= new ClientEvent('_NSet', $object, 'Selected', true);
						$deselect = parent::GetDeselect();
						$deselect['System']['Collapse']['BackObj'] 
							= new ClientEvent('_NSet', $object, 'Selected', false);
					}
					break;
				default: throw new SugarException();
			}
		}
		elseif($invocation == InnerSugar::Get)
		{
			switch($prop)
			{
				case 'backobject':
					if(isset($this->Title->Controls['BackObject']))
						return $this->Title->Controls['BackObject'];
					break;
			}
		}
	/*	elseif($invocation == InnerSugar::Get && isset($this->ScrollerInfo[$prop]))
			return $this->ScrollerInfo[$prop];*/
    }
	/**
	 * Sets whether the CollapsePanel is collapsed or not.
	 *
	 * This is an alias for !Selected
	 * @deprecated Use Selected instead
	 * @param boolean $bool
	 */
	function SetCollapsed($bool)
	{
		//System::Log('SetCollapsed');
//		$this->ToggleButton->SetSelected(!$bool);
//		if(!$this->GetShowStatus())
//			NolohInternal::SetProperty('Animates', 1, $this);
		$this->SetSelected(!$bool);
		//QueueClientFunction($this, '_NClpsPnlTgl', array('\''.$this->Id.'\'', $bool?'true':'false'), true, Priority::Low);
	}
	/**
	 * @ignore
	 */
	function SetSelected($bool)
	{
		parent::SetSelected($bool);
//		System::Log($this->Id, $bool?'Selected':'Not Selected');
		if(!$bool && !$this->GetShowStatus())
		{
//			ClientScript::Set($this, 'InitClpse', 200, '_N');
			ClientScript::Set($this, 'InitClpse', true, null); //Blame Phil
		}
//			else
//			NolohInternal::SetProperty('Animates', 1, $this);
//		$this->SetSelected(!$bool);
		//QueueClientFunction($this, '_NClpsPnlTgl', array('\''.$this->Id.'\'', $bool?'true':'false'), true, Priority::Low);
//		ClientScript::Set($this, 'Selected', true);
	}
	/**
	 * @ignore
	 */
	function SetHeight($height)
	{
		parent::SetHeight($height);
		ClientScript::Queue($this, '_NClpsPnlSetHgt', array($this, $height));
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
	 * Returns the Background of the Title.
	 * @return object|string
	 * @deprecated Use $this->Title->BackColor or $this->Title->BackObject
	 */
	function GetTitleBackground()
	{
//		if(isset($this->Title->Controls['Glossy']))
//			return $this->Title->Controls['Glossy'];
//		else
			return $this->Title->BackColor;

	}
	/**
	* Returns the CollapsePanel's 'Title Panel
	* @return Panel
	*/
	function GetTitle()	{return $this->Title;}
	/**
	*  @deprecated Use Title instead
	* Returns the CollapsePanel's Title
	* @return Panel
	*/
	function GetTitlePanel()	{return $this->GetTitle();}
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
        ClientScript::AddNOLOHSource('Animation.js', true);
        ClientScript::AddNOLOHSource('CollapsePanel.js');
//        ClientScript::AddNOLOHSource('Dimensions.js', true);
	}
}
?>