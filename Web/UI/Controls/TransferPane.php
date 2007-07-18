<?php

class TransferPane extends Panel
{
	public $LeftPane;
	public $RightPane;
	public $ToRight;
	public $ToLeft;
	
	function TransferPane($whatLeft=0, $whatTop=0, $whatWidth=300, $whatHeight=100)
	{	
		parent::Panel($whatLeft, $whatTop);
		$this->ToRight = new Button(">", 0,0,25,25);
		$this->ToLeft = new Button("<", 0,0,25,25);
		$this->LeftPane = new ListBox();
		$this->RightPane = new ListBox();
		$this->SetWidth($whatWidth);
		$this->SetHeight($whatHeight);
		$this->ToRight->Click = new ClientEvent("TransferPaneAdd('". $this->LeftPane->Id ."', '". $this->RightPane->Id ."');");
		$this->ToLeft->Click = new ClientEvent("TransferPaneAdd('". $this->RightPane->Id ."', '". $this->LeftPane->Id ."');");
		$this->Controls->Add($this->LeftPane);
		$this->Controls->Add($this->RightPane);
		$this->Controls->Add($this->ToRight);
		$this->Controls->Add($this->ToLeft);
	}

	public function SetWidth($whatWidth)
	{
		parent::SetWidth($whatWidth);
		$this->LeftPane->Left = 0;
		$this->LeftPane->Width = ($this->Width/2)-18;
		$this->ToRight->Left = ($this->LeftPane->Width) +5;
		$this->ToLeft->Left = ($this->LeftPane->Width) +5;
		$this->RightPane->Left = ($this->LeftPane->Width) + $this->ToRight->Width + 10;
		$this->RightPane->Width = ($this->Width/2)-18;
	}
	
	public function SetHeight($whatHeight)
	{
		parent::SetHeight($whatHeight);
		$this->LeftPane->Height = $this->Height;
		$this->ToRight->Top = ($this->Height/2) - $this->ToRight->Height - .025*($this->Height);
		$this->ToLeft->Top = ($this->Height/2)  + .025*($this->Height);
		$this->RightPane->Height = $this->Height;
	}
	
	function Show()
	{
		parent::Show();
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/TransferPaneScripts.js");
	}
}