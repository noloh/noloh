<?php
/** 
 * Control class
 *
 * Control is the base class for most NOLOH controls. They are different from Components in that they have a visual representation
 * on the browser, e.g., location, size, Visible, Opacity, etc... All custom defined controls must extend Control.<br>
 * Controls also have a built-in syntactical sugar for setting all CSS properties, simply by prepending the property with CSS and using
 * PascalCase. For example, if one wishes to set the CSS property margin-left to 2px, one would use $control->CSSMarginLeft = '2px';
 * 
 * @package Controls/Core
 */

abstract class Control extends Component
{
	/**
	* @ignore
	*/
	protected $CSSPropertyArray;
	private $CSSClass;
	private $Opacity;
	private $ZIndex;
	private $Layout;
	private $Enabled;
	private $Left;
	private $Top;
	private $Width;
	private $Height;
	private $Visible;
	private $Border;
	private $BackColor;
	private $Color;
	private $Cursor;
	private $ToolTip;
    private $ContextMenu;
	private $Text;
	private $Selected;
	private $Buoyant;
	private $Shifts;
	
	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends Control
	* @param integer $left The left coordinate of this element
	* @param integer $top The top coordinate of this element
	* @param integer $width The width of this element
	* @param integer $height The height of this element
 	*/
	function Control($left = 0, $top = 0, $width = 0, $height = 0)
	{
		parent::Component();
		if($left !== null)
			$this->SetLeft($left);
		if($top !== null)
			$this->SetTop($top);
		if($width !== null)
			$this->SetWidth($width);
		if($height !== null)
			$this->SetHeight($height);
	}
	/**
	 * @ignore
	 */
	function Bury()
	{
		NolohInternal::Bury($this);
		parent::Bury();
	}
	/**
	 * @ignore
	 */
	function Resurrect()
	{
		NolohInternal::Resurrect($this);
		parent::Resurrect();
	}
	/**
	 * Returns the CSS classes to be used with this Control. If more than one class is used, it is a space-delimitted string.
	 * @return string
	 */
	function GetCSSClass()
	{
		return $this->CSSClass === null ? '' : $this->CSSClass;
	}
	/**
	 * Sets the CSS classes to be used with this Control. If more than one class is used, it is a space-delimitted string.
	 * @param string $class
	 */
	function SetCSSClass($class)
	{
		$this->CSSClass = $class;
		NolohInternal::SetProperty('className', $class, $this);
	}
	/**
	 * Returns the Opacity of this Control in percents, i.e., from 0 to 100.
	 * @return integer
	 */
	function GetOpacity()
	{
		return $this->Opacity === null ? 100 : $this->Opacity;
	}
	/**
	 * Sets the Opacity of this Control in percents, i.e., from 0 to 100.
	 * @return integer
	 */
	function SetOpacity($opacity)
	{
		$this->Opacity = $opacity;
		if(UserAgent::GetBrowser()=='ie')
			NolohInternal::SetProperty('style.filter', "alpha(opacity=$opacity)", $this);
		else
			NolohInternal::SetProperty('style.opacity', $opacity/100, $this);
	}
	/**
	 * Returns the ZIndex of this Control. A higher ZIndex means that this Control will appear on top of other Controls with overlapping location.
	 * @return integer
	 */
	function GetZIndex()
	{
		return $this->ZIndex === null ? 0 : $this->ZIndex;
	}
	/**
	 * Sets the ZIndex of this Control. A higher ZIndex means that this Control will appear on top of other Controls with overlapping location.
	 * @param integer $zIndex
	 */
	function SetZIndex($zIndex)
	{
		if($zIndex > $_SESSION['HighestZIndex'])
			$_SESSION['HighestZIndex'] = $zIndex;
		if($zIndex < $_SESSION['LowestZIndex'])
			$_SESSION['LowestZIndex'] = $zIndex;
		$this->_NSetZIndex($zIndex);
	}
	/**
	 * @ignore
	 */
	function _NSetZIndex($newZIndex)
	{
		$this->ZIndex = $newZIndex;
		NolohInternal::SetProperty('style.zIndex', $newZIndex, $this);
	}
	/**
	 * Returns the Text of this Control. Depending on the specific Control, this can have several different interpretations.
	 * @return string
	 */
	function GetText()
	{
		return $this->Text === null ? '' : $this->Text;
	}
	/**
	 * Sets the Text of this Control. Depending on the specific Control, this can have several different interpretations.
	 * @param string $text
	 */
	function SetText($text)
	{
		$this->Text = $text;
	}
	/**
	 * Returns the Width of this Control. Can be either an integer signifying Width in pixels, or can be a string for percents, e.g., '50%'
	 * @return integer|string
	 */
	function GetWidth() 
	{
		return $this->Width;
	}
	/**
	 * Returns the Width of this Control. Can be either an integer signifying Width in pixels, or can be a string for percents, e.g., '50%'
	 * @param integer|string $width
	 */
	function SetWidth($width)
	{
		$this->Width = $width;
		if(is_numeric($width))
			NolohInternal::SetProperty('style.width', $width.'px', $this);
		elseif(is_numeric(rtrim($width, '%')))
			NolohInternal::SetProperty('style.width', $width, $this);
		elseif(is_null($width))
			NolohInternal::SetProperty('style.width', '', $this);
	}
	/**
	 * Returns the Height of this Control. Can be either an integer signifying Height in pixels, or can be a string for percents, e.g., '50%'
	 * @return integer|string
	 */
	function GetHeight() 
	{
		return $this->Height;
	}
	/**
	 * Sets the Height of this Control. Can be either an integer signifying Height in pixels, or can be a string for percents, e.g., '50%'
	 * @param integer|string $height
	 */
	function SetHeight($height)
	{
		$this->Height = $height;
		if(is_numeric($height))
			NolohInternal::SetProperty('style.height', $height.'px', $this);
		elseif(is_numeric(rtrim($height, '%')))
			NolohInternal::SetProperty('style.height', $height, $this);
		elseif(is_null($height))
			NolohInternal::SetProperty('style.height', '', $this);
	}
	/**
	 * Returns the Left of this Control. Can be either an integer signifying Left in pixels, or can be a string for percents, e.g., '50%'
	 * @return integer|string
	 */
	function GetLeft() 
	{
		return $this->Left;
	}
	/**
	 * Sets the Left of this Control. Can be either an integer signifying Left in pixels, or can be a string for percents, e.g., '50%'
	 * @param integer|string $left
	 */
	function SetLeft($left)
	{
		$this->Left = $left;
		if(is_numeric($left))
			NolohInternal::SetProperty('style.left', $left.'px', $this);
		elseif(is_numeric(rtrim($left, '%')))
			NolohInternal::SetProperty('style.left', $left, $this);
		elseif(is_null($left))
			NolohInternal::SetProperty('style.left', '', $this);
	}
	/**
	 * Returns the Top of this Control. Can be either an integer signifying Top in pixels, or can be a string for percents, e.g., '50%'
	 * @return integer|string
	 */
	function GetTop() 
	{
		return $this->Top;
	}
	/**
	 * Sets the Top of this Control. Can be either an integer signifying Top in pixels, or can be a string for percents, e.g., '50%'
	 * @param integer|string $top
	 */
	function SetTop($top)
	{
		$this->Top = $top;
		if(is_numeric($top))
			NolohInternal::SetProperty('style.top', $top.'px', $this);
		elseif(is_numeric(rtrim($top, '%')))
			NolohInternal::SetProperty('style.top', $top, $this);
		elseif(is_null($top))
			NolohInternal::SetProperty('style.top', '', $this);
	}
	/**
	 * Returns the Bottom coordinate of this Control, in pixels, but only if both the Top and Height were integers.
	 * @return integer
	 */
	function GetBottom()
	{
		return $this->GetTop() + $this->GetHeight();
	}
	/**
	 * Returns the Right coordinate of this Control, in pixels, but only if both the Left and Width were integers.
	 * @return integer
	 */
	function GetRight()
	{
		return $this->GetLeft() + $this->GetWidth();
	}
	/**
	 * Returns the Layout type of this Control. The Default is Layout::Absolute, but other possible values are 
	 * Layout::Relative and Layout::Web (which is the equivalent to CSS static).
	 * @return mixed
	 */
	function GetLayout()
	{
		return $this->Layout === null ? 0 : $this->Layout;
	}
	/**
	 * Sets the Layout type of this Control. The Default is Layout::Absolute, but other possible values are 
	 * Layout::Relative and Layout::Web (which is the equivalent to CSS static).
	 * @param mixed 
	 */
	function SetLayout($layout)
	{
		if(is_numeric($layout))
		{
			switch($layout)
			{
				case 0: $printAs = 'absolute'; break;
				case 1: $printAs = 'relative'; break;
				case 2: $printAs = 'static';
			}
			NolohInternal::SetProperty('style.position', $printAs, $this);
			if(is_string($this->Layout))
				NolohInternal::SetProperty('style.float', '', $this);
		}
		else
		{
			NolohInternal::SetProperty('style.float', $layout, $this);
			if(is_numeric($this->GetLayout()))
				NolohInternal::SetProperty('style.position', 'relative', $this);
		}
		$this->Layout = $layout === 0 ? null : $layout;
	}
	/**
	 * Reflects either the x or y axes. Once the x-axis has been reflected, Left will correspond to Right, and similarly for Top.
	 * @param string $axis Either the string 'x' or 'y'
	 * @param boolean $on
	 */
	function ReflectAxis($axis, $on=true)
	{
		if(strtolower($axis == 'x'))
		{
			if($on)
			{
				NolohInternal::SetProperty('style.left', '', $this);
				if(is_numeric($this->Left))
					NolohInternal::SetProperty('style.right', $this->Left.'px', $this);
				elseif(is_numeric(rtrim($this->Left, '%')))
					NolohInternal::SetProperty('style.right', $this->Left, $this);
			}
			else
			{
				NolohInternal::SetProperty('style.right', '', $this);
				$this->SetLeft($this->Left);
			}
		}
		elseif(strtolower($axis == 'y'))
		{
			if($on)
			{
				NolohInternal::SetProperty('style.top', '', $this);
				if(is_numeric($this->Top))
					NolohInternal::SetProperty('style.bottom', $this->Top.'px', $this);
				elseif(is_numeric(rtrim($this->Top, '%')))
					NolohInternal::SetProperty('style.bottom', $this->Top, $this);
			}
			else
			{
				NolohInternal::SetProperty('style.bottom', '', $this);
				$this->SetTop($this->Top);
			}
		}
	}
	/**
	 * Returns whether the Control is Enabled. The Events for Disabled Controls will not launch, as well as several other features being
	 * disabled, depending on the specific kind of Control. For instance, one cannot type into a disabled TextBox.
	 * @return boolean
	 */
	function GetEnabled()
	{
		return $this->Enabled === null;
	}
	/**
	 * Sets whether the Control is Enabled. The Events for Disabled Controls will not launch, as well as several other features being
	 * disabled, depending on the specific kind of Control. For instance, one cannot type into a disabled TextBox.
	 * @param boolean $bool
	 */
	function SetEnabled($bool)
	{
		$this->Enabled = $bool ? null : false;
		NolohInternal::SetProperty('disabled', !$bool, $this);
	}
	/**
	 * Returns whether the Control is Visible. Can be either a boolean value or System::Vacuous. The difference between false and
	 * System::Vacuous only comes into play when a Layout::Web is used. Invisible Controls still take up space, whereas Vacuous
	 * Controls do not.
	 * @return mixed
	 */
	function GetVisible()
	{
		return $this->Visible === null ? true : $this->Visible;
	}
	/**
	 * Sets whether the Control is Visible. Can be either a boolean value or System::Vacuous. The difference between false and
	 * System::Vacuous only comes into play when a Layout::Web is used. Invisible Controls still take up space, whereas Vacuous
	 * Controls do not.
	 * @param mixed $visibility
	 */
	function SetVisible($visibility)
	{
		if($visibility === null || $visibility === 'null')
		{
			$this->Visible = 0;
			NolohInternal::SetProperty('style.display', 'none', $this);
		}
		else//if(is_bool($visibility))
		{
			NolohInternal::SetProperty('style.display', '', $this);
			if($visibility===true || $visibility==='true')
			{
				$this->Visible = null;
				NolohInternal::SetProperty('style.visibility', 'inherit', $this);
			}
			else
			{
				$this->Visible = false;
				NolohInternal::SetProperty('style.visibility', 'hidden', $this);
			}
		}
	}
	/**
	 * Returns the border of this Control. Can be either an integer representing the number of pixels of thickness, or a string
	 * of the size, type, and color of the border, e.g., '2px solid red'. For integers, solid black is always used.
	 * @return integer|string
	 */
	function GetBorder()
	{
		return $this->Border;
	}
	/**
	 * Sets the border of this Control. Can be either an integer representing the number of pixels of thickness, or a string
	 * of the size, type, and color of the border, e.g., '2px solid red'. For integers, solid black is always used.
	 * @param integer|string $border
	 */
	function SetBorder($border)
	{
		$this->Border = $border;
		NolohInternal::SetProperty('style.border', is_numeric($border)?($border.'px solid black'):$border, $this);
	}
	/**
	 * Returns the background color of the Control. Can be either a string of hex like '#FF0000' or the name of a color like 'red'
	 * @return string
	 */
	function GetBackColor()
	{
		return $this->BackColor;
	}
	/**
	 * Sets the background color of the Control. Can be either a string of hex like '#FF0000' or the name of a color like 'red'
	 * @param string $backColor
	 */
	function SetBackColor($backColor)
	{
		$this->BackColor = $backColor;
		NolohInternal::SetProperty('style.background', $backColor, $this);
	}
	/**
	 * Sets the color of the Control. Can be either a string of hex like '#FF0000' or the name of a color like 'red'. Depending on
	 * the specific type of Control, this can have a variety of interpretations.
	 * @return string
	 */
	function GetColor()
	{
		return $this->Color;
	}
	/**
	 * Sets the color of the Control. Can be either a string of hex like '#FF0000' or the name of a color like 'red'. Depending on
	 * the specific type of Control, this can have a variety of interpretations.
	 * @param string $color
	 */
	function SetColor($color)
	{
		$this->Color = $color;
		NolohInternal::SetProperty('style.color', $color, $this);
	}
	/**
	 * Returns the mouse cursor when it is over the Control. Should be a constant or static of the Cursor class.
	 * @return mixed
	 */
	function GetCursor()
	{
		return $this->Cursor == null ? Cursor::Arrow : $this->Cursor;
	}
	/**
	 * Sets the mouse cursor when it is over the Control. Should be a constant or static of the Cursor class.
	 * @param mixed $cursor
	 */
	function SetCursor($cursor)
	{
		$this->Cursor = $cursor == Cursor::Arrow ? null : $cursor;
		NolohInternal::SetProperty('style.cursor', $cursor, $this);
	}
	/**
	 * Returns the ToolTip of the Control, a little caption displaying a specified string that appears when the user hovers his mouse cursor over the Control.
	 * @return string
	 */
	function GetToolTip()
	{
		return $this->ToolTip;
	}
	/**
	 * Sets the ToolTip of the Control, a little caption displaying a specified string that appears when the user hovers his mouse cursor over the Control.
	 * @param string $toolTip
	 */
	function SetToolTip($toolTip)
	{
		$this->ToolTip = $toolTip;
		NolohInternal::SetProperty('title', $toolTip, $this);
	}
	/**
	 * Returns the ContextMenu of the Control. It is a Menu that appears when the Control is right-clicked.
	 * @return ContextMenu
	 */
    function GetContextMenu()
    {
        return $this->ContextMenu;
    }
	/**
	 * Sets the ContextMenu of the Control. It is a Menu that appears when the Control is right-clicked.
	 * @param ContextMenu $contextMenu
	 */
    function SetContextMenu(ContextMenu $contextMenu)
    {
		$this->ContextMenu = &$contextMenu;
        $contextMenu->SetParentId(WebPage::That()->Id);
		NolohInternal::SetProperty('ContextMenu', $contextMenu->Id, $this);
    }
	/**
	 * Returns whether or not the Control is Buoyant. Buoyant Controls always float to the top, and compete with only other Buoyant
	 * Controls for being on top, based on their ZIndex.
	 * @return boolean
	 */
	function GetBuoyant()
	{
		return $this->Buoyant !== null;
	}
	/**
	 * Sets whether or not the Control is Buoyant. Buoyant Controls always float to the top, and compete with only other Buoyant
	 * Controls for being on top, based on their ZIndex.
	 * @param boolean $bool
	 */
	function SetBuoyant($bool)
	{
		$this->Buoyant = $bool ? true : null;
		/*if($bool)
			$this->Buoyant = true;
		else
		{
			$this->Buoyant = null;
			//QueueClientFunction($this, 'StopBuoyant', array("'$this->Id'"));
		}*/
		if($this->GetShowStatus()===1)
		{
			NolohInternal::Bury($this);
			NolohInternal::Resurrect($this);
			if(!$bool)
				QueueClientFunction($this, 'StopBuoyant', array("'$this->Id'"));
		}
	}
	/**
	 * Returns whether the Control is Selected. This only makes sense in the context of Controls implementing Groupable or
	 * MultiGroupable and Added to a Group.
	 * @return boolean
	 */
	function GetSelected()
	{
		return $this->Selected !== null;
	}
	/**
	 * Sets whether the Control is Selected. This only makes sense in the context of Controls implementing Groupable or
	 * MultiGroupable and Added to a Group.
	 * @param boolean $bool
	 */
	function SetSelected($bool)
	{
		if(!($this instanceof Groupable || $this instanceof MultiGroupable))
			BloodyMurder('Cannot call SetSelected on an object not implementing Groupable or MultiGroupable');
		if($bool != $this->GetSelected())
		{
			NolohInternal::SetProperty('Selected', $bool, $this);
			if($bool && $this->GroupName != null)
				GetComponentById($this->GroupName)->Deselect();
			$this->Selected = $bool ? true : null;
			if(/*$bool && */$this->GroupName != null)
			{
				$group = GetComponentById($this->GroupName);
				if(!$group->Change->Blank())
					$group->Change->Exec();
			}
		}
	}
	/**
	 * @ignore
	 */
	function Set_NText($text)
	{
		$this->Text = str_replace(array('~da~','~dp~'), array('&','+'), $text);
		//$this->Text = str_replace('~da~', '&', $text);
	}
	/**
	 * @ignore
	 */
    function SetParentId($id)
    {
        parent::SetParentId($id);
        if($this->ZIndex == null)
            $this->_NSetZIndex(++$_SESSION['HighestZIndex']);
    }
	/**
	 * Returns the Change Event, which gets launched when significant changes are made to the Control. This can have different
	 * interpretations depending on the specific type of Control.
	 * @return Event
	 */
	function GetChange()							{return $this->GetEvent('Change');}
	/**
	 * Sets the Change Event, which gets launched when significant changes are made to the Control. This can have different
	 * interpretations depending on the specific type of Control.
	 * @param Event $change
	 */
	function SetChange($change)						{$this->SetEvent($change, 'Change');}
	/**
	 * Returns the Click Event, which gets launched when a user clicks on the Control.
	 * @return Event
	 */
	function GetClick()								{return $this->GetEvent('Click');}
	/**
	 * Sets the Click Event, which gets launched when a user clicks on the Control.
	 * @param Event $click
	 */
	function SetClick($click)						{$this->SetEvent($click, 'Click');}
	/**
	 * Returns the DoubleClick Event, which gets launched when a user double-clicks on the Control.
	 * @return Event
	 */
	function GetDoubleClick()						{return $this->GetEvent('DoubleClick');}
	/**
	 * Sets the DoubleClick Event, which gets launched when a user double-clicks on the Control
	 * @param Event $doubleClick
	 */
	function SetDoubleClick($doubleClick)			{$this->SetEvent($doubleClick, 'DoubleClick');}
	/**
	 * Returns the DragCatch Event, which gets launched when a user drags a Control being Shifted into the space
	 * occupying this Control. An array of all the Controls being dragged can be found in the Event::$DragCaught array.
	 * @return Event
	 */
	function GetDragCatch()							{return $this->GetEvent('DragCatch');}
	/**
	 * Sets the DragCatch Event, which gets launched when a user drags a Control being Shifted into the space
	 * occupying this Control. An array of all the Controls being dragged can be found in the Event::$DragCaught array.
	 * @param Event $dragCatch
	 */
	function SetDragCatch($dragCatch)				{$this->SetEvent($dragCatch, 'DragCatch');}
	/**
	 * Returns the Focus Event, which gets launched when a user focuses this Control, e.g., by clicking or tabbing into it
	 * @return Event
	 */
	function GetFocus()								{return $this->GetEvent('Focus');}
	/**
	 * Sets the Focus Event, which gets launched when a user focuses this Control, e.g., by clicking or tabbing into it
	 * @param Event $focus
	 */
	function SetFocus($focus)						{$this->SetEvent($focus, 'Focus');}
	/**
	 * Returns the KeyPress Event, which gets launched when the Control is focused and a user presses a key on his keyboard
	 * @return Event
	 */
	function GetKeyPress()							{return $this->GetEvent('KeyPress');}
	/**
	 * Sets the KeyPress Event, which gets launched when the Control is focused and a user presses a key on his keyboard
	 * @param Event $keyPress
	 */
	function SetKeyPress($keyPress)					{$this->SetEvent($keyPress, 'KeyPress');}
	/**
	 * Returns the LoseFocus Event, which gets launched when the Control loses focus, e.g., by clicking away or tabbing out of it
	 * @return Event
	 */
	function GetLoseFocus()							{return $this->GetEvent('LoseFocus');}
	/**
	 * Sets the LoseFocus Event, which gets launched when the Control loses focus, e.g., by clicking away or tabbing out of it
	 * @param Event $loseFocus
	 */
	function SetLoseFocus($loseFocus)				{$this->SetEvent($loseFocus, 'LoseFocus');}
	/**
	 * Returns the MouseDown Event, which gets launched when the user presses down his left mouse button over the Control
	 * @return Event
	 */
	function GetMouseDown()							{return $this->GetEvent('MouseDown');}
	/**
	 * Sets the MouseDown Event, which gets launched when the user presses down his left mouse button over the Control
	 * @param Event $mouseDown
	 */
	function SetMouseDown($mouseDown)				{$this->SetEvent($mouseDown, 'MouseDown');}
	/**
	 * Returns the MouseOut Event, which gets launched when the user moves his mouse cursor out of the Control's occupying space
	 * @return Event
	 */
	function GetMouseOut()							{return $this->GetEvent('MouseOut');}
	/**
	 * Sets the MouseOut Event, which gets launched when the user moves his mouse cursor out of the Control's occupying space
	 * @param Event $mouseOut
	 */
	function SetMouseOut($mouseOut)					{$this->SetEvent($mouseOut, 'MouseOut');}
	/**
	 * Returns the MouseOver Event, which gets launched when the user moves his mouse cursor over the Control's occupying space
	 * @return Event
	 */
	function GetMouseOver()							{return $this->GetEvent('MouseOver');}
	/**
	 * Sets the MouseOver Event, which gets launched when the user moves his mouse cursor over the Control's occupying space
	 * @param Event $mouseOver
	 */
	function SetMouseOver($mouseOver)				{$this->SetEvent($mouseOver, 'MouseOver');}
	/**
	 * Returns the MouseUp Event, which gets launched when the user releases the left mouse button over the Control's occupying space
	 * @return Event
	 */
	function GetMouseUp()							{return $this->GetEvent('MouseUp');}
	/**
	 * Sets the MouseUp Event, which gets launched when the user releases the left mouse button over the Control's occupying space
	 * @param Event $mouseUp
	 */
	function SetMouseUp($mouseUp)					{$this->SetEvent($mouseUp, 'MouseUp');}
	/**
	 * Returns the ReturnKey Event, which gets launched when the Control is focused and a user presses the return key on his keyboard
	 * @return Event
	 */
	function GetReturnKey()							{return $this->GetEvent('ReturnKey');}
	/**
	 * Sets the ReturnKey Event, which gets launched when the Control is focused and a user presses the return key on his keyboard
	 * @param Event $returnKey
	 */
	function SetReturnKey($returnKey)				{$this->SetEvent($returnKey, 'ReturnKey');}
	/**
	 * Returns the RightClick Event, which gets launched when a user right-clicks the Control
	 * @return Event
	 */
	function GetRightClick()						{return $this->GetEvent('RightClick');}
	/**
	 * Sets the RightClick Event, which gets launched when a user right-clicks the Control
	 * @param Event $rightClick
	 */
	function SetRightClick($rightClick)				{$this->SetEvent($rightClick, 'RightClick');}
	/**
	 * Returns the ShiftStart Event, which gets launched when a user starts shifting this Control
	 * @return Event
	 */
	function GetShiftStart()						{return $this->GetEvent('ShiftStart');}
	/**
	 * Sets the ShiftStart Event, which gets launched when a user starts shifting this Control
	 * @param Event $shiftStart
	 */
	function SetShiftStart($shiftStart)				{$this->SetEvent($shiftStart, 'ShiftStart');}
	/**
	 * Returns the ShiftStop Event, which gets launched when a user stops shifting this Control
	 * @return Event
	 */
	function GetShiftStop()							{return $this->GetEvent('ShiftStop');}
	/**
	 * Sets the ShiftStop Event, which gets launched when a user stops shifting this Control
	 * @param Event $shiftStop
	 */
	function SetShiftStop($shiftStop)				{$this->SetEvent($shiftStop, 'ShiftStop');}
	
