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
	private $TitleBar;
	/**
	 * The Panel used to display all the Controls of the WindowPanel. All Controls are added to this Panel
	 * @var Panel
	 */
	public $BodyPanel;
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
	private $Menu;
	private $BorderSizes;
	private $Corners;
	private $Borders;
	private $Buttons;
	/**
	 * Constructor
	 * 
	 * @param string $title The Text displayed in the TitleBar
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 */
	function __construct($title = 'WindowPanel', $left=0, $top=0, $width=300, $height = 200)
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
		
		parent::__construct($left, $top, null, null);
		$this->WindowPanelComponents = new ArrayList();
		$this->WindowPanelComponents->ParentId = $this->Id;
		$this->ResizeImage = new Image($imagesRoot.'Std/WinResize.gif', null, null);
		$this->SetCorners($imagesDir.'WinTL' . $format, $imagesDir.'WinTR' . $format);
		$this->SetBorder('4px solid #07254a', null, '4px solid #07254a', '4px solid #07254a');
		$this->SetTitleBar(32, 'url('. $imagesDir .'WinMid'. $format.') repeat-x');
		$this->SetButtons(new RolloverImage($imagesDir.'WinClose' . $format, $imagesDir.'WinCloseHover' . $format, null));
		
		$this->SetWidth($width);
		$this->SetHeight($height);	
		$this->BodyPanel->SetScrolling(System::Auto);
		
		$this->SetText($title);
		$this->SetBackColor('white');
				
		$this->ResizeImage->Cursor = Cursor::NorthWestResize;
			
		$this->Click = new ClientEvent('BringToFront(\'' . $this->Id . '\');');
		$this->Cursor = Cursor::Arrow;
		
		$this->TitleBar->Shifts[] = Shift::Location($this);
		$this->ResizeImage->Shifts[] = Shift::Size($this, 150, null, 65);
		$this->TitleBar->Shifts[] = Shift::WidthWith($this);
		$this->BodyPanel->Shifts[] = Shift::SizeWith($this);
		
		$this->WindowPanelComponents->AddRange($this->BodyPanel, $this->ResizeImage);
	}
	/**
	 * Sets the Panel used for the Window's TitleBar
	 * @return Panel 
	 */
	function SetTitleBar($height, $backGround = null)
	{
		if(isset($this->TitleBar) && (!$height instanceof Panel))
			$this->TitleBar->SetHeight($height);
		else
		{
			if($height instanceof Panel)
			{
				if(isset($this->TitleBar))
					$this->WindowPanelComponents->Remove($this->TitleBar);
				$this->TitleBar = $height;
			}
			else
				$this->TitleBar = new Panel(0, 0, 0, $height);
				
			if($this->GetWidth())
				$this->SetWidth($this->GetWidth());	
			
			$this->WindowPanelComponents->Add($this->TitleBar);
			
			$this->TitleBar->SetCursor(Cursor::Move);	
			$this->TitleBar->SendToBack();
			$this->TitleBar->Controls['Title'] = new Label($this->GetText(), 0, 0, '100%', '100%');
			$this->TitleBar->Controls['Title']->CSSClass = 'NWinPanelTitle';
		}	
		if($backGround)
			$this->TitleBar->SetBackColor($backGround);
		if($this->GetHeight())
			$this->SetHeight($this->GetHeight());	
					
		return $this->TitleBar;
	}
	function GetTitleBar(){return $this->TitleBar;}
	/**
     * @ignore
	 */
	function SetWindowShade($bool)
	{
		$this->WindowShade = $bool;
		if($bool)
		{
			ClientScript::AddNOLOHSource('Animation.js', true);
			if(!isset($this->TitleBar->DoubleClick['WinShade']))
				$this->TitleBar->DoubleClick['WinShade'] = new ClientEvent("_NClpsPnlTgl('{$this->Id}');");
				//$this->TitleBar->DoubleClick['WinShade'] = new ClientEvent("_NClpsPnlTgl('{$this->Id}','{$this->TitleBar->Id}', '{$this->BodyPanel->Id}');");
			NolohInternal::SetProperty('Body', $this->BodyPanel->Id, $this);
			NolohInternal::SetProperty('Top', $this->TitleBar->Id, $this);
			ClientScript::Set($this, 'Hgt', $this->Height, null);
		}
		else
			$this->TitleBar->DoubleClick['WinShade'] = null;
	}
	/**
	 * @ignore
	 */
	function GetWindowShade()	{return $this->WindowShade;}
	/**
	 * The Image or RolloverImage used for the Close button of the WindowPanel
	 * @return Image/RolloverImage
	 */
	function GetCloseImage()	{return isset($this->Buttons[0])?$this->Buttons[0]:null;}
	/**
	 * The Image or RolloverImage used for the Minimize button of the WindowPanel
	 * @return Image/RolloverImage
	 */
	function GetMinimizeImage()	{return isset($this->Buttons[1])?$this->Buttons[1]:null;}
	/**
	 * The Image or RolloverImage used for the Maximize button of the WindowPanel
	 * @return Image/RolloverImage
	 */
	function GetMaximizeImage()	{return isset($this->Button[2])?$this->Buttons[2]:null;}
	function SetButtons($close = null, $minimize = null, $maximize = null)
	{
		$args = func_get_args();
		if(is_array($close))
			$args = $close;
		
		$top = 7;
		$left = isset($this->Corners[1])?$this->Corners[1]->GetWidth():0;
		foreach($args as $index => $button)
		{
			if($button)
			{
				if(isset($this->Buttons[$index]))
					$this->WindowPanelComponents->Remove($this->Buttons[$index]);
					
				if(!$button instanceof Image)
				{
					if(is_string($button))
						$button = new Image($button);
				}	
				$button->SetLocation($left, $top);
				$button->ReflectAxis('x');
				$this->Buttons[$index] = $button;
				$this->WindowPanelComponents->Add($button);
				
				if($index === 0)
				{
					$button->Click['Hide'] = new ClientEvent('_NChange', $this, 'style.display', 'none');
					$button->Click[] = new ServerEvent($this, 'Close');
				}
			}
			if(isset($this->Buttons[$index]))
				$left += $this->Buttons[$index]->GetWidth() + 5;	
			
			unset($button);	
		}
//		if($height = $this->GetHeight())
//			$this->SetHeight($height);
	}
	function SetCorners($topLeft=null, $topRight=null, $bottomLeft=null, $bottomRight=null)
	{
		$args = func_get_args();
		if(is_array($topLeft))
			$args = $topLeft;
		
		foreach($args as $index => $corner)
		{
			if(!$corner instanceof Image)
				if(is_string($corner))
					$corner = new Image($corner);
			
			if(isset($this->Corners[$index]))
				$this->WindowPanelComponents->Remove($this->Corners[$index]);
				
			$this->Corners[$index] = $corner;
			if($corner instanceof Image)
			{
				$this->WindowPanelComponents->Add($this->Corners[$index]);
			
				if($index == 1 || $index == 3)
					$corner->ReflectAxis('x');
				
				if($index == 2 || $index == 3)
					$corner->ReflectAxis('y');
			}
		}
	}
	function SetBorder($left=null, $top=null, $right=null, $bottom=null)
	{
		$args = func_get_args();
		if(is_array($left))
			$args = $left;
		
		if(count($args) == 1 && is_string($left))
		{
			foreach($this->Borders as $border)
				$this->WindowPanelComponents->Remove($border);
			$this->Borders = array($left, $left, $left, $left);
			$this->CSSBorderTop = $left;
			$this->BodyPanel->CSSBorderLeft = $this->BodyPanel->CSSBorderRight
			= $this->BodyPanel->CSSBorderBottom = $left;
			$this->SetBorderSizes();
			return;
		}
		$settings = array(0 => array(0, 2, 'Height', 'Left'), 1 => array(0, 1, 'Width', 'Top'),
					   2 => array(1, 3, 'Height', 'Right'), 3 => array(2, 3, 'Width', 'Bottom'));

					
		foreach($args as $index => $border)
		{		
			if(isset($this->Borders[$index]) && $this->Borders[$index] instanceof Image)
				$this->WindowPanelComponents->Remove($this->Borders[$index]);
			$currentProps = $settings[$index];
			
			if($border instanceof Image)
			{
				$this->WindowPanelComponents->Add($border);
				
				$margin = 0;
					
				$dimension = 'Get' . $currentProps[2];
				
				if(isset($this->Corners[$currentProps[0]]))
					$margin += $this->Corners[$currentProps[0]]->$dimension();
					
				$prop = $currentProps[2] == 'Height'?'Top':'Left';
				$border->$prop = $margin;
				
				if(isset($this->Corners[$currentProps[1]]))
					$margin += $this->Corners[$currentProps[1]]->$dimension();
				
				$border->$currentProps[2] = $this->$dimension() - $margin;
				
				if($index == 2)
					$border->ReflectAxis('x');
				elseif($index == 3)
					$border->ReflectAxis('y');
					
				$func = $currentProps[2] . 'With';
				$border->Shifts[] = Shift::$func($this);
				$stringBorder = 0;
			}
			elseif(is_string($border))
				$stringBorder = $border;
		
			$prop = 'CSSBorder' . $currentProps[3];
			if($index == 1)
				$this->$prop = $border;
			else
				$this->BodyPanel->$prop = $border;
			$this->Borders[$index] = $border;
		}
		$this->SetBorderSizes();
		$this->SetMenuBorder();
		if($this->Width)
			$this->SetBodyPanelWidth($this->Width);
		if($this->Height)
			$this->SetBodyPanelHeight($this->Height);
	}
	private function SetBorderSizes()
	{
		$even = true;
		foreach($this->Borders as $index => $border)
		{
			if($border instanceof Image)
			{
				$prop = 'Get' . ($even?'Width':'Height');
				$borderSize = $border->$prop();
			}
			elseif(preg_match('/\A\s*?(\d+)\D*?.*?\z/', $border, $values)) 
				$borderSize = $values[1];
			else
				$borderSize = 0;
			$even = !$even;
			$this->BorderSizes[$index] = $borderSize;
		}
		$this->ResizeImage->Left = $this->BorderSizes[2];
		$this->ResizeImage->Top = $this->BorderSizes[3];
		$this->ResizeImage->ReflectAxis('x');
		$this->ResizeImage->ReflectAxis('y');
	}
	private function SetMenuBorder()
	{
		if($this->Menu != null)
		{
			if(isset($this->Borders[0]) && is_string($this->Borders[0]))
				$this->Menu->CSSBorderLeft = $this->Borders[0];
			if(isset($this->Borders[2]) && is_string($this->Borders[2]))
				$this->Menu->CSSBorderRight = $this->Borders[2];
		}
	}
	/**
     * @ignore
	 */
	function Skin($border=null, $corners=null, $buttons=null, $titleBar = null, $resizeHandle=null)
	{
		if($borders)
			$this->SetCorners($corners);
		if($corners)
			$this->SetBorder($border);
		if($buttons)
			$this->SetButtons($buttons);
		if($titleBar)
			$this->SetTitleBar($titleBar);
			
		if($resizeHandle != null)
			$this->ResizeImage->SetPath($resizeHandle, true);
	}
	/**
	 * @ignore
	 */
	function SetText($text)
	{
		parent::SetText($text);
		if(isset($this->TitleBar->Controls['Title']))
			$this->TitleBar->Controls['Title']->SetText($text);
	}
	/**
	 * @ignore
	 */
