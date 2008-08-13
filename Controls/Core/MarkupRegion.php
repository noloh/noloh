<?php
/**
 * MarkupRegion class
 *
 * A MarkupRegion is a Control that is capable of displaying a string or file containing mark-up. Any HTML elements
 * contained in that mark-up gives up some of the benefits of NOLOH (such as user state management) so you should
 * probably not put a tag like INPUT. Indeed, MarkupRegion was specifically designed to take in the kind
 * of mark-up that mark-up was originally designed for and is good at: styled text and possibly images.
 * 
 * @package Controls/Core
 */
class MarkupRegion extends Control
{
	private $CachedWidth;
	private $CachedHeight;
    private $Scrolling;
    private $ScrollLeft;
	private $ScrollTop;
	//private $FontSize;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends MarkupRegion
	 * @param string|file $markupStringOrFile A string of mark-up or a path to a file containing mark-up
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width coordinate of this element
	 * @param integer $height The Height coordinate of this element
	 * @return MarkupRegion
	 */
	function MarkupRegion($markupStringOrFile, $left=0, $top=0, $width = 200, $height = 200)
	{
		parent::Control($left, $top, $width, $height);
		$this->SetScrolling(System::Auto);
		//$this->AutoScroll = true;
		$this->SetText($markupStringOrFile);
	}
	/**
	 * @ignore
	 */
	function GetFontSize()
	{
		return 12;
	}
//	function SetFontSize($newSize)
//	{
//		$this->FontSize = $newSize;
//		$this->AutoWidthHeight();
//		NolohInternal::SetProperty("style.fontSize",$this->FontSize."px",$this);
//	}
	/**
	 * @ignore
	 */
	function GetWidth()
	{
		$Width = parent::GetWidth();
		return ($Width == System::Auto || $Width == System::AutoHtmlTrim) ? $this->CachedWidth : $Width;
	}
	/**
	 * @ignore
	 */
	function GetHeight()
	{
		$Height = parent::GetHeight();
		return ($Height == System::Auto || $Height == System::AutoHtmlTrim)? $this->CachedHeight : $Height;
	}
	/**
	 * Returns the kind of scroll bars the MarkupRegion will have, if any
	 * @return mixed
	 */
    function GetScrolling()
	{
		return $this->Scrolling;
	}
	/**
	 * Sets the kind of scroll bars the MarkupRegion will have, if any
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
	//function GetMarkupString()
	//{
	//	return $this->MarkupString;
	//}
	//TODO, DON'T LIKE THAT THIS IS REPEATED FROM LABEL, BUT, MarkupRegion IS A PANEL, AND NOT A LABEL, SOMETHING TO THINK ABOUT - Asher
	/**
	 * @ignore
	 */
	protected function AutoWidthHeight($markup)
	{
		$width = parent::GetWidth();
		$height = parent::GetHeight();
		//Added Strip Tags
		
		if($width == System::Auto || $height == System::Auto)
			$widthHeight = AutoWidthHeight($markup, $width, $height, $this->GetFontSize());
		elseif($width == System::AutoHtmlTrim || $height == System::AutoHtmlTrim)
			$widthHeight = AutoWidthHeight(strip_tags(html_entity_decode($markup)), $width, $height, $this->GetFontSize());
		else
			return;
		if($width == System::Auto || $width == System::AutoHtmlTrim)
		{
			$this->CachedWidth = $widthHeight[0];
			NolohInternal::SetProperty('style.width', $this->CachedWidth.'px', $this);
		}
		if($height == System::Auto || $height == System::AutoHtmlTrim)
		{
			$this->CachedHeight = $widthHeight[1];
			NolohInternal::SetProperty('style.height', $this->CachedHeight.'px', $this);
		}
	}
	/**
	 * Sets the MarkupRegion to a string of mark-up or a file containing mark-up
	 * @param string|file $markupStringOrFile
	 */
    function SetText($markupStringOrFile)
	{

		//$this->IsFile = (is_file($markupStringOrFile))? true : false;
        parent::SetText($markupStringOrFile);
//		if(is_file($markupStringOrFile))
//		{
			$markupStringOrFile =  str_replace(array("\r\n", "\n", "\r", '"', '\'', '\\'), array('<Nendl>', '<Nendl>', '<Nendl>', '<NQt2>', '<NQt1>', '\\\\'), ($tmpFullString = ((is_file($markupStringOrFile))?file_get_contents($markupStringOrFile):$markupStringOrFile)));
			$this->AutoWidthHeight($tmpFullString);
			QueueClientFunction($this, 'SetMarkupString', array('\''.$this->Id.'\'', '\''.$markupStringOrFile.'\''));
//		}
//		else
//			NolohInternal::SetProperty("innerHTML", $markupStringOrFile, $this);
	}
		//$this->MarkupString = file_get_contents($markupStringOrFile);
		//if($this->MarkupString == false)
			//$this->MarkupString = $markupStringOrFile;
//	}
	/**
	 * @ignore
	 */
	function Show()
	{
        NolohInternal::Show('DIV', parent::Show(), $this);
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/MarkupRegionScript.js");
		AddNolohScriptSrc('MarkupRegion.js');
	}
	/**
	 * Styles a string of text by giving it a CSS class
	 * @param string $text The string to be styled
	 * @param string $class The name of the CSS class
	 * @return string
	 */
	static function StyleText($text, $class)
	{
		return '<span class=\''.$class.'\'>'.$text.'</span>';
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		echo is_file($this->Text)?file_get_contents($this->Text):$this->Text, ' ';
	}
}

?>