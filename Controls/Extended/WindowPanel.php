<?php
/**
 * WindowPanel class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Extended
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
	private $ThemeBorder;
	private $BorderSize;
	
	function WindowPanel($title = 'WindowPanel', $left=0, $top=0, $width=300, $height = 200)
	{
		$this->BodyPanel = new Panel(0, 0, null, null);
		$this->SetThemeBorder('4px solid #07254a');
		$imagesRoot = NOLOHConfig::GetNOLOHPath().'Images/';
		if(UserAgent::IsIE6())
		{
			$imagesDir = $imagesRoot .'IE/';
			$format = '.gif';
		}
		else
		{
			$imagesDir = $imagesRoot .'Std/';
			$format = '.png';
		}
		$tmpTop = 7;
			
		$this->LeftTitle = new Image($imagesDir.'WinTL' . $format);
		$this->RightTitle = new Image($imagesDir.'WinTR' . $format);
		$this->TitleBar = new Label($title, $this->LeftTitle->Right, 0, null, 34);
		$this->TitleBar->CSSBackground_Image = 'url('. $imagesDir .'WinMid'. $format.')';
		$this->TitleBar->CSSBackground_Repeat = "repeat-x";
//		$this->RestoreImage = new Image($imagesDir.'restore.gif', null, 2);
		$this->CloseImage = new RolloverImage($imagesDir.'WinClose' . $format, $imagesDir.'WinCloseHover' . $format, null, $tmpTop);
		$this->ResizeImage = new Image($imagesRoot.'Std/WinResize.gif', null, null); 

		parent::Panel($left, $top, $width, $height);
		$this->WindowPanelComponents = new ArrayList();
		$this->WindowPanelComponents->ParentId = $this->Id;
		$this->BodyPanel->SetScrolling(System::Auto);
		$this->TitleBar->SetCursor(Cursor::Arrow);
		$this->SetText($title);
		$this->SetBackColor('white');
		
		$this->TitleBar->CSSClass = 'NWinPanelTitle';
		
		$this->ResizeImage->Cursor = Cursor::NorthWestResize;
		
		$this->CloseImage->Click['Hide'] = new ClientEvent('NOLOHChange(\''.$this->Id.'\', \'style.display\', \'none\');');
		$this->CloseImage->Click[] = new ServerEvent($this, 'Close');
		$this->SetBodyBorder($this->ThemeBorder);
		
		$this->Click = new ClientEvent('BringToFront(\'' . $this->Id . '\');');
		
		$this->TitleBar->Shifts[] = Shift::Location($this);
		$this->ResizeImage->Shifts[] = Shift::Location($this->ResizeImage, 150, null, 62);
		$this->Shifts[] = Shift::With($this->ResizeImage, Shift::Size);
		$this->TitleBar->Shifts[] = Shift::With($this->ResizeImage, Shift::Width);
		$this->RightTitle->Shifts[] = Shift::With($this->ResizeImage, Shift::Left);
		$this->CloseImage->Shifts[] = Shift::With($this->ResizeImage, Shift::Left);
		$this->BodyPanel->Shifts[] = Shift::With($this->ResizeImage, Shift::Size);
		
		$this->WindowPanelComponents->Add($this->TitleBar);
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
			if(!isset($this->TitleBar->DoubleClick['WinShade']))
				$this->TitleBar->DoubleClick['WinShade'] = new ClientEvent("_NTglClpsePanel('{$this->Id}','{$this->TitleBar->Id}', '{$this->BodyPanel->Id}');");
			NolohInternal::SetProperty('Hgt', "{$this->GetHeight()}", $this->Id);
		}
		else
			$this->TitleBar->DoubleClick['WinShade'] = null;
	}
	private function SetBodyBorder($border)
	{
		$this->BodyPanel->CSSBorder_Bottom = $border;
		$this->BodyPanel->CSSBorder_Left = $border;
		$this->BodyPanel->CSSBorder_Right = $border;
		
		if($this->Menu != null)
		{
			$this->Menu->CSSBorder_Left = $border;
			$this->Menu->CSSBorder_Right = $border;
		}
	}
	function Skin($border=null, $corners=null, $buttons=null, $resizeHandle=null)
	{
		if($border != null)
		{
			$this->SetThemeBorder($border); 
			$this->SetBodyBorder($border);
		}
		if(!empty($corners))
		{
			if(isset($corners[0]))
				$this->LeftTitle->SetSrc($corners[0], true);
			if(isset($corners[2]))
				$this->RightTitle->SetSrc($corners[2], true);
			if(isset($corners[1]))
			{
				$this->TitleBar->CSSBackground_Image = 'url('. $corners[1] .')';
				$this->TitleBar->SetHeight($this->LeftTitle->GetHeight());
				if($this->Menu)
					$this->Menu->SetWidth($this->Width - ($this->BorderSize << 1));
			}
		}
		//TODO maximize, minimize, restore
		//close, maximize, minimize, restore
		if(!empty($buttons))
		{
			if(isset($buttons[0]))
			{
				if($buttons[0] instanceof Image)
				{
					$this->CloseImage->SetSrc($buttons[0]->GetSrc(), true);
					if($buttons[0] instanceof RolloverImage)
					{
						$this->CloseImage->SetOverSrc($buttons[0]->GetOverSrc());
						$this->CloseImage->SetDownSrc($buttons[0]->GetDownSrc());
						$this->CloseImage->SetSelectSrc($buttons[0]->GetSelectSrc());
					}
				}
				elseif(is_string($buttons[0]))
					$this->CloseImage->SetSrc($buttons[0], true);
			}
		}
		if($resizeHandle != null)
			$this->ResizeImage->SetSrc($resizeHandle, true);
		$this->SetWidth($this->Width);
		$this->SetHeight($this->Height);
	}
	function GetWindowShade()	{return $this->WindowShade;}
	/**
	 * @ignore
	 */
	function SetText($text){$this->TitleBar->SetText($text);}
	/**
	 * @ignore
	 */
	function GetText(){return $this->TitleBar->GetText();}
	function GetMenu()	{return $this->Menu;}
	function SetMenu(Menu $mainMenu)
	{
		$this->Menu = $mainMenu;
		$this->Menu->CSSBorder_Left = $this->ThemeBorder;
		$this->Menu->CSSBorder_Right = $this->ThemeBorder;
		$this->Menu->SetWidth($this->Width - ($this->BorderSize << 1));
		$this->Menu->SetLeft(0);
		$this->Menu->SetTop($this->TitleBar->Bottom);
		$this->ResizeImage->Shifts[] = Shift::Width($this->Menu);
		$this->BodyPanel->SetTop($this->Menu->Bottom);
		$this->WindowPanelComponents->Add($mainMenu);
		$this->BodyPanel->Height -= $mainMenu->Height;
	}
	function Close()
	{
		$this->GetParent()->Controls->Remove($this);
	}
	function Minimize(){}
	function GetMaximizeBox()
	{
		return $this->MaximizeBox == null;
	}
	function SetMaximizeBox($bool)
	{
		$this->MaximizeBox = $bool ? null : false;
//		$this->RestoreImage->ServerVisible = $bool;
	}
	function GetMinimizable()	{return $this->MinimizeBox == null;}
	function SetMinimizable($bool)
	{
		if(!$this->MinimizeImage)
		{
			$imagesRoot = NOLOHConfig::GetNOLOHPath().'Images/';
			if(UserAgent::IsIE6())
			{
				$imagesDir = $imagesRoot .'IE/';
				$format = '.gif';
			}
			else
			{
				$imagesDir = $imagesRoot .'Std/';
				$format = '.png';
			}
			$this->MinimizeImage = new RolloverImage($imagesDir .'WinMin'.$format, $imagesDir .'WinMinHover'.$format, 35, 7);
			$this->MinimizeImage->ReflectAxis('x');
			$this->MinimizeImage->Click['Minimize'] = new ServerEvent($this, 'Minimize');
			$this->WindowPanelComponents->Add($this->MinimizeImage);
		}
		$this->MinimizeBox = $bool ? null : false;
		$this->MinimizeImage->Visible = $bool;
	}
