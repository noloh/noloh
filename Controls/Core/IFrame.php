<?php
/**
 * IFrame class
 *
 * An IFrame is a Control for a conventional web iframe. Roughly speaking, is a mini-browser that runs within a browser. 
 * 
 * You can use the IFrame as follows
 * <pre>
 * function Foo()
 * {
 * 	// Instantiates a new IFrame that navigates to noloh.com
 *	$tempIFrame = new IFrame("http://www.noloh.com", 10, 10, 200, 300);
 *  // Adds an IFrame to the Controls of some object
 * 	$this->Controls->Add($tempIFrame); 
 * }
 * </pre>
 * 
 * @package Controls/Core
 */

class IFrame extends Control
{
	private $Src;
	private $ScrollLeft;
	private $ScrollTop;
	private $Scrolling;
	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends IFrame
 	* Example
 	*	<pre> $tempVar = new IFrame("http://www.noloh.com", 0, 0, 100, 100);</pre>
 	* @param string $src The URL to which the IFrame navigates
	* @param integer $left
	* @param integer $top
	* @param integer $width
	* @param integer $height
	* @return IFrame
	*/
	function IFrame($src ='', $left=0, $top=0, $width=300, $height=300)
	{
		parent::Control($left, $top, $width, $height);
		$this->SetSrc($src);
	}
	/**
	 * Returns the source of the IFrame, i.e., the URL to which it navigates
	 * @return string
	 */
	function GetSrc()
	{
		return $this->Src;
	}
	/**
	 * Sets the source of the IFrame, thereby navigating to a different location
	 * @param string $src
	 */
	function SetSrc($src)
	{
		$this->Src = $newSrc;
		NolohInternal::SetProperty('src', $src, $this);
	}
	/**
	 * @ignore
	 */
	function GetScrollLeft()
	{
		return $this->ScrollLeft;
	}
	/**
	 * Scrolls the IFrame horizontally to a given position
	 * @param integer $scrollLeft
	 */
	function SetScrollLeft($scrollLeft)
	{
		$scrollLeft = $scrollLeft==Layout::Left?0: $scrollLeft==Layout::Right?9999: $scrollLeft;
		$this->ScrollLeft = $scrollLeft;
		NolohInternal::SetProperty('scrollLeft', $scrollLeft, $this);
	}
	/**
	 * @ignore
	 */
	function GetScrollTop()
	{
		return $this->ScrollTop;
	}
	/**
	 * Scrolls the IFrame vertically to a given position
	 * @param integer $scrollTop
	 */
	function SetScrollTop($scrollTop)
	{
		$scrollTop = $scrollTop==Layout::Top?0: $scrollTop==Layout::Bottom?9999: $scrollTop;
		$this->ScrollTop = $scrollTop;
		NolohInternal::SetProperty('scrollTop', $scrollTop, $this);
	}
	/**
	 * Returns the kind of scroll bars the IFrame will have, if any
	 * @return mixed
	 */
	function GetScrolling()
	{
		return $this->Scrolling;
	}
	/**
	 * Sets the kind of scroll bars the IFrame will have, if any
	 * @param mixed $scrollType
	 */
	function SetScrolling($scrollType)
	{
		$this->Scrolling = $scrollType;
		$tmpScroll = null;
		if($scrollType == System::Auto)
			$tmpScroll = 'auto';
		elseif($scrollType)
			$tmpScroll = 'yes';
		elseif(!$scrollType)
			$tmpScroll = 'no';
		elseif($scrollType === null)
			$tmpScroll = '';
		NolohInternal::SetProperty('scrolling', $tmpScroll, $this);
	}
	/**
	* @ignore
	*/
	function Show()
	{
		$initialProperties = parent::Show() . '\'frameBorder\',\'no\'';
		if(UserAgent::IsIE())
			NolohInternal::Show('<IFRAME name="'.$this->Id.'">', $initialProperties, $this);
		else
		{
			$initialProperties .= ',"name","' . $this->Id . '"';
			NolohInternal::Show('IFRAME', $initialProperties, $this);
		}
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		echo '<A href="', $this->Src, '">', $this->Src, '</A> ';
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShow($indent);
		if($str !== false)
			echo $indent, '<IFRAME src="', $this->Src, '" ', $str, "></INPUT>\n";
	}
}
	
?>