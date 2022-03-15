<?php
/**
 * @ignore
 */
class TransferPanel extends Panel
{
	public $LeftPanel;
	public $RightPanel;
	public $ToRight;
	public $ToLeft;
	
	function __construct($left=0, $top=0, $width=300, $height=100)
	{	
		parent::__construct($left, $top);
		$this->ToRight = new Button('>', 0,0,25,25);
		$this->ToLeft = new Button('<', 0,0,25,25);
		$this->LeftPanel = new ListBox();
		$this->RightPanel = new ListBox();
		$this->SetWidth($width);
		$this->SetHeight($height);
		$this->ToRight->Click = new ClientEvent("_NTPTransfer('". $this->LeftPanel->Id ."', '". $this->RightPanel->Id ."');");
		$this->ToLeft->Click = new ClientEvent("_NTPTransfer('". $this->RightPanel->Id ."', '". $this->LeftPanel->Id ."');");
		$this->Controls->Add($this->LeftPanel);
		$this->Controls->Add($this->RightPanel);
		$this->Controls->Add($this->ToRight);
		$this->Controls->Add($this->ToLeft);
	}
	/**
	 * @ignore
	 */
	public function SetWidth($width)
	{
		parent::SetWidth($width);
		$this->LeftPanel->Left = 0;
		$this->LeftPanel->Width = ($this->Width/2)-18;
		$this->ToRight->Left = ($this->LeftPanel->Width) +5;
		$this->ToLeft->Left = ($this->LeftPanel->Width) +5;
		$this->RightPanel->Left = ($this->LeftPanel->Width) + $this->ToRight->Width + 10;
		$this->RightPanel->Width = ($this->Width/2)-18;
	}
	/**
	 * @ignore
	 */
	public function SetHeight($height)
	{
		parent::SetHeight($height);
		$this->LeftPanel->Height = $this->Height;
		$this->ToRight->Top = ($this->Height/2) - $this->ToRight->Height - .025*($this->Height);
		$this->ToLeft->Top = ($this->Height/2)  + .025*($this->Height);
		$this->RightPanel->Height = $this->Height;
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		parent::Show();
		ClientScript::AddNOLOHSource('TransferPanel.js');
	}
}