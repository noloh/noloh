<?php
/**
 * Control Class File
 * @package Web.UI.Controls
 */
/** 
 * Control is the base class for all NOLOH controls.
 * All Custom defined controls must extends Control.
 * <br>
 * The Control Class enables Controls to handle events, as well as most visual properties
 *
 * 
 *  @property string $CSSClass The CSSClass of the Control
 *  @property string $CSS_________ Allows for the ability to
 *  set ANY CSS property on the fly. Just prepend the style with CSS,
 *  and change dash to underscore. ex. CSSBorder_Bottom = "1px solid black";
 *  @property integer LayoutType
 * - <b>LayoutType</b>, integer,
 *   <br>Sets whether the position is absolute, relative, or static
 * - <b>Enabled</b>, boolean
 *   <br>Gets or sets whether the Control is Enabled
 * - <b>Left</b>, Integer
 *   <br>Gets or sets the Left of the Control
 * - <b>Top</b>, Integer
 *   <br>Gets or sets the Top of the Control
 * - <b>Width</b>, Integer
 *   <br>Gets or sets the Width of the Control
 * - <b>Height</b>, Integer
 *   <br>Gets or sets the Height of the Control
 * - <b>Bottom</b>, Integer, read-only
 *   <br>Gets the Bottom of the Control
 * - <b>Right</b>, Integer, read-only
 *   <br>Gets the Right of the Control
 * - <b>Opacity</b>, integer
 * 	 <br>Gets or sets the Opacity of the Control
 * - <b>ClientVisible</b>, boolean
 *   <br>Gets or sets the Visibility of the Control
 * - <b>Border</b>, string
 *   <br>Gets or sets the Border of the Control
 * - <b>BackColor</b>, string
 *   <br>Gets or sets the BackColor of the Control
 * - <b>Cursor</b>, string
 *   <br>Gets or sets the Mouse Cursor to be shown while over the the Control
 * - <b>Text</b>, string
 *   <br>Gets or sets the Text of the Control
 * - <b>ToolTip</b>, string
 *   <br>Gets or sets the ToolTip to be displayed while the mouse cursor is over the Control
 * - <b>ZIndex</b>, integer
 *   <br>Gets or sets the z index position of this control on the webpage, or in it's container
 *  
 * Events<br>
 * - <b>Change</b>, Event
 * - <b>Click</b>, Event
 * - <b>DoubleClick</b>, Event
 * - <b>MouseDown</b>, Event
 * - <b>MouseOver</b>, Event
 * - <b>MouseOut</b>, Event
 * - <b>MouseOver</b>, Event
 * - <b>MouseUp</b>, Event
 * - <b>RightClick</b>, Event
 * - <b>Scroll</b>, Event
 *
 * Events play a crucial role in NOLOH, you can set an event as follows
 * <code>
 *
 * Class Foo extends Control
 * {
 *      function Foo()
 *      {
 *          $this->Click = new ServerEvent($this, "HelloWorld");
 *      }
 *      public function HelloWorld()
 *      {
 *          Alert("Hello World");
 *      }
 * </code>
 */

