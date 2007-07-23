<?php

class WindowPanel extends Panel
{
	public $TitleBar;
	public $BodyPanel;
	public $MinimizeImage;
	public $RestoreImage;
	public $CloseImage;
	public $ResizeImage;
	public $WindowStyle;
	public $WindowPanelComponents;
	public $WindowShade;
	private $MaximizeBox;
	private $MinimizeBox;
	private $Menu;
	
	function WindowPanel($title = "WindowPanel", $left=0, $top=0, $width=300, $height = 200, $reflectStyle = true)
	{
		$this->BodyPanel = new Panel(0, 0, null, null);
		$this->TitleBar = new Label($title, 0, 0, null, 25);
		$imagesDir = NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/Win/';
		$this->MinimizeImage = new Image($imagesDir.'minimize.gif', null, 2);
		$this->RestoreImage = new Image($imagesDir.'restore.gif', null, 2);
		$this->CloseImage = new Image($imagesDir.'close.gif', null, 2);
		$this->ResizeImage = new Image($imagesDir.'resizecornerclear.gif', null, null); 
		//$this->ResizeImage = new Image($imagesDir.'resizecornerclear.gif', $this->Width-17, $this->Height - 16); 

		parent::Panel($left, $top, $width, $height);
		$this->WindowPanelComponents = new ArrayList();
		$this->WindowPanelComponents->ParentId = $this->Id;
		//$this->BodyPanel->SetWidth($width);
		$this->BodyPanel->SetScrolling(System::Auto);
		//$this->BodyPanel->AutoScroll = true;
		//$this->Controls = &$this->BodyPanel->Controls;
		//$this->BodyPanel->Controls = $this->Controls;
		//$this->TitleBar->SetHeight(25);
		$this->TitleBar->SetCursor(Cursor::Arrow);
		$this->BodyPanel->SetTop($this->TitleBar->GetBottom());
		$this->SetText($title);
		//$this->TitleBar->SetText($title);
		$this->SelectFix = true;
		//$this->TitleBar->SetWidth($width);
		//$this->DropShadow = true;
		$this->BackColor = "white";
		
//		if (GetOperatingSystem() == "mac" && GetGlobal("ReflectOS") == true)
//		{
//			$this->TitleBar->Color = "Black";
//			$this->TitleBar->align ="center";
//			$this->TitleBar->LeftPadding = 0;
//			$this->TitleBar->BackColor="#f4f4f4";
//			$BorderColor="#f4f4f4";
//			$this->Border = "1px solid  ". $BorderColor;
//			$this->MinimizeImage = new Image(NOLOHConfig::GetNOLOHPath()."Web/UI/Controls/Images/mac/minimize.gif",30,2);
//			$this->RestoreImage = new Image(NOLOHConfig::GetNOLOHPath()."NOLOH/Web/UI/Controls/Images/mac/maximize.gif",50,2);
//			$this->CloseImage = new Image(NOLOHConfig::GetNOLOHPath()."NOLOH/Web/UI/Controls/Images/mac/close.gif", 10,2);
//			$this->ResizeImage = new Image(NOLOHConfig::GetNOLOHPath()."NOLOH/Web/UI/Controls/Images/mac/resizecornerclear.gif", $this->Width-17, $this->Height - 16);
//			$this->WindowStyle = "mac";
//		}
//		else
//		{
			$this->TitleBar->CSSClass = "NWinPanelTitle";
//			$this->TitleBar->Color = "White";
//			$this->TitleBar->LeftPadding = 5;
//			$this->TitleBar->BackColor="#0055ea";
//			$BorderColor="#0055ea";
			$this->Border = "1px solid  #0055ea";//. $BorderColor;
			
//		}
		
		$this->MinimizeImage->MouseOver = new ClientEvent("this.src='{$imagesDir}minimizeover.gif'");
		$this->RestoreImage->MouseOver = new ClientEvent("this.src='{$imagesDir}restoreover.gif'");
		$this->CloseImage->MouseOver = new ClientEvent("this.src='{$imagesDir}closeover.gif'");
		
		$this->MinimizeImage->MouseDown = new ClientEvent("this.src='{$imagesDir}minimizedown.gif'");
		$this->RestoreImage->MouseDown = new ClientEvent("this.src='{$imagesDir}restoredown.gif'");
		$this->CloseImage->MouseDown = new ClientEvent("this.src='{$imagesDir}closedown.gif'");
		
		$this->MinimizeImage->MouseOut = new ClientEvent("this.src='{$imagesDir}minimize.gif'");
		$this->RestoreImage->MouseOut = new ClientEvent("this.src='{$imagesDir}restore.gif'");
		$this->CloseImage->MouseOut = new ClientEvent("this.src='{$imagesDir}close.gif'");
		
		$this->ResizeImage->Cursor = Cursor::NorthWestResize;
		
		$this->CloseImage->Click["Hide"] = new ClientEvent("NOLOHChange('$this->Id', 'style.visibility', 'hidden');");
		$this->CloseImage->Click[] = new ServerEvent($this, "Close");
				
		/*
		$closeE = new Event();
		$closeE["Hide"] = new ClientEvent("document.getElementById('$this->Id').style.visibility='hidden'");
		$closeE = new ServerEvent($this, "Close");
		$this->CloseImage->Click = $closeE;
		*/
		
		/*if($this->WindowShade != false)
		{
			$this->TitleBar->DoubleClick = new ClientEvent("SwapWindowPanelShade('{$this->Id}', '{$this->TitleBar->Id}')");
		}*/
		
		$this->ResizeImage->Shifts[] = Shift::Size($this);
		$this->ResizeImage->Shifts[] = Shift::Width($this->TitleBar);
//		$this->ResizeImage->Shifts[] = Shift::Left($this->MinimizeImage);
//		$this->ResizeImage->Shifts[] = Shift::Left($this->RestoreImage);
		$this->ResizeImage->Shifts[] = Shift::Left($this->CloseImage);
		$this->ResizeImage->Shifts[] = Shift::Size($this->BodyPanel);
		$this->ResizeImage->Shifts[] = Shift::Location($this->ResizeImage);
		$this->TitleBar->Shifts[] = Shift::Location($this);
		$this->WindowPanelComponents->Add($this->TitleBar);
		//$this->WindowPanelComponents->Add($this->MinimizeImage);
		//$this->WindowPanelComponents->Add($this->RestoreImage);
		$this->WindowPanelComponents->Add($this->CloseImage);
		$this->WindowPanelComponents->Add($this->BodyPanel);
		$this->WindowPanelComponents->Add($this->ResizeImage);
	}
	
