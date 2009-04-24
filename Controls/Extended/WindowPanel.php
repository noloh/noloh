<?php
/**
 * WindowPanel class
 *
 * A Control used to display a Window. A WindowPanel has a TitleBar used to display it's text. A WindowPanel can also be dragged, resized and closed.
 *
 * <pre>
 * $window = new WindowPanel('Surfing in the bahamas');
 * //Adds an Image to the BodyPanel of the WindowPanel
 * $window->Controls->Add(new Image('surfing.jpg'));
 * </pre>
 * @package Controls/Extended
 */
class WindowPanel extends Panel
{
	/**
	 * The Label used to display the Text of the WindowPanel
	 * @var Label
	 */
	public $TitleBar;
	/**
	 * The Panel used to display all the Controls of the WindowPanel. All Controls are added to this Panel
	 * @var Panel
	 */
	public $BodyPanel;
	/**
	 * @ignore
	 */
	public $MinimizeImage;
	/**
	 * @ignore
	 */
	public $RestoreImage;
	/**
	 * The RolloverImage used for the Close button of the WindowPanel
	 * @var RolloverImage
	 */
	public $CloseImage;
    /**
	 * The Image used for the resize handle of the WindowPanel
	 * @var Image
	 */
	public $ResizeImage;
	/**
	 * @ignore
	 */
	public $WindowStyle;
	/**
	 * @ignore
	 */
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
	/**
	 * Constructor
	 * 
	 * @param string $title The Text displayed in the TitleBar
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 */
	function WindowPanel($title = 'WindowPanel', $left=0, $top=0, $width=300, $height = 200)
	{
		$this->BodyPanel = new Panel(0, 0, null, null);
		$imagesRoot = System::ImagePath();
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
		
		$topMargin = 7;
			
		$this->LeftTitle = new Image($imagesDir.'WinTL' . $format);
		$this->RightTitle = new Image($imagesDir.'WinTR' . $format);
		$this->TitleBar = new Label($title, $this->LeftTitle->Right, 0, null, 34);
		$this->TitleBar->CSSBackground_Image = 'url('. $imagesDir .'WinMid'. $format.')';
		$this->TitleBar->CSSBackground_Repeat = "repeat-x";
//		$this->RestoreImage = new Image($imagesDir.'restore.gif', null, 2);
		$this->CloseImage = new RolloverImage($imagesDir.'WinClose' . $format, $imagesDir.'WinCloseHover' . $format, null, $topMargin);
		$this->ResizeImage = new Image($imagesRoot.'Std/WinResize.gif', null, null);
		$this->SetThemeBorder('4px solid #07254a');
		parent::Panel($left, $top, $width, $height);
		$this->WindowPanelComponents = new ArrayList();
		$this->WindowPanelComponents->ParentId = $this->Id;
		$this->BodyPanel->SetScrolling(System::Auto);
		$this->TitleBar->SetCursor(Cursor::Arrow);
		$this->SetText($title);
		$this->SetBackColor('white');
		
		$this->TitleBar->CSSClass = 'NWinPanelTitle';
		
		$this->ResizeImage->Cursor = Cursor::NorthWestResize;
		
		$this->CloseImage->Click['Hide'] = new ClientEvent('_NChange(\''.$this->Id.'\', \'style.display\', \'none\');');
		$this->CloseImage->Click[] = new ServerEvent($this, 'Close');
		$this->SetBodyBorder($this->ThemeBorder);
		
		$this->Click = new ClientEvent('BringToFront(\'' . $this->Id . '\');');
		$this->Cursor = Cursor::Arrow;
		
		$this->TitleBar->Shifts[] = Shift::Location($this);
		$this->ResizeImage->Shifts[] = Shift::Size($this, 150, null, 65);
		$this->TitleBar->Shifts[] = Shift::With($this, Shift::Width);
		$this->RightTitle->Shifts[] = Shift::With($this, Shift::Left, Shift::Width);
		$this->CloseImage->Shifts[] = Shift::With($this, Shift::Left, Shift::Width);
//		$this->BodyPanel->Shifts[] = Shift::With($this, Shift::Size);
		$this->BodyPanel->Shifts[] = Shift::With($this, Shift::Width);
		$this->BodyPanel->Shifts[] = Shift::With($this, Shift::Height);
		
		//$this->Shifts[] = Shift::With($this->BodyPanel, Shift::Size);
		
		$this->WindowPanelComponents->Add($this->TitleBar);
		$this->WindowPanelComponents->Add($this->LeftTitle);
		$this->WindowPanelComponents->Add($this->RightTitle);
		$this->WindowPanelComponents->Add($this->CloseImage);
		$this->WindowPanelComponents->Add($this->BodyPanel);
		$this->WindowPanelComponents->Add($this->ResizeImage);
//		$this->ResizeImage->ParentId = $this->BodyPanel->Id;
		
		$this->SetWindowShade(true);
	}
	/**
     * @ignore
	 */
	function SetWindowShade($bool)
	{
		return;
		$this->WindowShade = $bool;
		if($bool)
		{
			AddNolohScriptSrc('Animation.js', true);
			if(!isset($this->TitleBar->DoubleClick['WinShade']))
				$this->TitleBar->DoubleClick['WinShade'] = new ClientEvent("_NClpsPnlTgl('{$this->Id}');");
				//$this->TitleBar->DoubleClick['WinShade'] = new ClientEvent("_NClpsPnlTgl('{$this->Id}','{$this->TitleBar->Id}', '{$this->BodyPanel->Id}');");
			NolohInternal::SetProperty('InHgt', $this->GetInnerHeight(), $this);
			NolohInternal::SetProperty('Body', $this->BodyPanel->Id, $this);
			NolohInternal::SetProperty('Top', $this->TitleBar->Id, $this);
		}
		else
			$this->TitleBar->DoubleClick['WinShade'] = null;
	}
	/**
	 * @ignore
	 */
	function GetWindowShade()	{return $this->WindowShade;}
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
	/**
     * @ignore
	 */
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
						$this->CloseImage->SetSelectedSrc($buttons[0]->GetSelectedSrc());
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
	/**
	 * @ignore
	 */
	function SetText($text){$this->TitleBar->SetText($text);}
	/**
	 * @ignore
	 */
	function GetText(){return $this->TitleBar->GetText();}
	/**
	 * Returns the Menu assigned to the WindowPanel
	 * @return Menu 
	 */
	function GetMenu()	{return $this->Menu;}
	/**
	 * Sets the Menu assigned to the WindowPanel
	 * <pre>
	 * $window = new WindowPanel('People');
	 * $menu = new Menu();
	 * $add = $menu->MenuItems->Add('Add');
	 * $add->MenuItems->Add('Person');
	 * $window->Menu = $menu;
	 * </pre>
	 * @param Menu
	 * @return Menu
	 */
	function SetMenu(Menu $menu)
	{
		$this->Menu = $menu;
		$this->Menu->CSSBorder_Left = $this->ThemeBorder;
		$this->Menu->CSSBorder_Right = $this->ThemeBorder;
		$this->Menu->SetWidth($this->Width - ($this->BorderSize << 1));
		$this->Menu->SetLeft(0);
		$this->Menu->SetTop($this->TitleBar->Bottom);
		$this->ResizeImage->Shifts[] = Shift::Width($this->Menu);
		$this->BodyPanel->SetTop($this->Menu->Bottom);
		$this->WindowPanelComponents->Add($menu);
		$this->BodyPanel->Height -= $menu->Height;
		return $this->Menu;
	}
	/**
	 * Closes the WindowPanel. This removes the WindowPanel from the Controls of it's Parent
	 */
	function Close()
	{
		$this->GetParent()->Controls->Remove($this);
	}
	/**
	 * @ignore
	 */
	function Minimize(){}
	/**
	 * @ignore
	 */
	function GetMaximizeBox()
	{
		return $this->MaximizeBox == null;
	}
	/**
	 * @ignore
	 */
	function SetMaximizeBox($bool)
	{
		$this->MaximizeBox = $bool ? null : false;
//		$this->RestoreImage->ServerVisible = $bool;
	}
	/**
	 * @ignore
	 */
	function GetMinimizable()	{return $this->MinimizeBox == null;}
	/**
	 * @ignore
	 */
	function SetMinimizable($bool)
	{
		if(!$this->MinimizeImage)
		{
			$imagesRoot = System::ImagePath() . '';
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
		//$this->ResizeImage->SetTop($height - $this->BorderSize - 16);
		if($this->WindowShade)
			NolohInternal::SetProperty('Hgt', $height, $this);
	}
	/**
	 * Returns the width of the BodyPanel.
	 * @return mixed 
	 */
	function GetInnerWidth()	{return $this->BodyPanel->GetWidth();}
	/**
	 * Returns the the height of the BodyPanel.
	 * @return mixed 
	 */
	function GetInnerHeight()	{return $this->BodyPanel->GetHeight();}
	/**
	 * @ignore
	 */
	function SetWidth($width)
	{
		parent::SetWidth($width);
		
		$this->BodyPanel->SetWidth($width - ($this->BorderSize << 1));
		$this->TitleBar->SetLeft($this->LeftTitle->GetRight());
		$this->TitleBar->SetWidth($width - ($this->LeftTitle->GetWidth() << 1));
		$this->RightTitle->Left = $this->TitleBar->GetRight();
//		$this->RestoreImage->SetLeft($newWidth - 45);
		$this->CloseImage->SetLeft($width - 33);
		//$this->ResizeImage->SetLeft($width - ($this->BorderSize) - 18);
	}
	private function SetThemeBorder($border)
	{
		$this->ThemeBorder = $border;
		if(preg_match('/\A\s*?(\d+)\D*?.*?\z/', $this->ThemeBorder, $values)) 
			$borderSize = $values[1];
		else
			BloodyMurder('Border has no numeric value');
		$this->BorderSize = $borderSize;
		$this->ResizeImage->Left = $this->ResizeImage->Top = $borderSize;
		$this->ResizeImage->ReflectAxis('x');
		$this->ResizeImage->ReflectAxis('y');
	}
	/**
	 * @ignore
	 */
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
	/**
	 * @ignore
	 */
	function NoScriptShowChildren($indent)
	{
		$bodyId = $this->BodyPanel->Id;
		if(!empty($_SESSION['_NControlQueueDeep'][$this->Id]))
			foreach($_SESSION['_NControlQueueDeep'][$this->Id] as $id => $show)
				if($this->GetAddId(GetComponentById($id)) === $bodyId)
				{
					$_SESSION['_NControlQueueDeep'][$bodyId][$id] = $show;
					unset($_SESSION['_NControlQueueDeep'][$this->Id][$id]);
				}
		parent::NoScriptShowChildren($indent);
	}
}

?>