<?php
/**
 * RolloverTab class
 *
 * A RolloverTab is a Panel that displays text over a an object, usually a Tab, although objects other Objects can also be specified. A RolloverTab is usually used in Group situations and thus has an Out, Over, Down, and Selected states.
 * 
 * The following is an example of RolloverTabs being used within a Group
 * <pre>
 * $group = new Group();
 * $tabOut = new Tab('left.gif', 'center.gif', 'right.gif');
 * $tabSelected = new Tab('leftSelected.gif', 'centerSelected.gif', 'rightSelected.gif');
 * 
 * $home = new RolloverTab('Home', $tabOut, $tabSelected);
 * $about = new RolloverImage('About', $tabOut, $tabSelected, $home->Right);
 * 
 * $group->AddRange($tabOut, $tabSelected);
 * </pre>
 * @package Controls/Extended
 */
class RolloverTab extends Panel implements Groupable
{	
	private $OutTab;
	private $OverTab;
	private $DownTab;
	private $SelectedTab;
	private $Closeable;
	private $CloseObject;
	//private $Selected;
	
	/**
	 * The object used to display your text. This defaults to a Label, but other objects such as a RolloverLabel can be used so that when the RolloverTab's state is changed, the TextObject's state is also changed.
	 * @var $mixed
	 */
	public $TextObject;
	/**
	 * Constructor
	 * @param string|Control $text The objects used to display the RolloverTab's text
	 * @param mixed $outTab The Control displayed during the out state
	 * @param mixed $selectedTab The Control displayed during the selected state
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 */
	function RolloverTab($text = null, $outTab=null, $selectedTab=null, $left = 0, $top = 0, $width = System::AutoHtmlTrim, $height = null)
	{
		parent::Panel($left, $top, null, null);
		$click = parent::GetClick();
		$click['System'] = new Event();
		$click['User'] = new Event();
		$select = parent::GetSelect();
		$select['System'] = new Event();
		$select['User'] = new Event();
		$deselect = parent::GetDeselect();
		$deselect['System'] = new Event();
		$deselect['User'] = new Event();
		
		if($text != null)
		{
			if(is_object($text))
			{
				$this->TextObject = $text;
				//Currently won't work for all Groupable objects, must make sure it has a TogglesOff
				if($this->TextObject instanceof Groupable)
					$this->TextObject->TogglesOff = true;
					
				if($this->TextObject instanceof Label)
				{
					$this->TextObject->Width = $this->Width;
					$this->TextObject->Height = $this->Height;
				}
			}
			else
			{
				$this->TextObject = new Label($text, 0, 0, null, null);
				$this->TextObject->CSSClasses->Add('NRollTab');
			}
		}
		$imagePath = System::ImagePath() . 'Std/';
		$this->SetWidth($width);
		if($outTab == null)
			$this->SetOutTab(new Tab($imagePath . 'TabBackLeft.gif', $imagePath . 'TabBackMiddle.gif', $imagePath . 'TabBackRight.gif'));
//			$this->SetOutTab(new Tab(System::ImagePath() . 'Std/TabInARndLeft.gif', System::ImagePath() . 'Std/TabInACenter.gif', System::ImagePath() . 'Std/TabInARndRight.gif'));
		else 	
			$this->SetOutTab($outTab);
		if($selectedTab == null)
			$this->SetSelectedTab(new Tab($imagePath . 'TabFrontLeft.gif', $imagePath . 'TabFrontMiddle.gif', $imagePath . 'TabFrontRight.gif'));
		else
			$this->SetSelectedTab($selectedTab);
		$this->SetHeight($height == null?$this->OutTab->GetHeight():$height);
		if($this->TextObject != null)
			$this->Controls->Add($this->TextObject);
	}
	/**
	 * @ignore
	 */
	function SetWidth($width)
	{
		if($width == System::Auto || $width == System::AutoHtmlTrim)
		{
			if($this->TextObject != null)
			{
				$this->TextObject->SetWidth($width);
				$width = $this->TextObject->GetWidth() + 10;
			}
		}
		parent::SetWidth($width);
		if($this->OutTab != null)
			$this->OutTab->SetWidth($width);
		if($this->OverTab != null)
			$this->OverTab->SetWidth($width);
		if($this->DownTab != null)
			$this->DownTab->SetWidth($width);
		if($this->SelectedTab != null)
			$this->SelectedTab->SetWidth($width);
		if($this->TextObject != null)
			$this->TextObject->SetWidth($width);
	}
	/**
	 * @ignore
	 */
	function GetText()	{return $this->TextObject->GetText();}
	/**
	 * @ignore
	 */
	function SetText($text)
	{
		$this->TextObject->SetText($text);
	}
	/**
	 * @ignore
	 */
	function SetHeight($height)
	{
		parent::SetHeight($height);
		if($this->OutTab != null)
			$this->OutTab->Height = $height;
		if($this->OverTab != null)
			$this->OverTab->Height = $height;
		if($this->DownTab != null)
			$this->DownTab->Height = $height;
		if($this->SelectedTab != null)
			$this->SelectedTab->Height = $height;
		if($this->TextObject != null)
			$this->TextObject->Height = $height;
		if($this->TextObject != null)
			$this->TextObject->Height = $height;
	}
	/**
     * Returns the Control which is displayed during the Out state
     * @return mixed
	 */
	function GetOutTab()								{return $this->OutTab;}
	/**
     * Returns the Control which is displayed during the Over state
     * @return mixed
	 */
	function GetOverTab()								{return $this->OverTab;}
	/**
     * Returns the Control which is displayed during the Down state
     * @return mixed
	 */
	function GetDownTab()								{return $this->DownTab;}
	/**
     * Returns the Control which is displayed during the Selected state
     * @return mixed
	 */
	function GetSelectedTab()							{return $this->SelectedTab;}
	/**
	 * Sets the Control which is displayed during the Out state
	 * @param mixed $outTab
	 */
	function SetOutTab($outTab)
	{
		if(!empty($outTab))
		{
			if($this->OutTab != null)
				$this->Controls->Remove($this->OutTab);
			$this->OutTab = $outTab;
			$this->Controls->Add($this->OutTab);
			NolohInternal::SetProperty('Out', $this->OutTab->Id, $this);
			$this->OutTab->SetWidth($this->GetWidth());
			$this->MouseOut['Out'] = new ClientEvent("_NRlTbChg('{$this->Id}','Out');");
		}
	}
	/**
	 * Sets the Control which is displayed during the Over state
	 * @param mixed $overTab
	 */
	function SetOverTab($overTab)
	{
		if(!empty($overTab))
		{
			if($this->OverTab != null)
				$this->Controls->Remove($this->OverTab);
			$this->OverTab = $overTab;
			$this->Controls->Add($this->OverTab);
			$this->OverTab->Visible = System::Vacuous;
			NolohInternal::SetProperty('Ovr', $this->OverTab->Id, $this);
			$this->OverTab->SetWidth($this->GetWidth());
			$this->MouseOver['Over'] = new ClientEvent("_NRlTbChg('{$this->Id}','Ovr');");
		}
	}
	/**
	 * Sets the Control which is displayed during the Down state
	 * @param mixed $downTab
	 */
	function SetDownTab($downTab)
	{
		if(!empty($downTab))
		{
			if($this->DownTab != null)
				$this->Controls->Remove($this->DownTab);
			$this->DownTab = $downTab;
			$this->Controls->Add($this->DownTab);
			$this->DownTab->Visible = System::Vacuous;
			NolohInternal::SetProperty('Dwn', $this->DownTab->Id, $this);
			$this->DownTab->SetWidth($this->GetWidth());
			$this->MouseDown['Down'] = new ClientEvent("_NRlTbChg('{$this->Id}','Dwn');");
		}
	}	
	/**
	 * Sets the Control which is displayed during the Selected state
	 * @param mixed $selectedTab
	 */
	function SetSelectedTab($selectedTab)
	{
		if(!empty($selectedTab))
		{
			if($this->SelectedTab != null)
				$this->Controls->Remove($this->SelectedTab);
			$this->SelectedTab = $selectedTab;
			$this->Controls->Add($this->SelectedTab);
			$this->SelectedTab->Visible = System::Vacuous;
			NolohInternal::SetProperty('Slct', $this->SelectedTab->Id, $this);
			$this->SelectedTab->SetWidth($this->GetWidth());
//			$this->Click['Select'] = new ClientEvent('_NRlTbChg('{$this->Id}','Slct');");
			//System::Log('here');
			$click = parent::GetClick();
			$click['System'] = new ClientEvent("_NSetProperty('{$this->Id}','Selected', true);");
			$select = parent::GetSelect();
			$select['System'] = new ClientEvent("_NRlTbChg('{$this->Id}','Slct');");
			if($this->TextObject instanceof Groupable)
				$select['System'][] = new ClientEvent("_NSetProperty('{$this->TextObject->Id}','Selected', true);");
			$deselect = parent::GetDeselect();
			$deselect['System'] = new ClientEvent("_NRlTbChg('{$this->Id}','Out');");
			if($this->TextObject instanceof Groupable)
				$deselect['System'][] = new ClientEvent("_NSetProperty('{$this->TextObject->Id}','Selected', false);");
//			$this->Select = new ClientEvent("_NRlTbChg('{$this->Id}', 'Slct');");
		}
		else
		{
			$click = parent::GetClick();
			if(isset($click['System']))
				$click['System'] = null;
			$select = parent::GetSelect();
			if(isset($select['System']))
				$select['System'] = null;
			$deselect = parent::GetDeselect();
			if(isset($deselect['System']))
				$deselect['System'] = null;
		}
	}
	/**
	* Sets whether this tab is closeable. A value of true will display an x the user can click to remove the Tab.
	* 
	* @param mixed $closeable Whether this RolloverTab is closeable
	* @param mixed $object Optional object for the close.
	*/
	function SetCloseable($closeable, $object=null)
	{
		if($closeable)
		{
			$this->Closeable = true;
			if(!$object && isset($this->CloseObject))
				$this->CloseObject->ParentId = $this->Id;
			else
				$this->SetCloseObject($object);
		}
		else
		{
			$this->Closeable = null;
			if(isset($this->CloseObject))
				$this->CloseObject->ParentId = null;
		}
	}
	/**
	* Sets the object that is used to close the Tab when Closeable is true.
	* 
	* @param mixed $object
	*/
	function SetCloseObject($object)
	{
		if(!$object)
			if(isset($this->CloseObject))
				return;
			else
				$object = new Image(System::ImagePath() . 'smallX.png', 2, 2);
		
		if(isset($this->CloseObject))
			$this->CloseObject->ParentId = null;
			
		$object->ReflectAxis('x');
		$this->CloseObject = $object;
		$this->CloseObject->ParentId = $this->Id;
		$this->CloseObject->Click = new ClientEvent('_NLeave', $this);
		$this->CloseObject->Click->Bubbles = false;
	}
	/**
	* Gets the object that is used to close the Tab when Closeable is true.
	* 
	* @param mixed $object
	*/
	function GetCloseObject()	{return $this->CloseObject;}
	/**
	* Gets whether this tab is closeable. A value of true will display an x the user can click to remove the Tab.
	*/
	function GetCloseable()	{return $this->Closeable === null?false:true;}
	/**
	 * @ignore
	 */
	function GetClick()
	{
//		Control::AddSystemHandler(Event::Click, new ClientEvent())
		$click = parent::GetClick();
		return $click['User'];
	}
	/**
	 * @ignore
	 */
	function SetClick($event)
	{
		$click = parent::GetClick();
		$click['User'] = $event;
	}
	/**
	 * @ignore
	 */
	function GetSelect()
	{
//		Control::AddSystemHandler(Event::Click, new ClientEvent())
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
//		Control::AddSystemHandler(Event::Click, new ClientEvent())
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
	function SetLeave($event)
	{
		parent::SetEvent($event, 'Leave');
//		if(isset($this->CloseObject))
//			$this->CloseObject->Click = $this->GetClose();
	}
	function GetLeave()
	{
		return $this->GetEvent('Leave');
	}
	//Select Event Functions
	//function GetSelect()				{return $this->GetEvent('Select');}
	//function SetSelect($newSelect)		{$this->SetEvent($newSelect, 'Select');}
	//Groupable Functions
	/**
	 * @ignore
	 */
	/*function GetGroupName()				{return $this->GroupName;}
	*//**
	 * @ignore
	 *//*
	function SetGroupName($groupName)	{$this->GroupName = $groupName;}*/
	//function GetSelected()				{return $this->Selected != null;}
	/**
	 * @ignore
	 */
	function SetSelected($bool)
	{
		if($this->GetSelected() != $bool)
		{
			parent::SetSelected($bool);
			//Trigger Select Event if $bool is true, i.e. Selected
			//System::Log($this->GroupName . ' is the groupname');
/*			if($bool && $this->GroupName != null)
			{
				System::Log('SetSelected');
//				NolohInternal::SetProperty('Cur', 'Slct', $this);
				$sel = $this->GetSelect();
				if(!$sel->Blank())
					$sel->Exec();
			}
			else
				NolohInternal::SetProperty('Cur', 'Out', $this);*/
			//$this->Selected = $selected;
			if($bool)
			{
				if($this->OutTab)
					$this->OutTab->Visible =  System::Vacuous;
				if($this->DownTab)
					$this->DownTab->Visible =  System::Vacuous;
				if($this->OverTab)
					$this->OverTab->Visible =  System::Vacuous;
				
				NolohInternal::SetProperty('Cur', 'Slct', $this);
				$this->SelectedTab->Visible = true;
			}
			else
			{
				NolohInternal::SetProperty('Cur', 'Out', $this);
				$this->OutTab->Visible = true;
				$this->SelectedTab->Visible = System::Vacuous;
			}
		}
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		ClientScript::AddNOLOHSource('RolloverTab.js');
		//NolohInternal::SetProperty('Cur', 'Out', $this);
		if($this->TextObject)
			$this->TextObject->BringToFront();
		if($this->CloseObject)
			$this->CloseObject->BringToFront();
		parent::Show();
	}
}
?>