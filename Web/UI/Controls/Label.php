<?php
/**
 * @package Web.UI.Controls
 */
class Label extends Control 
{
	public $CachedWidth;
	public $CachedHeight;
	private $Align;
	private $VAlign;
	private $Bold;
	private $Font;
	private $FontWeight;
	private $LeftPadding;
	private $Overflow;
	private $EditInPlace;
	private $FontSize;
	
	function Label($text='', $left = 0, $top = 0, $width = 83, $height = 18)
	{
		parent::Control($left, $top, null, null);
		parent::SetWidth($width);
		parent::SetHeight($height);
		parent::SetText($text);
		NolohInternal::SetProperty('innerHTML', preg_replace('(\r\n|\n|\r)', '<BR>', $text), $this);
		$this->SetCSSClass();
		$this->ResetCache();
	}
	
	function SetText($text)
	{
		parent::SetText($text);
		//$width = parent::GetWidth();
		//$height = parent::GetHeight();
		//$this->AutoWidthHeight();
		$this->ResetCache();
		NolohInternal::SetProperty('innerHTML', preg_replace('(\r\n|\n|\r)', '<BR>', $text), $this);
		//QueueClientFunction($this, "SetLabelText", array("'$this->Id'", "'".preg_replace("(\r\n|\n|\r)", "<Nendl>", $newText)."'"));
	}
	
	function SetCSSClass($cssClass=null)
	{
		parent::SetCSSClass('NLabel '. $cssClass);
	}
	
	function GetFontSize()
	{
		return isset($this->FontSize) ? $this->FontSize : 12;
	}
	
	function SetFontSize($newSize)
	{
		$this->FontSize = $newSize;
		$this->AutoWidthHeight();
		NolohInternal::SetProperty('style.fontSize', $this->FontSize.'px', $this);
	}
	
	function GetWidth()
	{
		$width = parent::GetWidth();
		if($width == System::Auto || $width == System::AutoHtmlTrim)
		{
			if($this->CachedWidth == null)
				$this->AutoWidthHeight();
			return $this->CachedWidth;
		}
		else 
			return $width;
	}
	
	function SetWidth($newWidth)
	{
		parent::SetWidth($newWidth);
		$this->ResetCache();
	}
	
	function GetHeight()
	{
		$height = parent::GetHeight();
		if($height == System::Auto || $height == System::AutoHtmlTrim)
		{
			if($this->CachedHeight == null)
				$this->AutoWidthHeight();
			return $this->CachedHeight;
		}
		else 
			return $height;
	}
	
	function SetHeight($newHeight)
	{
		parent::SetHeight($newHeight);
		$this->ResetCache();
	}
	
	private function ResetCache()
	{
		$this->CachedWidth = null;
		$this->CachedHeight = null;
		$width = parent::GetWidth();
		$height = parent::GetHeight();
		if($width==System::Auto || $width==System::AutoHtmlTrim || $height==System::Auto || $height==System::AutoHtmlTrim)
			QueueClientFunction($this, '_NAWH', array("'$this->Id'"));
	}
	
	function GetAlign()
	{
		return $this->Align == null ? 'left' : $this->Align;
	}
	
	function SetAlign($newAlign)
	{
		$this->Align = $newAlign == 'left' ? null : $newAlign;
		NolohInternal::SetProperty('style.textAlign', $newAlign, $this);
	}
	
	function GetVAlign()
	{
		return $this->VAlign == null ? 'baseline' : $this->VAlign;
	}
	
	function SetVAlign($newVAlign)
	{
		$this->VAlign = $newVAlign == 'baseline' ? null : $newVAlign;
		NolohInternal::SetProperty('style.verticalAlign', $newVAlign, $this);
	}
	
	function GetBold()
	{
		return $this->Bold == null ? false : true;
	}
	
	function SetBold($whatBool)
	{
		if($whatBool)
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
	
	function GetFont()
	{
		return $this->Font;
	}
	
	function SetFont($newFont)
	{
		$this->Font = $newFont;
		NolohInternal::SetProperty('style.fontFamily', $newFont, $this);
	}
	
	function GetLeftPadding()
	{
		return $this->LeftPadding == null ? 0 : $this->LeftPadding;
	}
	
	function SetLeftPadding($newPadding)
	{
		$this->LeftPadding = $newPadding == 0 ? null : $newPadding;
		NolohInternal::SetProperty('style.paddingLeft', $newPadding.'px', $this);
	}
	
	function GetOverflow()
	{
		return $this->Overflow == null ? false : true;
	}
	
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
	
	function GetEditInPlace()
	{
		return $this->EditInPlace == null ? false : true;
	}
	
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
	
	function EditStart()
	{
		$txt = new TextBox($this->Left, $this->Top, $this->Width, $this->Height);
		$txt->Text = $this->Text;
		$this->ClientVisible = false;
		$txt->LoseFocus = new ClientEvent("_NSave('$txt->Id','value');");
		$txt->LoseFocus[] = new ServerEvent($txt, 'EditComplete', $this->Id);
		$txt->ReturnKey = new ClientEvent("document.getElementById('$txt->Id').blur()");
		$this->Parent->Controls->Add($txt);
		AddScript("document.getElementById('$txt->Id').select();");
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
			$this->CachedWidth = $widthHeight[0];
			NolohInternal::SetProperty('style.width', $this->CachedWidth.'px', $this);
		}
		if($height == System::Auto || $height == System::AutoHtmlTrim)
		{
			$this->CachedHeight = $widthHeight[1];
			NolohInternal::SetProperty('style.height', $this->CachedHeight.'px', $this);
		}
	}
	
	function Show()
	{
		$initialProperties = parent::Show();
		//$initialProperties .= ",'style.wordWrap','break-word','style.overflow','hidden'";
		NolohInternal::Show('DIV', $initialProperties, $this);
		return $initialProperties;
	}
}
?>