<?php
/**
 * @package Web.UI.Controls
 */
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
	private $WindowShade;
	private $MaximizeBox;
	private $MinimizeBox;
	private $Menu;
	private $LeftTitle;
	private $RightTitle;
	private $OldHeight;
	
	function WindowPanel($title = 'WindowPanel', $left=0, $top=0, $width=300, $height = 200)
	{
		$this->BodyPanel = new Panel(0, 0, null, null);
		$imagesRoot = NOLOHConfig::GetNOLOHPath().'Images/';
		if(!$_SESSION['_NIE6'])
		{
			$imagesDir = $imagesRoot .'Std/';
			$format = '.png';
		}
		else
		{
			$imagesDir = $imagesRoot .'IE/';
			$format = '.gif';
		}
		$tmpTop = 7;
		$border = 4;
		$this->LeftTitle = new Image($imagesDir.'WinTL' . $format);
		$this->RightTitle = new Image($imagesDir.'WinTR' . $format);
		$this->TitleBar = new Label($title, $this->LeftTitle->Right, 0, null, 34);
		$this->TitleBar->CSSBackground_Image = 'url('. $imagesDir .'WinMid'. $format.')';
		$this->TitleBar->CSSBackground_Repeat = "repeat-x";
		$this->MinimizeImage = new Image($imagesDir.'WinMin' . $format, null, $tmpTop);
//		$this->RestoreImage = new Image($imagesDir.'restore.gif', null, 2);
		$this->CloseImage = new Image($imagesDir.'WinClose' . $format, null, $tmpTop);
		$this->ResizeImage = new Image($imagesRoot.'Std/WinResize.gif', null, null); 

		parent::Panel($left, $top, $width, $height);
		$this->WindowPanelComponents = new ArrayList();
		$this->WindowPanelComponents->ParentId = $this->Id;
		$this->BodyPanel->SetScrolling(System::Auto);
		$this->TitleBar->SetCursor(Cursor::Arrow);
		$this->BodyPanel->SetTop($this->TitleBar->GetBottom());
		$this->SetText($title);
		$this->SetBackColor('white');
		
		$this->TitleBar->CSSClass = 'NWinPanelTitle';

//		$this->MinimizeImage->MouseOver = new ClientEvent("this.src='{$imagesDir}WinMinHover$format';");
//		$this->RestoreImage->MouseOver = new ClientEvent("this.src='{$imagesDir}restoreover.gif';");
		$this->CloseImage->MouseOver = new ClientEvent("this.src='{$imagesDir}WinCloseHover$format';");
		
//		$this->MinimizeImage->MouseDown = new ClientEvent("this.src='{$imagesDir}minimizedown.gif';");
//		$this->RestoreImage->MouseDown = new ClientEvent("this.src='{$imagesDir}restoredown.gif';");
//		$this->CloseImage->MouseDown = new ClientEvent("this.src='{$imagesDir}closedown.gif';");
		
//		$this->MinimizeImage->MouseOut = new ClientEvent("this.src='{$imagesDir}WinMin$format';");
//		$this->RestoreImage->MouseOut = new ClientEvent("this.src='{$imagesDir}restore.gif';");
		$this->CloseImage->MouseOut = new ClientEvent("this.src='{$imagesDir}WinClose$format';");
		
		$this->ResizeImage->Cursor = Cursor::NorthWestResize;
		
		$this->CloseImage->Click['Hide'] = new ClientEvent('NOLOHChange(\''.$this->Id.'\', \'style.visibility\', \'hidden\');');
		$this->CloseImage->Click[] = new ServerEvent($this, 'Close');
		$this->BodyPanel->CSSBorder_Bottom = $border . 'px solid #07254a';
		$this->BodyPanel->CSSBorder_Left = $border . 'px solid #07254a';
		$this->BodyPanel->CSSBorder_Right = $border . 'px solid #07254a';
		/*
		$closeE = new Event();
		$closeE["Hide"] = new ClientEvent("_N('$this->Id').style.visibility='hidden';");
		$closeE = new ServerEvent($this, "Close");
		$this->CloseImage->Click = $closeE;
		*/
		$this->Click = new ClientEvent('BringToFront(\'' . $this->Id . '\');');
		
		$this->TitleBar->Shifts[] = Shift::Location($this);
		$this->ResizeImage->Shifts[] = Shift::Location($this->ResizeImage, 150, null, 62);
		$this->Shifts[] = Shift::With($this->ResizeImage, Shift::Size);
		$this->TitleBar->Shifts[] = Shift::With($this->ResizeImage, Shift::Width);
		$this->RightTitle->Shifts[] = Shift::With($this->ResizeImage, Shift::Left);
		$this->CloseImage->Shifts[] = Shift::With($this->ResizeImage, Shift::Left);
		$this->BodyPanel->Shifts[] = Shift::With($this->ResizeImage, Shift::Size);
		
		/*$this->ResizeImage->Shifts[] = Shift::Size($this);
		$this->ResizeImage->Shifts[] = Shift::Width($this->TitleBar);
//		$this->ResizeImage->Shifts[] = Shift::Left($this->MinimizeImage);
//		$this->ResizeImage->Shifts[] = Shift::Left($this->RestoreImage);
		$this->ResizeImage->Shifts[] = Shift::Left($this->RightTitle);
		$this->ResizeImage->Shifts[] = Shift::Left($this->CloseImage);
		$this->ResizeImage->Shifts[] = Shift::Size($this->BodyPanel);*/
		
		//$this->TitleBar->Shifts[] = Shift::Location($this);
		$this->WindowPanelComponents->Add($this->TitleBar);
		//$this->WindowPanelComponents->Add($this->MinimizeImage);
		//$this->WindowPanelComponents->Add($this->RestoreImage);
		$this->WindowPanelComponents->Add($this->LeftTitle);
		$this->WindowPanelComponents->Add($this->RightTitle);
		$this->WindowPanelComponents->Add($this->CloseImage);
		$this->WindowPanelComponents->Add($this->BodyPanel);
		$this->WindowPanelComponents->Add($this->ResizeImage);
		
		$this->SetWindowShade(true);
	}
	function SetWindowShade($bool)
	{
		$this->WindowShade = $bool;
		if($bool)
		{
//			$this->TitleBar->DoubleClick['WinShade'] = new ClientEvent('SwapWindowPanelShade(' . $this->BodyPanel->Id . ');');
//			$this->TitleBar->DoubleClick['WinShade'] = new ClientEvent("alert('{$this->BodyPanel->Id}');");//SwapWindowPanelShade(' . $this->BodyPanel->Id . ');');
			if(!isset($this->TitleBar->DoubleClick['WinShade']))
				$this->TitleBar->DoubleClick['WinShade'] = new ClientEvent("SwapWindowPanelShade('{$this->Id}','{$this->TitleBar->Id}');");//SwapWindowPanelShade(' . $this->BodyPanel->Id . ');');
			NolohInternal::SetProperty('WinHght', "{$this->GetHeight()}", $this->Id);
		}
		else
			$this->TitleBar->DoubleClick['WinShade'] = null;
	}
	//function GetWindowShade()	{return $this->WindowShade;}
	function SetText($text){$this->TitleBar->SetText($text);}
	function GetText(){return $this->TitleBar->GetText();}
	function GetMenu()	{return $this->Menu;}
	function SetMenu(Menu $mainMenu)
	{
		$this->Menu = $mainMenu;
		$this->Menu->CSSBorder_Left = '4px solid #07254a';
		$this->Menu->CSSBorder_Right = '4px solid #07254a';
		$this->Menu->Width = $this->Width - 8;
		$this->Menu->Left = 0;
		$this->Menu->Top = $this->TitleBar->Bottom;
		$this->ResizeImage->Shifts[] = Shift::Width($this->Menu);
		$this->BodyPanel->Top = $this->Menu->Bottom;
		$this->WindowPanelComponents->Add($mainMenu);
		$this->BodyPanel->Height -= $mainMenu->Height;
	}
	function Close()
	{
		$this->GetParent()->Controls->Remove($this);
	}
	function GetMaximizeBox()
	{
		return $this->MaximizeBox == null;
	}
	function SetMaximizeBox($bool)
	{
		$this->MaximizeBox = $bool ? null : false;
//		$this->RestoreImage->ServerVisible = $bool;
	}
	function GetMinimizeBox()	{return $this->MinimizeBox == null;}
	function SetMinimizeBox($bool)
	{
		$this->MinimizeBox = $bool ? null : false;
		$this->MinimizeImage->Visible = $bool;
	}
	function SetHeight($newHeight)
	{
		parent::SetHeight($newHeight);
		$this->BodyPanel->SetHeight($newHeight - $this->TitleBar->GetHeight() - 4);
		$this->ResizeImage->SetTop($newHeight - 20);
		if($this->WindowShade)
			NolohInternal::SetProperty('WinHght', "{$this->GetHeight()}", $this->BodyPanel);
	}
	function SetWidth($newWidth)
	{
		parent::SetWidth($newWidth);
		$this->BodyPanel->SetWidth($newWidth - 8);
		$this->TitleBar->SetWidth($newWidth - ($this->LeftTitle->GetWidth() << 1));
		$this->RightTitle->Left = $this->TitleBar->Right;
		$this->MinimizeImage->SetLeft($newWidth - 67);
//		$this->RestoreImage->SetLeft($newWidth - 45);
		$this->CloseImage->SetLeft($newWidth - 33);
		$this->ResizeImage->SetLeft($newWidth - 22);
	}
	function SetBackColor($color)
	{
		$this->BodyPanel->BackColor = $color;
	}
	function GetAddId($obj)
	{
		return in_array($obj, $this->WindowPanelComponents->Elements) ? $this->Id : $this->BodyPanel->Id;
	}
	function Show()
	{
        parent::Show();
		AddNolohScriptSrc('WindowPanel.js');
	}
}

?>