class Control extends Component
{
	//Attributes
	/**
	* CSSClass, Gets or Sets the CSSClass associated with this object, default is empty
	* If you have a Cascading Style Sheet with a class as follows.
	* 	.Links
	*	{
	*		font-size:12px;
	*	}
	* <br> You would set this control to use that class as follows
	* <code> $this->CSSClass = "Links"; </code>
 	* @var string
 	*/
	private $CSSClass;
	/**
	*Opacity of the component
	*@var integer
	*/
	private $Opacity;
	/**
	*ZIndex of the component
	*@var integer
	*/
	private $ZIndex;
	/**
	*@ignore
	*/
	protected $CSSPropertyArray;
	/**
	*LayoutType, Gets or sets the LayoutType of this Control
	*Default is Layout::Absolute, possible values are Layout::Absolute, 
	*Layout::Relative, and Layout::Web (which is the equivalent to CSS static).
	*@var integer
	*/
	private $LayoutType;
	/**
	*	Enabled, Gets or sets whether this control is Enabled, when Enabled is false, the Control takes on a Disabled look
	* @var boolean
	*/
	private $Enabled;
	/**
	*	Left, Gets or sets the Left Coordinates of this control in pixels
	* @var integer
	*/
	private $Left;
	/**
	*	Top, Gets or sets the Top Coordinates of this control in pixels
	* @var integer
	*/
	private $Top;
	/**
	*	Width, Gets or sets the Width of this control in pixels
	* @var integer
	*/
	private $Width;
	/**
	*	Height, Gets or sets the Height of this control in pixels
	* @var integer
	*/
	private $Height;
	/**
	*	ClientVisible, Gets or sets the whether this control is visible on the client.
	*	<b>Note:</b> This is different from ServerVisible, when ServerVisible is set to false the control is not drawn on the client, when ClientVisible is set to false the control is drawn, but set to hidden.
	* @var boolean
	*/
	private $Visible;
	/**
	*	Border, Gets or sets  the border of this Control
	*	e.g, "1px solid black", "5px dashed red"
	* @var String
	*/
	private $Border;	
	//Styles
	/**
	*	BackColor, Gets or sets  the BackgroundColor of this Control
	*	e.g, "Red", "#FFFFFF"
	* @var String
	*/
	private $BackColor;
	/**
	*	BackColor, Gets or sets  the Color of this Control
	* 	Usually affects the Text of the Control
	*	e.g, "Red", "#FFFFFF"
	* @var String
	*/
	private $Color;
	/**
	*	Cursor, Gets or sets the Cursor property of this Control
	*	This affects how the Mouse Cursor will look over this Controlo
	*	e.g, "pointer", "crosshair", "text", "wait", "help", "default","move","e-resize","ne-resize","nw-resize","n-resize","se-resize"."sw-resize","s-resize","w-resize", "http://www.foo.com/mycursor.gif"
	* @var String
	*/
	private $Cursor;
	/**
	* @ignore
	*/
	private $ToolTip;
    /**
	* @ignore
	*/
    private $ContextMenu;
	/**
	*Text, Gets or sets  the Text of this Control
	*<b>Note:</b>Different Controls use this differently
	* @var String
	*/
	private $Text;
	/**
	*
	*/
	private $Selected;
	private $Buoyant;
	/**
	*	An array that holds the different Shift information on this Control.
	*	This allows a Control to manipulate itself and any other control in multiple ways
	*	<b>Example</b>
	* 	<code>
	*	$this->Shifts[] = Shift::Left(someObj, null, null, null, null, null);
	*	$this->Shifts[] = Shift::Top(someObj2, null, null, null, null, null);
	*	$this->Shifts[] = Shift::Location(this, null, null, null, null, null);
	*	$this->Shifts[] = Shift::Width(someObj3, null, null, null, null, null);
	*	$this->Shifts[] = Shift::Height(someObj3, null, null, null, null, null);
	*	$this->Shifts[] = Shift::Size(someObj, null, null, null, null, null);
	*	$this->Shifts[] = Shift::Size(someObj2, null, null, null, null, null);
	*	</code>
	*	Note: All the paramaters other than the object are optional.
	*	<b>Note:</b>Different Controls use this differently
	*@var array
	*/
	private $Shifts;
	/**
	* Constructor.
	* for inherited components, be sure to call the parent constructor first
 	* so that the component properties and events are defined.
 	* Example
 	*	<code> $tempVar = new Control(15, 15, 20, 20);</code>
	* @param integer|optional
	* @param integer|optional
	* @param integer|optional
	* @param integer|optional
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

	function Bury()
	{
		NolohInternal::Bury($this);
		parent::Bury();
	}
	
	function Resurrect()
	{
		NolohInternal::Resurrect($this);
		parent::Resurrect();
	}

	function GetCSSClass()
	{
		return $this->CSSClass;
	}

	function SetCSSClass($newClass)
	{
		$this->CSSClass = $newClass;
		NolohInternal::SetProperty('className', $newClass, $this);
	}

	function GetOpacity()
	{
		return $this->Opacity;
	}

	function SetOpacity($newOpacity)
	{
		$this->Opacity = $newOpacity;
		if(UserAgentDetect::GetBrowser()=='ie')
			NolohInternal::SetProperty('style.filter', "alpha(opacity=$newOpacity)", $this);
		else
			NolohInternal::SetProperty('style.opacity', $newOpacity/100, $this);
	}

	function GetZIndex()
	{
		return $this->ZIndex;
	}

	function SetZIndex($newZIndex)
	{
		if($newZIndex > $_SESSION['HighestZIndex'])
			$_SESSION['HighestZIndex'] = $newZIndex;
		if($newZIndex < $_SESSION['LowestZIndex'])
			$_SESSION['LowestZIndex'] = $newZIndex;
		$this->_NSetZIndex($newZIndex);
	}

	function _NSetZIndex($newZIndex)
	{
		$this->ZIndex = $newZIndex;
		NolohInternal::SetProperty('style.zIndex', $newZIndex, $this);
	}

	/**
	* @ignore
	*/
	function GetText()
	{
		return ($this->Text == null?'':$this->Text);
	}
	/**
	*Sets the Text of the Control.
	*<b>Note:</b>Can also be set as a property.
	*<code>$this->Text = "NOLOH";</code>
	*<b>!Important!</b> If Overriding, make sure to call parent::SetText($newText)
	* @param string|Src
	*/
	function SetText($newText)
	{
		$this->Text = $newText;
	}
	/**
	* @ignore
	*/
	function GetWidth() {return $this->Width;}
	/**
	*Sets the Width of the Control.
	*<b>Note:</b>Can also be set as a property.
	*<code>$this->Width = 100;</code>
	*<b>!Important!</b> If Overriding, make sure to call parent::SetWidth($newWidth)
	* @param string|Src
	*/
	function SetWidth($newWidth)
	{
		$this->Width = $newWidth;
		if(is_numeric($newWidth))
			NolohInternal::SetProperty('style.width', $newWidth.'px', $this);
		elseif(is_numeric(rtrim($newWidth, '%')))
			NolohInternal::SetProperty('style.width', $newWidth, $this);
		elseif(is_null($newWidth))
			NolohInternal::SetProperty('style.width', '', $this);
	}
	/**
	* @ignore
	*/
	function GetHeight() {return $this->Height;}
	/**
	*Sets the Height of the Control.
	*<b>Note:</b>Can also be set as a property.
	*<code>$this->Height = 100;</code>
	*<b>!Important!</b> If Overriding, make sure to call parent::SetHeight($newHeight)
	* @param string|Src
	*/
	function SetHeight($newHeight)
	{
		$this->Height = $newHeight;
		if(is_numeric($newHeight))
			NolohInternal::SetProperty('style.height', $newHeight.'px', $this);
		elseif(is_numeric(rtrim($newHeight, '%')))
			NolohInternal::SetProperty('style.height', $newHeight, $this);
		elseif(is_null($newHeight))
			NolohInternal::SetProperty('style.height', '', $this);
	}
	//
	function GetLeft() {return $this->Left;}
	//
	function SetLeft($newLeft)
	{
		$this->Left = $newLeft;
		if(is_numeric($newLeft))
			NolohInternal::SetProperty('style.left', $newLeft.'px', $this);
		elseif(is_numeric(rtrim($newLeft, '%')))
			NolohInternal::SetProperty('style.left', $newLeft, $this);
		elseif(is_null($newLeft))
			NolohInternal::SetProperty('style.left', '', $this);
	}
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
	//
	function GetTop() {return $this->Top;}
	//
	function SetTop($newTop)
	{
		$this->Top = $newTop;
		if(is_numeric($newTop))
			NolohInternal::SetProperty('style.top', $newTop.'px', $this);
		elseif(is_numeric(rtrim($newTop, '%')))
			NolohInternal::SetProperty('style.top', $newTop, $this);
		elseif(is_null($newTop))
			NolohInternal::SetProperty('style.top', '', $this);
	}
	/**
	*<b>Note:</b>Can also be called as a property.
	*<code> $tempLeft = $this->Bottom;</code>
	* @return integer|Bottom
	*/
	function GetBottom()
	{
		return $this->GetTop() + $this->GetHeight();
	}
	/**
	*<b>Note:</b>Can also be called as a property.
	*<code> $tempLeft = $this->Right;</code>
	* @return integer|Right
	*/
	function GetRight()
	{
		return $this->GetLeft() + $this->GetWidth();
	}
	function GetLayoutType()
	{
		return $this->LayoutType === null ? 0 : $this->LayoutType;
	}
	/**
	*LayoutType, Gets or sets the LayoutType of this Control
	*Default is Layout::Absolute, possible values are Layout::Absolute, 
	*Layout::Relative, and Layout::Web (which is the equivalent to CSS static).
	*@var integer
	*/
	function SetLayoutType($layoutType)
	{
		if(is_numeric($layoutType))
		{
			switch($layoutType)
			{
				case 0: $printAs = 'absolute'; break;
				case 1: $printAs = 'relative'; break;
				case 2: $printAs = 'static';
			}
			NolohInternal::SetProperty('style.position', $printAs, $this);
			if(is_string($this->LayoutType))
				NolohInternal::SetProperty('style.float', '', $this);
		}
		else
		{
			NolohInternal::SetProperty('style.float', $layoutType, $this);
			if(is_numeric($this->GetLayoutType()))
				NolohInternal::SetProperty('style.position', 'relative', $this);
		}
		$this->LayoutType = $layoutType === 0 ? null : $layoutType;
	}
	function GetEnabled()
	{
		return $this->Enabled === null;
	}