/*	function SetResizable($bool)
	{
		if(!$this->ResizeImage)
	}
	function GetResizable()
	{
		
	}*/
	/**
	 * @ignore
	 */
	function SetHeight($height)
	{
		parent::SetHeight($height);
		$this->BodyPanel->SetHeight($height - $this->TitleBar->GetHeight() - $this->BorderSize);
		$this->BodyPanel->SetTop($this->TitleBar->GetBottom());
		$this->ResizeImage->SetTop($height - $this->BorderSize - 16);
		if($this->WindowShade)
			NolohInternal::SetProperty('Hgt', '\'' . $this->GetHeight() . '\'', $this);
	}
	/**
	 * @ignore
	 */
	function SetWidth($width)
	{
		parent::SetWidth($width);
		
		$this->BodyPanel->SetWidth($width - ($this->BorderSize << 1));
		$this->TitleBar->SetWidth($width - ($this->LeftTitle->GetWidth() << 1));
		$this->RightTitle->Left = $this->TitleBar->Right;
//		$this->RestoreImage->SetLeft($newWidth - 45);
		$this->CloseImage->SetLeft($width - 33);
		$this->ResizeImage->SetLeft($width - ($this->BorderSize) - 18);
	}
	private function SetThemeBorder($border)
	{
		$this->ThemeBorder = $border;
		if(preg_match('/\A\s*?(\d+)\D*?.*?\z/', $this->ThemeBorder, $values)) 
			$borderSize = $values[0];
		else
			BloodyMurder('Border has no numeric value');
		$this->BorderSize = $borderSize;
	}
	function SetBackColor($color)	{$this->BodyPanel->SetBackColor($color);}
	/**
	 * @ignore
	 */
	function GetAddId($obj)	{return in_array($obj, $this->WindowPanelComponents->Elements, true) ? $this->Id : $this->BodyPanel->Id;}
	/**
	 * @ignore
	 */
	function Show()
	{
        parent::Show();
		AddNolohScriptSrc('CollapsePanel.js');
	}
}

?>