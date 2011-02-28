<?php
/**
 * MarkupRegion class
 *
 * A MarkupRegion is a Control that is capable of displaying a string or file containing mark-up. Any HTML elements
 * contained in that mark-up gives up some of the benefits of NOLOH (such as user state management) so you should
 * probably not put a tag like INPUT. Indeed, MarkupRegion was specifically designed to take in the kind
 * of mark-up that mark-up was originally designed for and is good at: styled text and possibly images.
 * 
 * <pre>
 * $content = new MarkupRegion('Path/To/Markup/content.htm');
 * </pre>
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
	protected $InnerCSSClass;
	//private $FontSize;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends MarkupRegion
	 * @param string|file $markupStringOrFile A string of mark-up or a path to a file containing mark-up
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 * @return MarkupRegion
	 */
	function MarkupRegion($markupStringOrFile, $left=0, $top=0, $width = 200, $height = 200)
	{
		parent::Control($left, $top, $width, $height);
		$this->SetScrolling(System::Auto);
		//$this->AutoScroll = true;
		if($markupStringOrFile !== null)
			$this->SetText($markupStringOrFile);
	}
	/**
	 * @ignore
	 */
	function GetFontSize()
	{
		return 12;
	}
	/**
	* Sets the CSSClass for the content of the MarkupRegion. This is useful for adding things like Margin around your content.
	* 
	* @param string $className
	*/
	function SetInnerCSSClass($className)
	{
		$this->InnerCSSClass = $className;
		if($text = $this->GetText())
			$this->SetText($text);
	}
	/**
	* Returns the CSSClass for the content of the MarkupRegion. This is useful for adding things like Margin around your content.
	* 
	*/
	function GetInnerCSSClass()	{return $this->InnerCSSClass;}
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
     * Returns the Scroll Event, which gets launched when a user scrolls through the MarkupRegion
     * @return Event
     */
	function GetScroll()							{return $this->GetEvent('Scroll');}
	/**
	 * Sets the Scroll Event, which gets launched when a user scrolls through the MarkupRegion
	 * @param Event $scroll
	 */
	function SetScroll($scroll)						{$this->SetEvent($scroll, 'Scroll');}
	/**
	 * Returns the kind of scroll bars the MarkupRegion will have, if any
	 * @return null|true|false|System::Auto|System::Full|System::Horizontal|System::Vertical
	 */
    function GetScrolling()
	{
		return $this->Scrolling;
	}
	/**
	 * Sets the kind of scroll bars the MarkupRegion will have, if any
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
    		ClientScript::Queue($this, '_NChange', array($this->Id, 'scrollTop', $scrollTop), false, Priority::High);
//    		QueueClientFunction($this, '_NChange', array('\''.$this->Id.'\'', '\'scrollTop\'', $scrollTop), false, Priority::High);
    	else
    		ClientScript::Set($this, 'scrollTop', $scrollTop, null);
//        	NolohInternal::SetProperty('scrollTop', $scrollTop, $this);
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
        parent::SetText($markupStringOrFile);
		$markupStringOrFile =  str_replace(array("\r\n", "\n", "\r", '"', '\'', '\\'), array('<Nendl>', '<Nendl>', '<Nendl>', '<NQt2>', '<NQt1>', '\\\\'), ($tmpFullString = ((is_file($markupStringOrFile))?file_get_contents($markupStringOrFile):$markupStringOrFile)));
		$this->AutoWidthHeight($tmpFullString);
		if(isset($this->InnerCSSClass))
			 $markupStringOrFile = "<div id = 'Inner{$this->Id}' class = '{$this->InnerCSSClass}'>$markupStringOrFile</div>";
//			 $markupStringOrFile = '<div class = \''. $this->InnerCSSClass . '\'>' . $markupStringOrFile . '</div>';
		ClientScript::Queue($this, '_NMkupSet', array($this, $markupStringOrFile));
//		QueueClientFunction($this, '_NMkupSet', array('\''.$this->Id.'\'', '\''.$markupStringOrFile.'\''));
	}
	/**
	 * Styles a string of text by giving it a CSS class
	 * @deprecated Use System::Style() instead.
	 * @param string $text The string to be styled
	 * @param string $class The name of the CSS class
	 * @return string
	 */
	static function StyleText($text, $class, $newLine = false)
	{
		$styled = '<span class=\''.$class.'\'>'.$text.'</span>';
		if($newLine)
			$styled .= '<br/>';
		return $styled;
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		ClientScript::AddNOLOHSource('MarkupRegion.js');
		NolohInternal::Show('DIV', parent::Show(), $this);
	}
	/**
	 * @ignore
	 */
	function GetSearchEngineTag()
	{
		if($this->Semantics === System::Auto)
		{
			return 'DIV';
		}
		else 
			return ($this->Semantics === Semantics::Normal)
				? 'DIV'
				: $this->Semantics;
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		echo '<', $tag = $this->GetSearchEngineTag(), parent::SearchEngineShow(true),'>',preg_replace('/<([^<>]* )target\s*=([\'"])\w+\2\s*([^<>]*)>/', '<$1$3>', is_file($this->Text)?file_get_contents($this->Text):$this->Text), '</',$tag,'>';
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = Control::NoScriptShow($indent);
		if($str !== false)
			echo $indent, '<DIV ', $str, ">\n", is_file($this->Text)?file_get_contents($this->Text):$this->Text, "\n", $indent, "</DIV>\n";
	}
}

?>