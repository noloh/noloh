<?php
/**
 * Panel class
 *
 * A Panel is a Control to which other Controls can be added. It is important both for visual reasons (as it has a position,
 * size, background color, and all the other properties associated with Control - unlike a Container!) and for organizing
 * your controls in a logical manner. When Controls that are added to a Panel, their Left and Top both being 0 corresponds to
 * the top and left corner of the Panel, not of the screen. Thus, every Panel has its own inner coordinate system.
 * 
 * @package Controls/Core
 */
class Panel extends Control
{
	/**
	* An ArrayList of Controls that will be Shown when added, provided the Panel has also been Shown
	* @var ArrayList
	*/
	public $Controls;
	private $ScrollLeft;
	private $ScrollTop;
	private $Scrolling;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Panel
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width coordinate of this element
	 * @param integer $height The Height coordinate of this element
	 * @param Control $implicitObject If you want an ImplicitArrayList instantiated instead of a regular ArrayList, this parameter signifies the implicit object, usually $this
	 * @return Panel
	 */
	function Panel($left = 0, $top = 0, $width = 100, $height = 100, $implicitObject = null)
	{
		parent::Control($left, $top, $width, $height);
		if($implicitObject == null)
			$this->Controls = new ArrayList();
		elseif($implicitObject == $this)
			$this->Controls = new ImplicitArrayList();
		else 
			$this->Controls = new ImplicitArrayList($implicitObject);
		$this->Controls->ParentId = $this->Id;
		$this->SetScrolling(($width === null || $height === null)?null:false);
		$this->SetCSSClass();
	}
	/**
     * Returns the Scroll Event, which gets launched when a user scrolls through the Panel
     * @return Event
     */
	function GetScroll()							{return $this->GetEvent('Scroll');}
	/**
	 * Sets the Scroll Event, which gets launched when a user scrolls through the Panel
	 * @param Event $scroll
	 */
	function SetScroll($scroll)						{$this->SetEvent($scroll, 'Scroll');}
	/**
	 * @ignore
	 */
	function GetScrollLeft()
	{
		return $this->ScrollLeft;
	}
	/**
	 * Sets the position of the horizontal scrollbar
	 * @param integer $scrollLeft
	 */
    function SetScrollLeft($scrollLeft)
    {
    	$scrollLeft = $scrollLeft==Layout::Left?0: $scrollLeft==Layout::Right?9999: $scrollLeft;
        if($_SESSION['_NIsIE'])
    		QueueClientFunction($this, 'NOLOHChange', array('\''.$this->Id.'\'', '\'scrollLeft\'', $scrollLeft), false, Priority::High);
    	else
        	NolohInternal::SetProperty('scrollLeft', $scrollLeft, $this);
        $this->ScrollLeft = $scrollLeft;
    }
    /**
     * @ignore
     */
    function GetScrollTop()
    {
    	return $this->ScrollTop;
    }
    /**
	 * Sets the position of the vertical scrollbar
	 * @param integer $scrollTop
	 */
    function SetScrollTop($scrollTop)
    {
    	$scrollTop = $scrollTop==Layout::Top?0: $scrollTop==Layout::Bottom?9999: $scrollTop;
    	if($_SESSION['_NIsIE'])
    		QueueClientFunction($this, 'NOLOHChange', array('\''.$this->Id.'\'', '\'scrollTop\'', $scrollTop), false, Priority::High);
    	else
        	NolohInternal::SetProperty('scrollTop', $scrollTop, $this);
        $this->ScrollTop = $scrollTop;
    }
    /**
     * @ignore
     */
	function SetCSSClass($cssClass=null)
	{
		parent::SetCSSClass('NPanel '.$cssClass);
	}
	/**
	 * Opens the Panel and only the Panel in a separate window so that the user may print it. The user's browser must
	 * allow opening new windows for this to work.
	 */
	function OpenPrintableVersion()
	{
		AddScript('var oldNode = _N(\''.$this->Id.'\'); var newWin = window.open(); newWin.document.write(oldNode.innerHTML);');
	}
	/**
	 * Returns the kind of scroll bars the Panel will have, if any
	 * @return mixed
	 */
	function GetScrolling()
	{
		return $this->Scrolling;
	}
	/**
	 * Sets the kind of scroll bars the Panel will have, if any
	 * @param mixed $scrollType
	 */
	function SetScrolling($scrollType)
	{
		$this->Scrolling = $scrollType;
		$tmpScroll = null;
		if($scrollType == System::Auto)
			$tmpScroll = 'auto';
		elseif($scrollType == System::Full)
			$tmpScroll = 'visible';
		elseif($scrollType === null)
			$tmpScroll = '';
		elseif($scrollType == System::Horizontal)
		{
			$tmpScroll = '';
			NolohInternal::SetProperty('style.overflowX', 'auto', $this);
			NolohInternal::SetProperty('style.overflowY', 'hidden', $this);
		}
		elseif($scrollType == System::Vertical)
		{
			
			$tmpScroll = '';
			NolohInternal::SetProperty('style.overflowX', 'hidden', $this);
			NolohInternal::SetProperty('style.overflowY', 'auto', $this);
		}
		elseif($scrollType)
			$tmpScroll = 'scroll';
		else//if(!$scrollType)
			$tmpScroll = 'hidden';
		//Alert($tmpScroll);
		NolohInternal::SetProperty('style.overflow', $tmpScroll, $this);
	}
	/**
	 * @ignore
	 */
	function GetStyleString()
	{
		return parent::Show();
	}
	/**
	 * @ignore
	 */
	function Show()
	{
        NolohInternal::Show('DIV', parent::Show(), $this);
		//$initialProperties = $this->GetStyleString();
		//$initialProperties = parent::Show();
		//NolohInternal::Show('DIV', $initialProperties, $this);

//			if(false && $this->SelectFix && (GetBrowser() == "ie"))
//			{
//				$initialProperties = "'id','{$this->Id}IFRAME','style.position','absolute','style.left','{$this->Left}px','style.top','{$this->Top}px','style.width','{$this->Width}px','style.height','{$this->Height}px','src','javascript:false','scrolling','no','frameborder','0'";
//				NolohInternal::Show("IFRAME", $initialProperties, $this);
//				AddScript("_N('{$this->Id}').ShiftsWith = '{$this->Id}IFRAME'");
//			}
		
		
		/*
		if($showIFrame)
			$dispStr .= "<IFRAME ID = '{$this->Id}IFRAME' style='POSITION:absolute; LEFT:{$this->Left}px; TOP:{$this->Top}px; WIDTH:{$this->Width}px; HEIGHT:{$this->Height}px;' src='javascript:false;' scrolling='no' frameborder='0'></IFRAME>";
		//if($this->DropShadow == true)
		//{
		//	print(str_repeat("  ", $IndentLevel) . "<DIV ID = '{$this->Id}DS' style='POSITION:absolute; LEFT:".($this->Left + 5)."px; TOP:".($this->Top+5)."px; WIDTH:{$this->Width}px; HEIGHT:{$this->Height}px; background:black; filter:alpha(opacity=20)'></DIV>\n");
		//	AddScript("_N('{$this->Id}').ShiftsWith = '{$this->Id}DS'");
		//}
			/*else
			{
				print(str_repeat("  ", $IndentLevel) . "<IFRAME ID = '{$this->Id}IFRAME' style='POSITION:absolute; LEFT:{$this->Left}px; TOP:{$this->Top}px; WIDTH:{$this->Width}px; HEIGHT:{$this->Height}px; filter: alpha(opacity=0)' src='javascript:false;' scrolling='no' frameborder='0'></IFRAME>\n");
				AddScript("_N('{$this->Id}').ShiftsWith = '{$this->Id}IFRAME'");
			}*
		
		$dispStr .= "<DIV " . $parentShow;
		//if(GetBrowser() == "ie" && $this->AddIFrame == true)
			//print(str_repeat("  ", $IndentLevel+1) . "<IFRAME style='POSITION:absolute; LEFT:0px; TOP:0px; WIDTH:{$this->Width}px; HEIGHT:{$this->Height}px; filter: alpha(opacity=0);' frameborder=0 scrolling=no src='javascript:false;'></IFRAME>\n");		
		$this->IterateThroughAllControls($IndentLevel);
		$dispStr .= "</DIV>";
		NolohInternal::Show($dispStr, $this);*/
		//$this->IterateThroughAllControls();
		//if($showIFrame)
		//	AddScript("_N('{$this->Id}').ShiftsWith = '{$this->Id}IFRAME'");

		//return $initialProperties;
	}
	/**
	 * @ignore
	 */
	function SetChildLayout($layout)
	{
		//This needs to be modified to actually set a property, and Controls needs to be Implict so when adding a control it can set the Layout right there
		$childCount = $this->Controls->Count();
		for($i=0;$i<$childCount;++$i)
			$this->Controls->Elements[$i]->Layout = $layout;
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		foreach($this->Controls as $control)
			$control->SearchEngineShow();
	}
}
?>