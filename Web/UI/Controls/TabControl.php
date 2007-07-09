<?php
class TabControl extends Panel
{
	const Top = "Top";
	const Bottom = "Bottom";
	public $TabPages;
	public $TabControlBar;
	public $TabPagesPanel;
	private $TabAlignment = "Top";
	private $SelectedIndex = -1;
		
	function TabControl($whatLeft = 0, $whatTop = 0, $whatWidth = 500, $whatHeight = 500)
	{
		parent::Panel($whatLeft, $whatTop, $whatWidth, $whatHeight);
		$this->TabControlBar = new Panel(0, 0, $this->Width, 24);
		$this->TabPagesPanel = new Panel(0, $this->TabControlBar->Height, $this->Width, ($this->Height - $this->TabControlBar->Height), $this);
		//$this->TabPagesPanel->CSSLeft_Border = "1px solid #91a7b7";
		//Added this line to Make the TabControl the TabPages Parent;
		//$this->TabPagesPanel->Controls->ParentId = $this->DistinctId;
		$this->TabPages = &$this->TabPagesPanel->Controls;
		//$this->TabPages->SpecialFunction = "AddTabPage";
		$this->TabPages->AddFunctionName = "AddTabPage";
		//$this->TabPages->SpecialObjectId = $this->DistinctId;
		$this->Controls->Add($this->TabControlBar);
		$this->Controls->Add($this->TabPagesPanel);
	}
	public function GetSelectedIndex()	
	{
		return $this->SelectedIndex;
	}
	public function GetSelectedTab()	{return $this->TabPages->Item[$this->SelectedIndex];}
	public function SetSelectedTab($whatTabPage)
	{
		if(is_string($whatTabPage))
			$this->SetSelectedIndex($this->TabControlBar->Controls->IndexOf(GetComponentById($whatTabPage)));
		else 
			$this->SetSelectedIndex($this->TabPages->IndexOf($whatTabPage));
	}
	public function SetSelectedIndex($whatSelectedIndex)
	{
		//print("ahhh");
		//print_r($this->TabControlBar->Controls->Item);
		//print_r($this->TabPagesPanel->Controls);
		if($whatSelectedIndex != $this->SelectedIndex)
		{
			$this->SelectedIndex = $whatSelectedIndex;
			//Need to address the following line, currenty it breaks TabControl - Asher
			//$this->TabControlBar->Controls->Item[$whatSelectedIndex]->SetSelected(true);
			//Why doesn't this work? - Asher, seems to be a priority thing. ---- Urgent
			QueueClientFunction($this, "SetTabPage", array("'$this->DistinctId'", "'{$this->TabControlBar->Controls->Item[$this->SelectedIndex]->DistinctId}'","'{$this->TabPagesPanel->Controls->Item[$this->SelectedIndex]->DistinctId}'"), Priority::Low);
			//AddScript("SetTabPage('{$this->DistinctId}','{$this->TabControlBar->Controls->Item[$this->SelectedIndex]->DistinctId}','{$this->TabPagesPanel->Controls->Item[$this->SelectedIndex]->DistinctId}')", Priority::Low);
			//AddScript("SetTabPage('{$this->DistinctId}', '{$this->TabControlBar->Controls->Item[$this->SelectedIndex]->DistinctId}','{$this->TabPagesPanel->Controls->Item[$this->SelectedIndex]->DistinctId}')");
		}
	}
	public function AddTabPage($whatTabPage)
	{	
		$temp = $whatTabPage->GetRolloverTab();
		$temp->Click = new ClientEvent("SetTabPage('{$this->DistinctId}','{$temp->DistinctId}','{$whatTabPage->DistinctId}');");
		$temp->TabPageId = $whatTabPage->DistinctId;
		if($this->TabControlBar->Controls->Count() < 1)
		{
			$this->TabControlBar->Height = $temp->Height;
			$this->TabPagesPanel->Height = ($this->Height - $this->TabControlBar->Height);
		}
		$temp->Left = (($tmpCount = $this->TabControlBar->Controls->Count()) > 0)?$this->TabControlBar->Controls->Item[$tmpCount -1]->Right:0;
		$this->TabControlBar->Controls->Add($temp);
		$this->TabPagesPanel->Controls->Add($whatTabPage, true, true);
		if($this->TabPages->Count == 1)
			$this->SetSelectedIndex(0);
		//else
		//$whatTabPage->ClientVisible = false;
		$whatTabPage->ClientVisible = "NoDisplay";
	}
	public function GetTabAlignment(){return $this->TabAlignment;}
	public function SetTabAlignment($whatTabAlignment)
	{
		$this->TabAlignment = $whatTabAlignment;
		if($this->TabAlignment == "Top")
		{
			$this->TabControlBar->Left = 0;
			$this->TabControlBar->Top = 0; 
			$this->TabPagesPanel->Top = $this->TabControlBar->Height;
		}
		else if($this->TabAlignment == "Bottom")
		{
			$this->TabControlBar->Left = 0;
			$this->TabControlBar->Top = $this->TabPagesPanel->Height;
			$this->TabPagesPanel->Top = 0;
		}
	}
	function Show()
	{
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/TabControlScripts.js");
		parent::Show();
	}
}
?>
