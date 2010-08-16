<?php
/**
 * Label class
 *
 * The Label is a Control that displays styled text to the user. Its text should be a simple string, styled by the properties
 * and\or CSS properties of the Label. The text should generally NOT contain any mark-up, in that case, one should use the
 * MarkupRegion class instead.
 *  
 * @package Controls/Core
 */
class Label extends Control 
{
	/**
	 * @ignore
	 */
	public $_NCaW;
	/**
	 * @ignore
	 */
	public $_NCaH;
	private $Align;
	private $VAlign;
	private $Bold;
	private $Font;
	private $FontWeight;
	private $LeftPadding;
	private $Overflow;
	private $EditInPlace;
	private $FontSize;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Label
	 * @param string $text 
	 * @param integer $left The left coordinate of this element
	 * @param integer $top The top coordinate of this element
	 * @param integer $width The width of this element
	 * @param integer $height The height of this element
	 * @return Label
	 */
	function Label($text='', $left = 0, $top = 0, $width = 83, $height = 18)
	{
		parent::Control($left, $top, null, null);
		parent::SetWidth($width);
		parent::SetHeight($height);
		$this->SetText($text);
		$this->SetCSSClass();
	}
	/**
	 * @ignore
	 */
	function SetText($text)
	{
		parent::SetText($text);
		$this->ResetCache();
		//NolohInternal::SetProperty('innerHTML', $text, $this);
		NolohInternal::SetProperty('innerHTML', preg_replace('(\r\n|\n|\r)', '<BR>', $text), $this);
		//QueueClientFunction($this, "SetLabelText", array("'$this->Id'", "'".preg_replace("(\r\n|\n|\r)", "<Nendl>", $newText)."'"));
	}
	/**
	 * @ignore
	 */
	function SetCSSClass($cssClass=null)
	{
		parent::SetCSSClass('NLabel '. $cssClass);
	}
	/**
	 * Returns the font size of the Label, in points. The default is 12.
	 * @return integer
	 */
	function GetFontSize()
	{
		return $this->FontSize ? $this->FontSize : 12;
	}
	/**
	 * Sets the font size of the Label, in points. The default is 12.
	 * @param integer $size
	 */
	function SetFontSize($size)
	{
		$this->FontSize = $size;
		$this->ResetCache();
		NolohInternal::SetProperty('style.fontSize', $size.'pt', $this);
	}
	/**
	 * @ignore
	 */
	function GetWidth()
	{
		$width = parent::GetWidth();
		if($width === System::Auto || $width === System::AutoHtmlTrim)
		{
			if($this->_NCaW === null)
				$this->AutoWidthHeight();
			return $this->_NCaW;
		}
		else 
			return $width;
	}
	/**
	 * @ignore
	 */
	function SetWidth($width)
	{
		parent::SetWidth($width);
		$this->ResetCache();
	}
	/**
	 * @ignore
	 */
	function GetHeight()
	{
		$height = parent::GetHeight();
		if($height === System::Auto || $height === System::AutoHtmlTrim)
		{
			if($this->_NCaH === null)
				$this->AutoWidthHeight();
			return $this->_NCaH;
		}
		else 
			return $height;
	}
	/**
	 * @ignore
	 */
	function SetHeight($height)
	{
		parent::SetHeight($height);
		$this->ResetCache();
	}
	