	function SetEnabled($bool)
	{
		$this->Enabled = $bool ? null : false;
		NolohInternal::SetProperty('disabled', !$bool, $this);
	}

	function GetClientVisible()
	{
		return $this->Visible === null ? true : $this->Visible;
	}

	function SetClientVisible($newVisibility)
	{
		if(is_string($newVisibility))
		{
			$this->Visible = $newVisibility;
			NolohInternal::SetProperty('style.display', 'none', $this);
			NolohInternal::SetProperty('style.visibility', 'inherit', $this);
		}
		else
		{
			$this->Visible = $newVisibility ? null : false;
			NolohInternal::SetProperty('style.display', '', $this);
			NolohInternal::SetProperty('style.visibility', $newVisibility?'inherit':'hidden', $this);
		}
	}

	function GetVisible()
	{
		return $this->Visible === null ? true : $this->Visible;
	}

	function SetVisible($visibility)
	{
		if($visibility === null || $visibility === "null")
		{
			$this->Visible = 0;
			NolohInternal::SetProperty('style.display', 'none', $this);
		}
		else//if(is_bool($visibility))
		{
			NolohInternal::SetProperty('style.display', '', $this);
			if($visibility===true || $visibility==="true")
			{
				$this->Visible = null;
				NolohInternal::SetProperty('style.visibility', 'visible', $this);
			}
			else
			{
				$this->Visible = false;
				NolohInternal::SetProperty('style.visibility', 'hidden', $this);
			}
		}
	}

