<?php 
/**
* @ignore
*/
	
define("NoDisplay", "NoDisplay");

/**
 * Control class file.
 */
 
/**
 * Control class
 *
 * Control is the base class for all NOLOH controls.
 * All Custom defined controls must extends Control.
 * <br>
 * The Control Class enables Controls to handle events, as well as most visual properties
 *
 *
 * Properties
 * - <b>CSSClass</b>, string, 
 *   <br>Gets or sets the CSSClass of this Control
 * - <b>PositionType</b>, integer,
 *   <br>Sets whether the position is abosolute, or relative
 * - <b>Enabled</b>, boolean
 *   <br>Gets or sets whether the Control is Enabled
 * - <b>Left</b>, Integer
 * - <b>Top</b>, Integer
 * - <b>Width</b>, Integer
 * - <b>Height</b>, Integer
 * - <b>Bottom</b>, Integer, read-only
 * - <b>Right</b>, Integer, read-only
 * - <b>Overlap</b>, boolean
 * 	 <br>Gets or sets whether this control can overlap
 * - <b>ClientVisible</b>, boolean
 * - <b>Border</b>, string
 * - <b>BackColor</b>, string
 * - <b>Cursor</b>, string
 
 * Events
 * - <b>Change</b>, Event
 * - <b>Click</b>, Event
 * - <b>DoubleClick</b>, Event
 * - <b>MouseDown</b>, Event
 * - <b>MouseOver</b>, Event
 * - <b>MouseOut</b>, Event
 * - <b>MouseOver</b>, Event
 * - <b>MouseUp</b>, Event
 * - <b>RightClick</b>, Event
 *
 * Events play a crucial role in NOLOH, you can set an event as follows
 * <code>
 *
 * Class Foo extends Control
 * {
 *		function Foo()
 *		{
 *			$this->Click = new ServerEvent($this, "HelloWorld");
 *		}
 *		
 *		public function HelloWorld()
 *		{
 *			Alert("Hello World");
 *		}
 * </code>
 */

class Control extends Component
{
	//Events
	private $EventSpace;
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
	*PositionType, Gets or sets the PositionType of this Control
	*Default is 0, which is absolute, 1 is relative.
	*@var integer
	*/
	private $PositionType;
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
	*	Overlap, Gets or sets whether this control can Overlap another control, if set to false, the control will be placed to the immediate Right of the closest control.
	* @var boolean
	*/
	public $Overlap;
	/**
	*	ClientVisible, Gets or sets the whether this control is visible on the client.
	*	<b>Note:</b> This is different from ServerVisible, when ServerVisible is set to false the control is not drawn on the client, when ClientVisible is set to false the control is drawn, but set to hidden.
	* @var boolean
	*/
	private $ClientVisible;
	/**
	*	HtmlName, Gets or sets the HtmlName of this Control, usually only applicable to RadioButtons, and CheckBoxes.
	*	Can only be used when making a custom Control, and overriding Show() to somehow use HtmlName
	*	<b>Note:</b> This gets shown as the NAME Attribute in Markup on the Client.
	* @var String
	*/
	public $HtmlName;
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
	*	Text, Gets or sets  the Text of this Control
	*	<b>Note:</b>Different Controls use this differently
	* @var String
	*/
	private $Text;
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
	/**
	* Show for the Control, <br><b>Note:</b>Is automatically called by NOLOH,
	* Should not be called under most circumstances, should only be called in overriding the Show() of a custom Control.
	* @return string
	*/
	function Show()
	{
		parent::Show();
		return "'id','$this->Id'";
	}
	
	function Hide()
	{
		NolohInternal::Hide($this);
		parent::Hide();
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
		NolohInternal::SetProperty("className", $newClass, $this);
	}
	
	function GetOpacity()
	{
		return $this->Opacity;
	}
	