	private function ResetCache()
	{
		$this->_NCaW = null;
		$this->_NCaH = null;
		$width = parent::GetWidth();
		$height = parent::GetHeight();
		if($width==System::Auto || $width==System::AutoHtmlTrim || $height==System::Auto || $height==System::AutoHtmlTrim)
		{
			AddNolohScriptSrc('Auto.js');
			QueueClientFunction($this, '_NAWH', array('\''.$this->Id.'\''));
		}
		else
			unset($_SESSION['_NFunctionQueue'][$this->Id]['_NAWH']);
	}
	/**
	 * Returns the alignment of the Label, i.e., where the text will show with respect to the bounds of the Label. Possible values include Layout::Left, Layout::Center, or Layout::Right. 
	 * @return mixed
	 */
	function GetAlign()
	{
		return $this->Align == null ? 'left' : $this->Align;
	}
	/**
	 * Sets the alignment of the Label, i.e., where the text will show with respect to the horizontal bounds of the Label. Possible values include Layout::Left, Layout::Center, or Layout::Right. 
	 * @param mixed $align
	 */
	function SetAlign($align)
	{
		$this->Align = $align == 'left' ? null : $align;
		NolohInternal::SetProperty('style.textAlign', $align, $this);
	}
	/**
	 * Returns the vertical alignment of the Label, i.e., where the text will show with respect to the vertical bounds of the Label. Possible values include Layout::Top, Layout::Baseline, or Layout::Bottom.
	 * @return mixed
	 */
	function GetVAlign()
	{
		return $this->VAlign == null ? 'baseline' : $this->VAlign;
	}
	/**
	 * Sets the vertical alignment of the Label, i.e., where the text will show with respect to the vertical bounds of the Label. Possible values include Layout::Top, Layout::Baseline, or Layout::Bottom.
	 * @param mixed $vAlign
	 */
	function SetVAlign($vAlign)
	{
		$this->VAlign = $vAlign == 'baseline' ? null : $vAlign;
		NolohInternal::SetProperty('style.verticalAlign', $vAlign, $this);
	}
	/**
	 * Returns whether the Label will be bold
	 * @return boolean
	 */
	function GetBold()
	{
		return $this->Bold !== null;
	}
	/**
	 * Sets whether the Label will be bold
	 * @param boolean $bool
	 */
	function SetBold($bool)
	{
		if($bool)
		{
			$this->Bold = true;
			NolohInternal::SetProperty('style.fontWeight', 'bold', $this);
		}
		else 
		{
			$this->Bold = null;
			NolohInternal::SetProperty('style.fontWeight', 'normal', $this);
		}
	}
	/**
	 * Returns the name of the font of the Label, e.g., 'Arial'
	 * @return string
	 */
	function GetFont()
	{
		return $this->Font;
	}
	/**
	 * Sets the name of the font of the Label, e.g., 'Arial'
	 * @param string $font
	 */
	function SetFont($font)
	{
		$this->Font = $font;
		NolohInternal::SetProperty('style.fontFamily', $font, $this);
	}
	/**
	 * @ignore
	 */
	function GetLeftPadding()
	{
		return $this->LeftPadding == null ? 0 : $this->LeftPadding;
	}
	/**
	 * @ignore
	 */
	function SetLeftPadding($padding)
	{
		$this->LeftPadding = $padding == 0 ? null : $padding;
		NolohInternal::SetProperty('style.paddingLeft', $padding.'px', $this);
	}
	/**
	 * Returns whether the text will overflow beyond the bounds of the Label
	 * @return boolean
	 */
	function GetOverflow()
	{
		return $this->Overflow !== null;
	}
	/**
	 * Sets whether the text will overflow beyond the bounds of the Label
	 * @param boolean $bool
	 */
	function SetOverflow($bool)
	{
		if($bool)
		{
			$this->Overflow = true;
			NolohInternal::SetProperty('style.overflow', 'visible', $this);
		}
		else 
		{
			$this->Overflow = null;
			NolohInternal::SetProperty('style.overflow', 'hidden', $this);
		}
	}
	/**
	 * Returns whether the user will be able to edit the Label in place. If they can, the Label will have a DoubleClick Event
	 * that turns it into a TextBox to edit its Text.
	 * @return boolean
	 */
	function GetEditInPlace()
	{
		return $this->EditInPlace !== null;
	}
	/**
	 * Sets whether the user will be able to edit the Label in place. If they can, the Label will have a DoubleClick Event
	 * that turns it into a TextBox to edit its Text.
	 * @param boolean $bool
	 */
	function SetEditInPlace($bool)
	{
		if($bool)
		{
			$this->DoubleClick = new ServerEvent($this, 'EditStart');
			$this->EditInPlace = true;
		}
		else 
		{
			$this->DoubleClick = null;
			$this->EditInPlace = null;
		}
	}
	/**
	 * @ignore
	 */
	function EditStart()
	{
		$txt = new TextBox($this->Left, $this->Top, $this->Width, $this->Height);
		$txt->Text = $this->Text;
		$this->SetVisible(false);
		$txt->LoseFocus = new ClientEvent('_NSave(\''.$txt->Id.'\',\'value\');');
		$txt->LoseFocus[] = new ServerEvent($txt, 'EditComplete', $this->Id);
		$txt->ReturnKey = new ClientEvent('_N(\''.$txt->Id.'\').blur();');
		$this->Parent->Controls->Add($txt);
		AddScript('_N(\''.$txt->Id.'\').select();');
	}
	
	private function AutoWidthHeight()
	{
		$width = parent::GetWidth();
		$height = parent::GetHeight();
		
		if($width == System::Auto || $height == System::Auto)
			$widthHeight = AutoWidthHeight($this->Text, $width, $height, $this->GetFontSize());
		elseif($width == System::AutoHtmlTrim || $height == System::AutoHtmlTrim)
			$widthHeight = AutoWidthHeight(strip_tags(html_entity_decode($this->Text)), $width, $height, $this->GetFontSize());
		else
			return;
		if($width == System::Auto || $width == System::AutoHtmlTrim)
		{
			$this->_NCaW = $widthHeight[0];
			//NolohInternal::SetProperty('style.width', $this->_NCaW.'px', $this);
		}
		if($height == System::Auto || $height == System::AutoHtmlTrim)
		{
			$this->_NCaH = $widthHeight[1];
			//NolohInternal::SetProperty('style.height', $this->_NCaH.'px', $this);
		}
		//if(isset($_SESSION['_NFunctionQueue'][$this->Id]))
		//	unset($_SESSION['_NFunctionQueue'][$this->Id]['_NAWH']);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		//$initialProperties = parent::Show();
		//$initialProperties .= ",'style.wordWrap','break-word','style.overflow','hidden'";
		NolohInternal::Show('DIV', parent::Show(), $this);
		//return $initialProperties;
	}
	/**
	 * @ignore
	 */
	function GetSearchEngineTag()
	{
		if($this->Semantics === System::Auto || $this->Semantics === Semantics::Heading)
		{
			return 'SPAN';
		}
		else
			return ($this->Semantics === Semantics::Normal)
				? 'SPAN'
				: $this->Semantics;
	}
	/**
	 * @ignore
	 *
	function SearchEngineShow()
	{
		if($this->Text)
			echo '<P>', $this->Text, '</P>';
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShow($indent);
		if($str !== false)
			echo $indent, '<DIV ', $str, '>', $this->Text, "</DIV>\r\n";
	}
}
?>