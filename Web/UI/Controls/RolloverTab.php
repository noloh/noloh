<?php
/**
 * @package Web.UI.Controls
 */
class RolloverTab extends Panel
{	
	private $OutTab;
	private $OverTab;
	private $DownTab;
	private $SelectedTab;
	private $Selected;
	
	public $GroupName;
	public $TextObject;
	public $TabPageId;
	
	
	function RolloverTab($text = null, $outTab=null, $selectedTab=null, $left = 0, $top = 0, $width = System::Auto, $height = null)
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
//			$this->SetOutTab(new Tab(NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Win/TabBackLeft.gif', NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Win/TabBackMiddle.gif', NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Win/TabBackRight.gif'));
			$this->SetOutTab(new Tab(NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Std/TabInARndLeft.gif', NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Std/TabInACenter.gif', NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Std/TabInARndRight.gif'));
		else 	
			$this->SetOutTab($outTab);
		if($selectedTab == null)
//			$this->SetSelectedTab(new Tab(NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Win/TabFrontLeft.gif', NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Win/TabFrontMiddle.gif', NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Win/TabFrontRight.gif'));
			$this->SetSelectedTab(new Tab(NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Std/TabActRndLeft.gif', NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Std/TabActCenter.gif', NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Std/TabActRndRight.gif'));
		else
			$this->SetSelectedTab($selectedTab);
		$this->Cursor = Cursor::Arrow;
		$this->SetWidth($width);
		$this->SetHeight($height == null?$this->OutTab->GetHeight():$height);
		if($this->TextObject != null)
			$this->Controls->Add($this->TextObject);
		
	}
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
	function GetText()	{return $this->TextObject->Text;}
	function SetText($text)
	{
		$this->TextObject->Text = $text;
	}
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
	function GetSelected()								{return $this->Selected;}
	function GetSelect()								{return $this->GetEvent('Select');/*$this->Select;*/}
	function SetSelect($newSelect)
	{
		$this->SetEvent($newSelect, 'Select');
		//$this->Select = $newSelect;
	}
	function SetOutTab($outTab)
	{
		if($this->OutTab == null)
		{
			$this->OutTab = $outTab;
			$this->Controls->Add($this->OutTab);
		}
		else
			$this->OutTab = $outTab;
		$this->OutTab->SetWidth($this->Width);
		if(!empty($outTab))
			$this->MouseOut = new ClientEvent("ChangeRolloverTab('{$this->Id}','{$this->OutTab->Id}');");
	}
	function SetOverTab($overTab)
	{
		if($this->OverTab == null)
		{
			$this->OverTab = $overTab;
			$this->Controls->Add($this->OverTab);
		}
		else
			$this->OverTab = $overTab;
		$this->OverTab->SetWidth($this->Width);
		$this->OverTab->ClientVisible = false;
		if(!empty($overTab))
			$this->MouseOver = new ClientEvent("ChangeRolloverTab('{$this->Id}','{$this->OverTab->Id}');");
	}
	function SetDownTab($downTab)
	{
		if($this->DownTab == null)
		{
			$this->DownTab = $downTab;
			$this->Controls->Add($this->DownTab);
		}
		else
			$this->DownTab = $downTab;
		$this->DownTab->SetWidth($this->Width);
		$this->OverTab->ClientVisible = false;
		if(!empty($downTab))
			$this->MouseDown = new ClientEvent("ChangeRolloverTab('{$this->Id}','{$this->DownTab->Id}');");
	}
	function SetSelectedTab($selectedTab)
	{
		if($this->SelectedTab == null)
		{
			$this->SelectedTab = $selectedTab;
			$this->Controls->Add($this->SelectedTab);
		}
		else
			$this->SelectedTab = $selectedTab;
		$this->SelectedTab->SetWidth($this->Width);
		//if($this->ClientSide == true)
			$this->SelectedTab->ClientVisible = false;
		//else
			//$this->SelectedTab->ServerVisible = false;
		//if(!empty($whatSelectedTab) && $this->ClientSide != true)
		/*	if(empty($this->Click))
				$this->Click = new ServerEvent($this, 'SetSelected', true);
			else */
				$this->Click[] = new ServerEvent($this, 'SetSelected', true);
	}
	function SetSelected($bool, $fireEvents = true)
	{
		if(isset($this->GroupName) && $bool)
		{
			//Alert("Inner being called");
			$tempGroup = GetComponentById($this->GroupName);
			$tmpSelectedTab = $tempGroup->SelectedRolloverTab;
			if($tmpSelectedTab === $this)
				return;
			if($tmpSelectedTab != null)
			{
				//Alert("I'm unselecting");
//				Alert($tmpSelectedTab->Id . " & " . $this->Id);
					$tempGroup->SelectedRolloverTab->SetSelected(false);
//				}
//				$tempGroup->SetSelectedRolloverTab($this);
//				//elseif($tempGroup->GetSelectedIndex() == -1)
//			}
			}
		}
		if($bool)
		{
			$sel = $this->GetEvent('Select');
			if(!$sel->Blank()) // && $fireEvents && $this->Click != null)
				$sel->Exec();
		}
		$this->Selected = $bool;
		$notBool = !$bool;
		$this->MouseOut->Enabled = $this->MouseOver->Enabled = $this->Click->Enabled = $notBool;
		$this->OutTab->ClientVisible = $this->OverTab->ClientVisible = $this->DownTab->ClientVisible = $notBool;
		$this->SelectedTab->ClientVisible = $bool;
	}
	function Show()
	{
		$this->TextObject->BringToFront();
		AddNolohScriptSrc('RolloverTab.js');
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/RolloverTab.js");
		//AddScript("SetRolloverTabInitialProperties('{$this->Id}', '{$this->OutTab->Id}', '{$this->SelectedTab->Id}')", Priority::High);
//		QueueClientFunction($this, "SetRolloverTabInitialProperties", "'$this->OutTab->Id'", "'$this->SelectedTab->Id'");
		AddScript("SetRolloverTabInitialProperties('{$this->Id}', '{$this->OutTab->Id}', '{$this->SelectedTab->Id}')");
		//Should it be?
//		QueueClientFunction($this, 'SetRolloverTabInitialProperties', array("'$this->Id'", "'{$this->OutTab->Id}'", "'{$this->SelectedTab->Id}'"));
		parent::Show();
	}
}
?>