	function GetBorder()
	{
		return $this->Border;
	}

	function SetBorder($newBorder)
	{
		$this->Border = $newBorder;
		NolohInternal::SetProperty('style.border', is_numeric($newBorder)?($newBorder.'px solid black'):$newBorder, $this);
	}

	function GetBackColor()
	{
		return $this->BackColor;
	}

	function SetBackColor($newBackColor)
	{
		$this->BackColor = $newBackColor;
		NolohInternal::SetProperty('style.background', $newBackColor, $this);
	}

	function GetColor()
	{
		return $this->Color;
	}

	function SetColor($newColor)
	{
		$this->Color = $newColor;
		NolohInternal::SetProperty('style.color', $newColor, $this);
	}

	function GetCursor()
	{
		return $this->Cursor == null ? Cursor::Arrow : $this->Cursor;
	}

	function SetCursor($newCursor)
	{
		$this->Cursor = $newCursor == Cursor::Arrow ? null : $newCursor;
		NolohInternal::SetProperty('style.cursor', $newCursor, $this);
	}

	function GetToolTip()
	{
		return $this->ToolTip;
	}

	function SetToolTip($newToolTip)
	{
		$this->ToolTip = $newToolTip;
		NolohInternal::SetProperty('title', $newToolTip, $this);
	}

    function GetContextMenu()
    {
        return $this->ContextMenu;
    }

    function SetContextMenu(ContextMenu $contextMenu)
    {
		$this->ContextMenu = &$contextMenu;
        $contextMenu->SetParentId('N1');
		NolohInternal::SetProperty('ContextMenu', $contextMenu->Id, $this);
    }

	function GetBuoyant()
	{
		return $this->Buoyant !== null;
	}

	function SetBuoyant($bool)
	{
		if($bool)
			$this->Buoyant = true;
		else
		{
			$this->Buoyant = null;
			QueueClientFunction($this, 'StopBuoyant', array("'$this->Id'"));
		}
		if($this->GetShowStatus()===1)
		{
			NolohInternal::Bury($this);
			NolohInternal::Resurrect($this);
		}
	}
	
	function GetSelected()
	{
		return $this->SetSelected !== null;
	}
	
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
			if($bool && $this->GroupName != null)
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

    function SetParentId($id)
    {
        parent::SetParentId($id);
        if($this->ZIndex == null)
            $this->_NSetZIndex(++$_SESSION['HighestZIndex']);
    }

