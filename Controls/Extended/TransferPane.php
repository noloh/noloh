<?php
/**
 * @package Controls/Extended
 */
/**
 * TransferPane class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 */
class TransferPane extends Panel
{
	public $LeftPane;
	public $RightPane;
	public $ToRight;
	public $ToLeft;
	
	function TransferPane($left=0, $top=0, $width=300, $height=100)
	{	
		parent::Panel($left, $top);
		$this->ToRight = new Button('>', 0,0,25,25);
		$this->ToLeft = new Button('<', 0,0,25,25);
		$this->LeftPane = new ListBox();
		$this->RightPane = new ListBox();
		$this->SetWidth($width);
		$this->SetHeight($height);
		$this->ToRight->Click = new ClientEvent("TransferPaneAdd('". $this->LeftPane->Id ."', '". $this->RightPane->Id ."');");
		$this->ToLeft->Click = new ClientEvent("TransferPaneAdd('". $this->RightPane->Id ."', '". $this->LeftPane->Id ."');");
		$this->Controls->Add($this->LeftPane);
		$this->Controls->Add($this->RightPane);
		$this->Controls->Add($this->ToRight);
		$this->Controls->Add($this->ToLeft);
	}

	public function SetWidth($width)
	{
		parent::SetWidth($width);
		$this->LeftPane->Left = 0;
		$this->LeftPane->Width = ($this->Width/2)-18;
		$this->ToRight->Left = ($this->LeftPane->Width) +5;
		$this->ToLeft->Left = ($this->LeftPane->Width) +5;
		$this->RightPane->Left = ($this->LeftPane->Width) + $this->ToRight->Width + 10;
		$this->RightPane->Width = ($this->Width/2)-18;
	}
	
	public function SetHeight($height)
	{
		parent::SetHeight($height);
		$this->LeftPane->Height = $this->Height;
		$this->ToRight->Top = ($this->Height/2) - $this->ToRight->Height - .025*($this->Height);
		$this->ToLeft->Top = ($this->Height/2)  + .025*($this->Height);
		$this->RightPane->Height = $this->Height;
	}
	
	function Show()
	{
		parent::Show();
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/TransferPaneScripts.js");
		AddNolohScriptSrc('TransferPane.js');
	}
}