//	function GetText(){return $this->TitleBar->GetText();}
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
		if($this->ThemeBorder)
		{
			$this->Menu->CSSBorderLeft = $this->ThemeBorder;
			$this->Menu->CSSBorderRight = $this->ThemeBorder;
			$this->Menu->SetWidth($this->Width - ($this->BorderSize << 1));
		}
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
	function SetWidth($width)
	{
		parent::SetWidth($width);
		if(isset($this->Borders[1]) && $this->Borders[1] instanceof Image)
		{
			$margin = 0;
			if(isset($this->Corners[0]))
				$margin += $this->Corners[0]->GetWidth();
			if(isset($this->Corners[1]))
				$margin += $this->Corners[1]->GetWidth();
			$this->Borders[1]->SetWidth($width - $margin);	
		}
		if(isset($this->Borders[3]) && $this->Borders[3] instanceof Image)
		{
			$margin = 0;
			if(isset($this->Corners[2]))
				$margin += $this->Corners[2]->GetWidth();
			if(isset($this->Corners[3]))
				$margin += $this->Corners[3]->GetWidth();
			$this->Borders[3]->SetWidth($width - $margin);	
		}
		$this->SetBodyPanelWidth($width);
	}
	/**
	 * @ignore
	 */
	function SetHeight($height)
	{
		parent::SetHeight($height);
		if(isset($this->Borders[0]) && $this->Borders[0] instanceof Image)
		{
			$margin = 0;
			if(isset($this->Corners[0]))
				$margin += $this->Corners[0]->GetHeight();
			if(isset($this->Corners[2]))
				$margin += $this->Corners[2]->GetHeight();
			$difference = $height - $margin;
			$this->Borders[0]->SetHeight($difference < 0 ? 0:$difference);	
		}
		if(isset($this->Borders[2]) && $this->Borders[2] instanceof Image)
		{
			$margin = 0;
			if(isset($this->Corners[1]))
				$margin += $this->Corners[1]->GetHeight();
			if(isset($this->Corners[3]))
				$margin += $this->Corners[3]->GetHeight();
			$difference = $height - $margin;
			$this->Borders[2]->SetHeight($difference < 0 ? 0:$difference);		
		}
		$this->SetBodyPanelHeight($height);
		if($this->WindowShade)
			ClientScript::Set($this, 'Hgt', $height, null);
	}
	private function SetBodyPanelWidth($width)
	{
		$this->BodyPanel->SetWidth($width - (($this->BorderSizes[0] + $this->BorderSizes[2])));
		$cornersWidthSum = 0;
		if(isset($this->Corners[0]))
			$cornersWidthSum += $this->Corners[0]->GetWidth();	
	
		$this->TitleBar->SetLeft($cornersWidthSum);
		
		if($this->Borders[0] instanceof Image)
			$this->BodyPanel->SetLeft($cornersWidthSum);
		
//		$this->GetCloseImage()->SetLeft($width - $cornersWidthSum - $this->CloseImage->GetWidth());
		if(isset($this->Corners[1]))
			$cornersWidthSum += $this->Corners[1]->GetWidth();
		
		$this->TitleBar->SetWidth($width - $cornersWidthSum);		
	}
	private function SetBodyPanelHeight($height)
	{
		if(isset($this->BorderSizes[1]))
			$this->TitleBar->SetTop($this->BorderSizes[1]);
		$this->BodyPanel->SetHeight($height - $this->TitleBar->GetHeight() - ($this->BorderSizes[1] + $this->BorderSizes[3]));
		$this->BodyPanel->SetTop($this->TitleBar->GetBottom());	
		if($button = $this->Buttons[0])
			$button->SetTop($this->TitleBar->GetTop() + 5);
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
	function SetInnerWidth($width)	
	{
		$width = $width + $this->BorderSizes[0] + $this->BorderSizes[2];
		$this->SetWidth($width);
	}
	/**
	 * Returns the the height of the BodyPanel.
	 * @return mixed 
	 */
	function SetInnerHeight($height)	
	{
		$height = $height + $this->BorderSizes[1] + $this->BorderSizes[3] + $this->TitleBar->GetHeight();
		$this->SetHeight($height);
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
        ClientScript::AddNOLOHSource('CollapsePanel.js');
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