	//Event Functions
	function GetChange()							{return $this->GetEvent('Change');}
	function SetChange($newChange)					{$this->SetEvent($newChange, 'Change');}
	function GetClick()								{return $this->GetEvent('Click');}
	function SetClick($newClick)					{$this->SetEvent($newClick, 'Click');}
	function GetDoubleClick()						{return $this->GetEvent('DoubleClick');}
	function SetDoubleClick($newDoubleClick)		{$this->SetEvent($newDoubleClick, 'DoubleClick');}
	function GetDragCatch()							{return $this->GetEvent('DragCatch');}
	function SetDragCatch($newDragCatch)			{$this->SetEvent($newDragCatch, 'DragCatch');}
	function GetFocus()								{return $this->GetEvent('Focus');}
	function SetFocus($newFocus)					{$this->SetEvent($newFocus, 'Focus');}
	function GetKeyPress()							{return $this->GetEvent('KeyPress');}
	function SetKeyPress($newKeyPress)				{$this->SetEvent($newKeyPress, 'KeyPress');}
	function GetLoseFocus()							{return $this->GetEvent('LoseFocus');}
	function SetLoseFocus($newLoseFocus)			{$this->SetEvent($newLoseFocus, 'LoseFocus');}
	function GetMouseDown()							{return $this->GetEvent('MouseDown');}
	function SetMouseDown($newMouseDown)			{$this->SetEvent($newMouseDown, 'MouseDown');}
	function GetMouseOut()							{return $this->GetEvent('MouseOut');}
	function SetMouseOut($newMouseOut)				{$this->SetEvent($newMouseOut, 'MouseOut');}
	function GetMouseOver()							{return $this->GetEvent('MouseOver');}
	function SetMouseOver($newMouseOver)			{$this->SetEvent($newMouseOver, 'MouseOver');}
	function GetMouseUp()							{return $this->GetEvent('MouseUp');}
	function SetMouseUp($newMouseUp)				{$this->SetEvent($newMouseUp, 'MouseUp');}
	function GetReturnKey()							{return $this->GetEvent('ReturnKey');}
	function SetReturnKey($newReturnKey)			{$this->SetEvent($newReturnKey, 'ReturnKey');}
	function GetRightClick()						{return $this->GetEvent('RightClick');}
	function SetRightClick($newRightClick)			{$this->SetEvent($newRightClick, 'RightClick');}
	function GetTypePause()							{return $this->GetEvent('TypePause');}
	function SetTypePause($newTypePause)			{$this->SetEvent($newTypePause, 'TypePause');}
	
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
			QueueClientFunction($this, 'AddShiftWith', array('\''.$shift[0].'\'', 'Array(\''.$this->Id.'\',' . $shift[2]), false, Priority::High);
		}
		else
		{
			$fncStr = 'document.getElementById(\''.$this->Id.'\').Shifts.splice';
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
	
	function AddShift($shift)
	{
		$this->AddShiftHelper($shift);
		$this->Shifts->Add($shift, true, true);
	}
	
	function InsertShift($shift, $index)
	{
		$this->AddShiftHelper($shift);
		$this->Shifts->Insert($shift, $index, true);
	}
	/**
	 * Removes the 
	 * @param mixed $shift
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
					QueueClientFunction($this, "document.getElementById('$this->Id').Shifts.splice", array($i,1), false);
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
	 * Removes all Shift on this Control
	 */
	function ClearShift()
	{
		NolohInternal::SetProperty('Shifts', 'Array()', $this);
		$this->Shifts->Clear(true);
	}
	
	/**
	* Brings this Control to the front of whatever container it is in.
	*/
	function BringToFront()
	{
		$this->_NSetZIndex(++$_SESSION['HighestZIndex']);
	}
	/**
	* Sends this Control to the back of whatever container it is in.
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
			$key = str_replace('_', '', str_replace('CSS', '', $nm));
			$first = strtolower(substr($key, 0, 1));
			$key = $first . substr($key, 1, strlen($key)-1);
			$ret = &$this->CSSPropertyArray[$key];
		}
		//elseif(Event::ValidType($nm))
		//	$ret = $this->GetEvent($nm);
		else 
			$ret = parent::__get($nm);
		return $ret;
	}
	/**
	 * @ignore
	 */
	function __set($nm, $val)
	{
		parent::__set($nm, $val);
		if(strpos($nm, 'CSS') === 0 && $nm != 'CSSFile' && $nm != 'CSSClass')
		{
			if($this->CSSPropertyArray == null)
				$this->CSSPropertyArray = array();
			$key = str_replace('_', '', str_replace('CSS', '', $nm));
			$first = strtolower(substr($key, 0, 1));
			$key = $first . substr($key, 1, strlen($key)-1);
			$this->CSSPropertyArray[$key] = $val;
			NolohInternal::SetProperty("style.$key", $val, $this);
		}
		return $val;
	}
}
?>