	/**
	 * Returns the TypePause Event, which gets launched when a user has the Control focused, types something, and pauses typing for half a second
	 * @return Event
	 */
	function GetTypePause()							{return $this->GetEvent('TypePause');}
	/**
	 * Sets the TypePause Event, which gets launched when a user has the Control focused, types something, and pauses typing for half a second
	 * @param Event $typePause
	 */
	function SetTypePause($typePause)				{$this->SetEvent($typePause, 'TypePause');}
	/**
	 * Returns the ArrayList holding all the Shifts. This allows a Control to manipulate itself and any other control in various ways.
	 * The only thing that should be added to this ArrayList are statics of the Shift class.
	 * @return ArrayList
	 */
	function GetShifts()
	{
		if($this->Shifts == null)
		{
			$this->Shifts = new ImplicitArrayList($this, 'AddShift', '', 'ClearShift');
			$this->Shifts->RemoveFunctionName = 'RemoveShift';
			$this->Shifts->InsertFunctionName = 'InsertShift';
			NolohInternal::SetProperty('Shifts', 'Array()', $this);
		}
		return $this->Shifts;
	}
	/**
	 * @ignore
	 */
	private function AddShiftHelper($shift)
	{
		if($shift[1]==7)
		{
			AddNolohScriptSrc('Shift.js', true);
			QueueClientFunction($this, '_NShftWth', array('\''.$shift[0].'\'', 'Array(\''.$this->Id.'\',' . $shift[2]), false, Priority::High);
		}
		else
		{
			$fncStr = '_N(\''.$this->Id.'\').Shifts.splice';
			if(isset($_SESSION['_NFunctionQueue'][$this->Id]) && isset($_SESSION['_NFunctionQueue'][$this->Id][$fncStr]))
				$_SESSION['_NFunctionQueue'][$this->Id][$fncStr][0][] = $shift[2];
			else 
			{
				AddNolohScriptSrc('Shift.js', true);
				QueueClientFunction($this, $fncStr, array(-1, 0, $shift[2]));
			}
		}
		unset($shift[2]);
	}
	/**
	 * @ignore
	 */
	function AddShift($shift)
	{
		$this->AddShiftHelper($shift);
		$this->Shifts->Add($shift, true, true);
	}
	/**
	 * @ignore
	 */
	function InsertShift($shift, $index)
	{
		$this->AddShiftHelper($shift);
		$this->Shifts->Insert($shift, $index, true);
	}
	/**
	 * @ignore
	 */
	function RemoveShift($shift)
	{
		foreach($this->Shifts as $i => $val)
			if($this->Shifts[$i][0] == $shift[0])
			{
				$curType = $this->Shifts[$i][1];
				if($shift[1]==$curType || 
				  ($shift[1]==3 && ($curType==1||$curType==2)) ||
				  ($shift[1]==6 && ($curType==4||$curType==5)))
				{
					$this->Shifts->RemoveAt($i);
					QueueClientFunction($this, '_N(\'' . $this->Id. '\').Shifts.splice', array($i,1), false);
				}
				elseif($curType==3)
				{
					if($shift[1]==1)
						$this->ChangeShiftType($i, 2);
					elseif($shift[1]==2)
						$this->ChangeShiftType($i, 1);
				}
				elseif($curType==6)
				{
					if($shift[1]==4)
						$this->ChangeShiftType($i, 5);
					elseif($shift[1]==5)
						$this->ChangeShiftType($i, 4);
				}
				else 
					continue;
				return;
			}
	}
	/**
	 * @ignore
	 */
	private function ChangeShiftType($arrayIndex, $newType)
	{
		$tmp = $this->Shifts[$arrayIndex];
		$tmp[1] = $newType;
		$this->Shifts->Elements[$arrayIndex] = $tmp;
		QueueClientFunction($this, 'ChangeShiftType', array("'$this->Id'", $arrayIndex, $newType));
	}
	/**
	 * @ignore
	 */
	function ClearShift()
	{
		NolohInternal::SetProperty('Shifts', 'Array()', $this);
		$this->Shifts->Clear(true);
	}
	