	function SetOpacity($newOpacity)
	{
		$this->Opacity = $newOpacity;
		if(UserAgentDetect::GetBrowser()=="ie")
			NolohInternal::SetProperty("style.filter", "alpha(opacity=$newOpacity)", $this);
		else 
			NolohInternal::SetProperty("style.opacity", $newOpacity/100, $this);
	}
	
	function GetZIndex()
	{
		return $this->ZIndex;
	}
	
	function SetZIndex($newZIndex)
	{
		if($newZIndex > GetGlobal("HighestZIndex"))
			SetGlobal("HighestZIndex", $newZIndex);
		if($newZIndex < GetGlobal("LowestZIndex"))
			SetGlobal("LowestZIndex", $newZIndex);
		$this->ZIndex = $newZIndex;
		NolohInternal::SetProperty("style.zIndex", $newZIndex, $this);
	}
	
	/**
	* @ignore
	*/
	function GetText() 
	{
		return ($this->Text == null?"":$this->Text);
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
			NolohInternal::SetProperty("style.width", $newWidth."px", $this);
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
			NolohInternal::SetProperty("style.height", $newHeight."px", $this);
	}
	//
	function GetLeft() {return $this->Left;}
	//
	function SetLeft($newLeft) 
	{
		$this->Left = $newLeft;
		if(is_numeric($newLeft))
			NolohInternal::SetProperty("style.left", $newLeft."px", $this);
	}
	//
	function GetTop() {return $this->Top;}
	//
	function SetTop($newTop) 
	{
		$this->Top = $newTop;
		if(is_numeric($newTop))
			NolohInternal::SetProperty("style.top", $newTop."px", $this);
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
	
	function GetPositionType()
	{
		return $this->PositionType === null ? 0 : $this->PositionType;
	}
	
	function SetPositionType($newPositionType)
	{
		if($newPositionType < 3)
		{
			switch($newPositionType)
			{
				case 0: $printAs = "absolute"; break;
				case 1: $printAs = "relative"; break;
				case 2: $printAs = "static";
			}
			NolohInternal::SetProperty("style.position", $printAs, $this);
			if($this->PositionType == 3)
				NolohInternal::SetProperty("style.float", "", $this);
		}
		else 
		{
			NolohInternal::SetProperty("style.float", "left", $this);
			if($this->PositionType < 3)
				NolohInternal::SetProperty("style.position", "static", $this);
		}
		$this->PositionType = $newPositionType === 0 ? null : $newPositionType;
	}
	
	function GetEnabled()
	{
		return $this->Enabled === null;
	}
	
	function SetEnabled($whatBool)
	{
		$this->Enabled = $whatBool ? null : false;
		NolohInternal::SetProperty("disabled", !$whatBool, $this);
	}
	
	function GetClientVisible()
	{
		return $this->ClientVisible === null ? true : $this->ClientVisible;
	}
	
	function SetClientVisible($newVisibility)
	{
		if(is_string($newVisibility))
		{
			$this->ClientVisible = $newVisibility;
			NolohInternal::SetProperty("style.display", "none", $this);
			NolohInternal::SetProperty("style.visibility", "inherit", $this);
		}
		else 
		{
			$this->ClientVisible = $newVisibility ? null : false;
			NolohInternal::SetProperty("style.display", "", $this);
			NolohInternal::SetProperty("style.visibility", $newVisibility?"inherit":"hidden", $this);
		}
	}
	
	function GetBorder()
	{
		return $this->Border;
	}
	
	function SetBorder($newBorder)
	{
		$this->Border = $newBorder;
		NolohInternal::SetProperty("style.border", $newBorder, $this);
	}
	
	function GetBackColor()
	{
		return $this->BackColor;
	}
	
	function SetBackColor($newBackColor)
	{
		$this->BackColor = $newBackColor;
		NolohInternal::SetProperty("style.background", $newBackColor, $this);
	}
	
	function GetColor()
	{
		return $this->Color;
	}
	
	function SetColor($newColor)
	{
		$this->Color = $newColor;
		NolohInternal::SetProperty("style.color", $newColor, $this);			
	}
	
	function GetCursor()
	{
		return $this->Cursor == null ? Cursor::Arrow : $this->Cursor;
	}
	
	function SetCursor($newCursor)
	{
		$this->Cursor = $newCursor == Cursor::Arrow ? null : $newCursor;
		NolohInternal::SetProperty("style.cursor", $newCursor, $this);
	}
	
	function GetToolTip()
	{
		return $this->ToolTip;
	}
	
	function SetToolTip($newToolTip)
	{
		$this->ToolTip = $newToolTip;
		NolohInternal::SetProperty("title", $newToolTip, $this);
	}
	
	//Event Functions
	function GetChange()							{return $this->GetEvent("Change");}
	function SetChange($newChange)					{$this->SetEvent($newChange, "Change");}
	function GetClick()								{return $this->GetEvent("Click");}
	function SetClick($newClick)					{$this->SetEvent($newClick, "Click");}
	function GetDoubleClick()						{return $this->GetEvent("DoubleClick");}
	function SetDoubleClick($newDoubleClick)		{$this->SetEvent($newDoubleClick, "DoubleClick");}
	function GetDragCatch()							{return $this->GetEvent("DragCatch");}
	function SetDragCatch($newDragCatch)			{$this->SetEvent($newDragCatch, "DragCatch");}
	function GetKeyPress()							{return $this->GetEvent("KeyPress");}
	function SetKeyPress($newKeyPress)				{$this->SetEvent($newKeyPress, "KeyPress");}
	function GetLoseFocus()							{return $this->GetEvent("LoseFocus");}
	function SetLoseFocus($newLoseFocus)			{$this->SetEvent($newLoseFocus, "LoseFocus");}
	function GetMouseDown()							{return $this->GetEvent("MouseDown");}
	function SetMouseDown($newMouseDown)			{$this->SetEvent($newMouseDown, "MouseDown");}
	function GetMouseOut()							{return $this->GetEvent("MouseOut");}
	function SetMouseOut($newMouseOut)				{$this->SetEvent($newMouseOut, "MouseOut");}
	function GetMouseOver()							{return $this->GetEvent("MouseOver");}
	function SetMouseOver($newMouseOver)			{$this->SetEvent($newMouseOver, "MouseOver");}
	function GetMouseUp()							{return $this->GetEvent("MouseUp");}
	function SetMouseUp($newMouseUp)				{$this->SetEvent($newMouseUp, "MouseUp");}
	function GetReturnKey()							{return $this->GetEvent("ReturnKey");}
	function SetReturnKey($newReturnKey)			{$this->SetEvent($newReturnKey, "ReturnKey");}
	function GetRightClick()						{return $this->GetEvent("RightClick");}
	function SetRightClick($newRightClick)			{$this->SetEvent($newRightClick, "RightClick");}
	function GetLoad()								{return $this->GetEvent("Load");}
	function SetLoad($newLoad)						{$this->SetEvent($newLoad, "Load");}
	function GetScroll()							{return $this->GetEvent("Scroll");}
	function SetScroll($newScroll)					{$this->SetEvent($newScroll, "Scroll");}
	function GetTypePause()							{return $this->GetEvent("TypePause");}
	function SetTypePause($newTypePause)			{$this->SetEvent($newTypePause, "TypePause");}
	
	function GetEvent($eventType)
	{
		if($this->EventSpace == null)
			$this->EventSpace = array();
		return isset($this->EventSpace[$eventType]) 
			? $this->EventSpace[$eventType]
			: new Event(array(), array(array($this->Id, $eventType)));
	}
	
	function SetEvent($eventObj, $eventType)
	{
		if($this->EventSpace == null)
			$this->EventSpace = array();
		$this->EventSpace[$eventType] = $eventObj;
		$pair = array($this->Id, $eventType);
		if($eventObj != null && !in_array($pair, $eventObj->Handles))
			$eventObj->Handles[] = $pair;
		$this->UpdateEvent($eventType);
	}
	
	function UpdateEvent($eventType)
	{
		NolohInternal::SetProperty(Event::ConvertToJS($eventType), array($eventType, null), $this);
	}
	
	function GetEventString($eventType)
	{
		return isset($this->EventSpace[$eventType])
			? $this->EventSpace[$eventType]->GetEventString($eventType, $this->Id)
			: "";
	}
	
	function GetShifts()
	{
		if($this->Shifts == null)
		{
			$this->Shifts = new ImplicitArrayList($this, "AddShift", "", "ClearShift");
			$this->Shifts->RemoveItemFunctionName = "RemoveShift";
			NolohInternal::SetProperty("Shifts", "Array()", $this);
			$this->UpdateEvent("MouseDown");
		}
		return $this->Shifts;
	}
	
	function AddShift($shift)
	{
		if($shift[1]==7)
			QueueClientFunction($this, "AddShiftWith", array("'{$shift[0]}'", "Array(\"$this->Id\"," . $shift[2]), false, Priority::High);
		else
		{
			$fncStr = "document.getElementById('$this->Id').Shifts.splice";
			if(isset($_SESSION['NOLOHFunctionQueue'][$this->Id]) && isset($_SESSION['NOLOHFunctionQueue'][$this->Id][$fncStr]))
				$_SESSION['NOLOHFunctionQueue'][$this->Id][$fncStr][0][] = $shift[2];
			else 
				QueueClientFunction($this, $fncStr, array(-1, 0, $shift[2]));
		}
		unset($shift[2]);
		$this->Shifts->Add($shift, true, true);
	}
	
	function RemoveShift($shift)
	{
		$shiftCount = $this->Shifts->Count;
		for($i=0; $i<$shiftCount; ++$i)
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
	
	private function ChangeShiftType($arrayIndex, $newType)
	{
		$tmp = $this->Shifts[$arrayIndex];
		$tmp[1] = $newType;
		$this->Shifts[$arrayIndex] = $tmp;
		QueueClientFunction($this, "ChangeShiftType", array("'$this->Id'", $arrayIndex, $newType));
	}
	
	function ClearShift()
	{
		NolohInternal::SetProperty("Shifts", "Array()", $this);
		$this->Shifts->Clear(false, true);
	}
	
	/**
	* Brings this Control to the front of whatever container it is in.
	*/
	function BringToFront()
	{
		$this->SetZIndex(GetGlobal('HighestZIndex')+1);
	}
	/**
	* Sends this Control to the back of whatever container it is in.
	*/
	function SendToBack()
	{
		$this->SetZIndex(GetGlobal('LowestZIndex')-1);
	}
	
	function GetAddId()
	{
		return $this->Id;
	}
	
	function &__get($nm)
	{
		if(strpos($nm, "CSS") === 0 && $nm != "CSSFile" && $nm != "CSSClass")
		{
			if($this->CSSPropertyArray == null)
				$this->CSSPropertyArray = array();
			$key = str_replace("_", "", str_replace("CSS", "", $nm));
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
	
	function __set($nm, $val)
	{
		parent::__set($nm, $val);
		if(strpos($nm, "CSS") === 0 && $nm != "CSSFile" && $nm != "CSSClass")
		{
			if($this->CSSPropertyArray == null)
				$this->CSSPropertyArray = array();
			$key = str_replace("_", "", str_replace("CSS", "", $nm));
			$first = strtolower(substr($key, 0, 1));
			$key = $first . substr($key, 1, strlen($key)-1);
			$this->CSSPropertyArray[$key] = $val;
			NolohInternal::SetProperty("style.$key", $val, $this);
		}
		return $val;
	}
}
?>