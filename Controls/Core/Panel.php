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
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 * @param Control $implicitObject If you want an ImplicitArrayList instantiated instead of a regular ArrayList, this parameter signifies the implicit object, usually $this
	 * @return Panel
	 */
	function Panel($left = 0, $top = 0, $width = 100, $height = 100, $implicitObject = null)
	{
		parent::Control($left, $top, null, null);
		if($implicitObject == null)
			$this->Controls = new ArrayList();
		elseif($implicitObject === $this)
			$this->Controls = new ImplicitArrayList();
		else 
			$this->Controls = new ImplicitArrayList($implicitObject);
		$this->Controls->ParentId = $this->Id;
		$this->SetScrolling(($width === null || $height === null)?null:false);
		$this->SetCSSClass();
		if($width !== null)
			$this->SetWidth($width);
		if($height !== null)
			$this->SetHeight($height);
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
    		QueueClientFunction($this, '_NChange', array('\''.$this->Id.'\'', '\'scrollLeft\'', $scrollLeft), false, Priority::High);
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
    		QueueClientFunction($this, '_NChange', array('\''.$this->Id.'\'', '\'scrollTop\'', $scrollTop), false, Priority::High);
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
	 * 
	 * @param bool $openDialog Automatically launch the 
	 */
	function OpenPrintableVersion($openDialog=false)
	{
		ClientScript::AddNOLOHSource(UserAgent::GetBrowser() == 'op' ? 'Mixed/PrintableVersionOp.js' : 'Mixed/PrintableVersion.js');
		ClientScript::Queue($this, '_NOpenPrintable', array($this->Id, $openDialog));
	}
	/**
	 * Returns the kind of scroll bars the Panel will have, if any
	 * @return null|true|false|System::Auto|System::Full|System::Horizontal|System::Vertical
	 */
	function GetScrolling()
	{
		return $this->Scrolling;
	}
	/**
	 * Sets the kind of scroll bars the Panel will have, if any
	 * @param null|true|false|System::Auto|System::Full|System::Horizontal|System::Vertical $scrollType
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
	private function SetStretches()
	{
		if(!$this->Controls instanceof ImplicitArrayList)
		{
			ClientScript::AddNOLOHSource('StretchPanel.js');
			ClientScript::AddNOLOHSource('Dimensions.js', true);
			ClientScript::AddNOLOHSource('Animation.js', true);
			$this->Controls = $this->Controls->ToImplicit($this, 'ImplicitAdd');
		}
	}
	/**
	* @ignore
	*/
	function SetWidth($width)
	{
		if($width === System::Auto)
		{
			$this->SetStretches();
			ClientScript::Set($this, 'AutX', true, '_N');
			if($this->Controls->GetCount() > 0)
				ClientScript::Queue($this, '_NStrResize', $this);
			$width = 0;
		}
		elseif(parent::GetWidth() === System::Auto)
			ClientScript::Set($this, 'AutX', null, '_N');
		parent::SetWidth($width);	
	}
	/**
	* @ignore
	*/
	function SetHeight($height)
	{
		if($height === System::Auto)
		{
			$this->SetStretches();
			ClientScript::Set($this, 'AutY', true, '_N');
		}
		elseif(parent::GetHeight() === System::Auto)
			ClientScript::Set($this, 'AutY', null, '_N');
		parent::SetHeight($height);	
	}
	/**
	 * @ignore
	 */
	function ImplicitAdd($object)
	{
		if($object instanceof Control)
		{
			ClientScript::Queue($this, '_NStrPnlAdd', array($this, $object), false);
			$object->Leave['resize'] = new ClientEvent('_NStrResize', $this);
		}
		return $this->Controls->Add($object, true);
	}
	/**
	 * @ignore
	 */
	function GetSemantics()
	{
		$sem = parent::GetSemantics();
		return $sem === null ? System::Auto : ($sem === false ? null: $sem);
	}
	/**
	 * @ignore
	 */
	function SetSemantics($semantics)
	{
		return $this->Semantics = ($semantics === System::Auto ? null : ($semantics === null ? false : $semantics));
	}
	/**
	 * @ignore
	 */
	function GetSearchEngineTag()
	{
		if($this->Semantics === System::Auto)
		{
			if(empty($_SESSION['_NControlQueueDeep'][$this->Id]) || (count($arr = $_SESSION['_NControlQueueDeep'][$this->Id]) < 2) || ($this->GetParent('Link') !== null))
				return '';
			else
				foreach($arr as $id => $show)
				{
					$obj = GetComponentById($id);
					if($show && $obj && !($obj instanceof Panel) && !($obj instanceof Container) && !($obj instanceof Group))
						return Semantics::Grouped;
				}
			return '';
		}
		else 
			return ($this->Semantics === Semantics::Normal)
				? ''
				: $this->Semantics;
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		$tag = $this->GetSearchEngineTag();
		if($tag)
			echo '<', $tag, parent::SearchEngineShow(true), '>';
		$this->SearchEngineShowChildren();
		if($tag)
			echo '</', $tag, '>';
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShow($indent);
		if($str !== false)
		{
			echo $indent, '<DIV ', $str, ">\n";
			$this->NoScriptShowChildren($indent);
			echo $indent, "</DIV>\n";
		}
	}
}
?>