	/**
	 * Brings this Control to the front of whatever Parent it is in. In other words, it will be given a ZIndex higher than any other.
	 */
	function BringToFront()
	{
		$this->_NSetZIndex(++$_SESSION['HighestZIndex']);
	}
	/**
	 * Sends this Control to the back of whatever Parent it is in. In other words, it will be given a ZIndex lower than any other.
	 */
	function SendToBack()
	{
		$this->_NSetZIndex(--$_SESSION['LowestZIndex']);
	}
	/**
	 * @ignore
	 */
	function GetAddId()
	{
		return $this->Id;
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		print($this->Text . ' ');
	}
	/**
	 * @ignore
	 */
	function &__get($nm)
	{
		if(strpos($nm, 'CSS') === 0 && $nm != 'CSSFile' && $nm != 'CSSClass')
		{
			if($this->CSSPropertyArray == null)
				$this->CSSPropertyArray = array();
			$key = str_replace(array('_', 'CSS'), array('', ''), $nm);
			$key = strtolower($key[0]) . substr($key, 1);
			$ret = &$this->CSSPropertyArray[$key];
		}
		else 
			$ret = parent::__get($nm);
			//The following line stole 10 hours from my life :( - Asher
//			$ret = &parent::__get($nm);
		return $ret;
	}
	/**
	 * @ignore
	 */
	function __set($nm, $val)
	{
		if(strpos($nm, 'CSS') === 0 && $nm != 'CSSFile' && $nm != 'CSSClass')
		{
			if($this->CSSPropertyArray == null)
				$this->CSSPropertyArray = array();
			$key = str_replace(array('_', 'CSS'), array('', ''), $nm);
			$key = strtolower($key[0]) . substr($key, 1);
			$this->CSSPropertyArray[$key] = $val;
			NolohInternal::SetProperty('style.'.$key, $val, $this);
		}
		else
			return parent::__set($nm, $val);
		return $val;
	}
}

?>