	function GetMenu()
	{
		return $this->Menu;
	}
	
	function SetMenu(MainMenu $mainMenu)
	{
		$this->Menu = $mainMenu;
		$this->Menu->Width = $this->Width;
		$this->Menu->Left = 0;
		$this->Menu->Top = $this->TitleBar->Bottom + 7;
		$this->ResizeImage->Shifts[] = Shift::Width($this->Menu);
		$this->BodyPanel->Top = $this->Menu->Bottom;
		$this->WindowPanelComponents->Add($mainMenu);
	}
	
	function Close()
	{
		$this->GetParent()->Controls->RemoveItem($this);
	}
	
	function GetMaximizeBox()
	{
		return $this->MaximizeBox == null;
	}
	
	function SetMaximizeBox($bool)
	{
		$this->MaximizeBox = $bool ? null : false;
		$this->RestoreImage->ServerVisible = $bool;
	}
	
	function GetMinimizeBox()
	{
		return $this->MinimizeBox == null;
	}
	
	function SetMinimizeBox($bool)
	{
		$this->MinimizeBox = $bool ? null : false;
		$this->MinimizeImage->ServerVisible = $bool;
	}
	
	function SetHeight($newHeight)
	{
		parent::SetHeight($newHeight);
		$this->BodyPanel->SetHeight($newHeight - 25);
		$this->ResizeImage->SetTop($newHeight - 16);
	}
	
	function SetWidth($newWidth)
	{
		parent::SetWidth($newWidth);
		$this->BodyPanel->SetWidth($newWidth);
		$this->TitleBar->SetWidth($newWidth);
		$this->MinimizeImage->SetLeft($newWidth - 67);
		$this->RestoreImage->SetLeft($newWidth - 45);
		$this->CloseImage->SetLeft($newWidth - 23);
		$this->ResizeImage->SetLeft($newWidth - 17); 
	}
	
	function GetAddId($obj)
	{
		return in_array($obj, $this->WindowPanelComponents->Item) ? $this->Id : $this->BodyPanel->Id;
	}
	
	function Show()
	{
		$initialProperties = $this->GetStyleString();
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/WindowPanelScripts.js");
	
		//if($this->DropShadow == true)
		//{
		//	print(str_repeat("  ", $IndentLevel) . "<DIV ID = '{$this->Id}DS' style='POSITION:absolute; LEFT:".($this->Left + 10)."px; TOP:".($this->Top+10)."px; WIDTH:{$this->Width}px; HEIGHT:{$this->Height}px; background-color:black; filter:alpha(opacity=100)'></DIV>\n");
		//	AddScript("document.getElementById('{$this->Id}').ShiftsWith = '{$this->Id}DS'");
		//}
		NolohInternal::Show("DIV", $initialProperties, $this);
	}
}

?>