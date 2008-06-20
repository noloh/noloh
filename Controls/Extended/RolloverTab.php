<?php
/**
 * RolloverTab class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Extended
 */
class RolloverTab extends Panel implements Groupable
{	
	private $OutTab;
	private $OverTab;
	private $DownTab;
	private $SelectedTab;
	//private $Selected;
	
	private $GroupName;
	public $TextObject;
	
	function RolloverTab($text = null, $outTab=null, $selectedTab=null, $left = 0, $top = 0, $width = System::AutoHtmlTrim, $height = null)
	{
		parent::Panel($left, $top, null, null);
		if($text != null)
		{
			if(is_string($text))
			{
				$this->TextObject = new Label($text, 0, 0, null, null);
				$this->TextObject->CSSClass = 'NRollTab';
			}
			else
			
			{
				$this->TextObject = $text;
				if($this->TextObject instanceof Label)
				{
					$this->TextObject->Width = $this->Width;
					$this->TextObject->Height = $this->Height;
				}
			}
		}
		if($outTab == null)
			$this->SetOutTab(new Tab(NOLOHConfig::GetNOLOHPath().'Images/Std/TabBackLeft.gif', NOLOHConfig::GetNOLOHPath().'Images/Std/TabBackMiddle.gif', NOLOHConfig::GetNOLOHPath().'Images/Std/TabBackRight.gif'));
//			$this->SetOutTab(new Tab(NOLOHConfig::GetNOLOHPath().'Images/Std/TabInARndLeft.gif', NOLOHConfig::GetNOLOHPath().'Images/Std/TabInACenter.gif', NOLOHConfig::GetNOLOHPath().'Images/Std/TabInARndRight.gif'));
		else 	
			$this->SetOutTab($outTab);
		if($selectedTab == null)
			$this->SetSelectedTab(new Tab(NOLOHConfig::GetNOLOHPath().'Images/Std/TabFrontLeft.gif', NOLOHConfig::GetNOLOHPath().'Images/Std/TabFrontMiddle.gif', NOLOHConfig::GetNOLOHPath().'Images/Std/TabFrontRight.gif'));
//			$this->SetSelectedTab(new Tab(NOLOHConfig::GetNOLOHPath().'Images/Std/TabActRndLeft.gif', NOLOHConfig::GetNOLOHPath().'Images/Std/TabActCenter.gif', NOLOHConfig::GetNOLOHPath().'Images/Std/TabActRndRight.gif'));
		else
			$this->SetSelectedTab($selectedTab);
		$this->Cursor = Cursor::Arrow;
		$this->SetWidth($width);
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
		$this->TextObject->SetWidth($width);
	}
	/**
	 * @ignore
	 */
	function GetText()	{return $this->TextObject->Text;}
	/**
	 * @ignore
	 */
	function SetText($text)
	{
		$this->TextObject->Text = $text;
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
	function GetOutTab()								{return $this->OutTab;}
	function GetOverTab()								{return $this->OverTab;}
	function GetDownTab()								{return $this->DownTab;}
	function GetSelectedTab()							{return $this->SelectedTab;}
	
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
			$this->MouseOut['Out'] = new ClientEvent("_NChgRlOvrTb('{$this->Id}','Out');");
		}
	}
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
			$this->MouseOver['Over'] = new ClientEvent("_NChgRlOvrTb('{$this->Id}','Ovr');");
		}
	}
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
			$this->MouseDown['Down'] = new ClientEvent("_NChgRlOvrTb('{$this->Id}','Dwn');");
		}
	}	
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
			$this->Click['Select'] = new ClientEvent("_NChgRlOvrTb('{$this->Id}','Slct');");
		}
	}
	//Select Event Functions
	function GetSelect()				{return $this->GetEvent('Select');}
	function SetSelect($newSelect)		{$this->SetEvent($newSelect, 'Select');}
	//Groupable Functions
	/**
	 * @ignore
	 */
	function GetGroupName()				{return $this->GroupName;}
	/**
	 * @ignore
	 */
	function SetGroupName($groupName)	{$this->GroupName = $groupName;}
	//function GetSelected()				{return $this->Selected != null;}
	function SetSelected($bool)
	{
//		if(is_string($bool))
//			$bool = $bool == 'true'?true:false;			
		//$selected = $bool ? true : null;
		//Alert($selected . ' + ' . $this->Selected);
		//Alert($selected . ' + ' . $this->GetSelected());
		if($this->Selected != $bool)
		{
			parent::SetSelected($bool);
			//Trigger Select Event if $bool is true, i.e. Selected
			if($bool && $this->GroupName != null)
			{
				NolohInternal::SetProperty('Cur', 'Slct', $this);
				$sel = $this->GetSelect();
				if(!$sel->Blank())
					$sel->Exec();
			}
			else
				NolohInternal::SetProperty('Cur', 'Out', $this);
			//$this->Selected = $selected;
			if($bool)
			{
				$this->OutTab->Visible = $this->OverTab->Visible = $this->DownTab->Visible = System::Vacuous;
				$this->SelectedTab->Visible = true;
			}
			else
			{
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
		$this->TextObject->BringToFront();
		parent::Show();
		AddNolohScriptSrc('RolloverTab.js');
		NolohInternal::SetProperty('Cur', 'Out', $this);
	}
}
?>