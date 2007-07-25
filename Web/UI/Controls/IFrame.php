<?php

/**
 * IFrame class file.
 */
 
/**
 * IFrame class
 *
 * A Control for an IFrame
 *
 * Properties
 * - <b>Src</b>, string, 
 *   <br>Gets or Sets the IFrame's source string
 * 
 * You can use the IFrame as follows
 * <code>
 *
 *		function Foo()
 *		{
 *			$tempIFrame = new IFrame("http://www.noloh.com", 10, 10, 200, 300);
 * 			$this->Controls->Add($tempIFrame); //Adds an IFrame to the Controls class of some Container
 *		}
 *		
 * </code>
 */

class IFrame extends Control
{
	/**
 	* Src, The source of the IFrame, this is where the IFrame will direct to.
 	* @var string
 	*/
	private $Src;
	/**
	*ScrollLeft of the component
	*@var integer
	*/
	private $ScrollLeft;
	/**
	*ScrollTop of the component
	*@var integer
	*/
	private $ScrollTop;
	private $Scrolling;

	/**
	* Constructor.
	* for inherited components, be sure to call the parent constructor first
 	* so that the component properties and events are defined.
 	* Example
 	*	<code> $tempVar = new IFrame("http://www.noloh.com", 0, 0, 100, 100);</code>
 	* @param string|optional
	* @param integer|optional
	* @param integer|optional
	* @param integer|optional
	* @param integer|optional
	*/
	function IFrame($src ="", $left=0, $top=0, $width=300, $height=300)
	{
		Control::Control($left, $top, $width, $height);
		$this->HtmlName = $this->Id;
		$this->SetSrc($src);
	}
	
	function GetSrc()
	{
		return $this->Src;
	}
	
	function SetSrc($newSrc)
	{
		$this->Src = $newSrc;
		NolohInternal::SetProperty("src", $newSrc, $this);
	}
	
	function GetScrollLeft()
	{
		return $this->ScrollLeft;
	}
	
	function SetScrollLeft($newScrollLeft)
	{
		$this->ScrollLeft = $newScrollLeft;
		NolohInternal::SetProperty("scrollLeft", $newScrollLeft, $this);
	}
	
	function GetScrollTop()
	{
		return $this->ScrollTop;
	}
	
	function SetScrollTop($newScrollTop)
	{
		$this->ScrollTop = $newScrollTop;
		NolohInternal::SetProperty("scrollTop", $newScrollTop, $this);
	}
	function SetScrolling($scrollType)
	{
		$this->Scrolling = $scrollType;
		$tmpScroll = null;
		if($scrollType == System::Auto)
			$tmpScroll = "auto";
		elseif($scrollType)
			$tmpScroll = "yes";
		elseif(!$scrollType)
			$tmpScroll = "no";
		elseif($scrollType === null)
			$tmpScroll = "";
		//Alert($tmpScroll);
		NolohInternal::SetProperty("scrolling", $tmpScroll, $this);
	}
	function GetScrolling(){return $this->Scrolling;}
	/**
	* @ignore
	*/
	function Show()
	{
		$initialProperties = parent::Show();
		$initialProperties .= ",'frameborder','no'";
		NolohInternal::Show("IFRAME", $initialProperties, $this);
	}
}
	
?>