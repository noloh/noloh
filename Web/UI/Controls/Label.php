<?php

class Label extends Control 
{
	private $Align;
	private $VAlign;
	private $Bold;
	private $Font;
	private $FontWeight;
	private $LeftPadding;
	private $Overflow;
	private $EditInPlace;
	private $CachedWidth;
	private $CachedHeight;
	private $FontSize;
	
	function Label($whatText="", $whatLeft = 0, $whatTop = 0, $whatWidth = 83, $whatHeight = 18)
	{
		parent::Control($whatLeft, $whatTop, $whatWidth, $whatHeight);
		$this->SetCSSClass();
		$this->SetText($whatText);
	}
	
	function SetText($newText)
	{
		parent::SetText($newText);
		//$width = parent::GetWidth();
		//$height = parent::GetHeight();
		$this->AutoWidthHeight();
		NolohInternal::SetProperty("innerHTML", preg_replace("(\r\n|\n|\r)", "<BR>", $newText), $this);
		//QueueClientFunction($this, "SetLabelText", array("'$this->DistinctId'", "'".preg_replace("(\r\n|\n|\r)", "<Nendl>", $newText)."'"));
	}
	function SetCSSClass($cssClass=null)
	{
		parent::SetCSSClass("NLabel ".$cssClass);
	}
	function GetFontSize()
	{
		return isset($this->FontSize) ? $this->FontSize : 12;
	}
	
	function SetFontSize($newSize)
	{
		$this->FontSize = $newSize;
		$this->AutoWidthHeight();
		NolohInternal::SetProperty("style.fontSize",$this->FontSize."px",$this);
	}
	
	function GetWidth()
	{
		$Width = parent::GetWidth();
		return ($Width == System::Auto || $Width == System::AutoHtmlTrim) ? $this->CachedWidth : $Width;
	}
	
	function GetHeight()
	{
		$Height = parent::GetHeight();
		return ($Height == System::Auto || $Height == System::AutoHtmlTrim)? $this->CachedHeight : $Height;
	}
	
	function GetAlign()
	{
		return $this->Align == null ? "left" : $this->Align;
	}
	
	function SetAlign($newAlign)
	{
		$this->Align = $newAlign == "left" ? null : $newAlign;
		NolohInternal::SetProperty("style.textAlign", $newAlign, $this);
	}
	
	function GetVAlign()
	{
		return $this->VAlign == null ? "baseline" : $this->VAlign;
	}
	
	function SetVAlign($newVAlign)
	{
		$this->VAlign = $newVAlign == "baseline" ? null : $newVAlign;
		NolohInternal::SetProperty("style.verticalAlign", $newVAlign, $this);
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
			NolohInternal::SetProperty("style.fontWeight", "bold", $this);
		}
		else 
		{
			$this->Bold = null;
			NolohInternal::SetProperty("style.fontWeight", "normal", $this);
		}
	}
	
	function GetFont()
	{
		return $this->Font;
	}
	
	function SetFont($newFont)
	{
		$this->Font = $newFont;
		NolohInternal::SetProperty("style.fontFamily", $newFont, $this);
	}
	
	function GetLeftPadding()
	{
		return $this->LeftPadding == null ? 0 : $this->LeftPadding;
	}
	
	function SetLeftPadding($newPadding)
	{
		$this->LeftPadding = $newPadding == 0 ? null : $newPadding;
		NolohInternal::SetProperty("style.paddingLeft", $newPadding."px", $this);
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
			NolohInternal::SetProperty("style.overflow", "visible", $this);
		}
		else 
		{
			$this->Overflow = null;
			NolohInternal::SetProperty("style.overflow", "hidden", $this);
		}
	}
	
	function GetEditInPlace()
	{
		return $this->EditInPlace == null ? false : true;
	}
	
	function SetEditInPlace($whatBool)
	{
		if($whatBool === true)
			$this->DoubleClick = new ServerEvent($this, "EditStart");
		else 
			$this->DoubleClick = null;
			
		$this->EditInPlace = $whatBool == false ? null : true;
	}
	
	function EditStart()
	{
		$txt = new TextBox($this->Left, $this->Top, $this->Width, $this->Height);
		$txt->Text = $this->Text;
		$this->ClientVisible = false;
		$txt->LoseFocus = new ClientEvent("_NSave('$txt->DistinctId','value');");
		$txt->LoseFocus[] = new ServerEvent($txt, "EditComplete", $this->DistinctId);
		$txt->ReturnKey = new ClientEvent("document.getElementById('$txt->DistinctId').blur()");
		$this->Parent->Controls->Add($txt);
		AddScript("document.getElementById('$txt->DistinctId').select();");
	}
	
	private function AutoWidthHeight()
	{
		$width = parent::GetWidth();
		$height = parent::GetHeight();
		//Added Strip Tags
		
		if($width == System::Auto || $height == System::Auto)
			$widthHeight = AutoWidthHeight($this->Text, $width, $height, $this->GetFontSize());
		elseif($width == System::AutoHtmlTrim || $height == System::AutoHtmlTrim)
			$widthHeight = AutoWidthHeight(strip_tags(html_entity_decode($this->Text)), $width, $height, $this->GetFontSize());
		else
			return;
		if($width == System::Auto || $width == System::AutoHtmlTrim)
		{
			$this->CachedWidth = $widthHeight[0];
			NolohInternal::SetProperty("style.width", $this->CachedWidth."px", $this);
		}
		if($height == System::Auto || $height == System::AutoHtmlTrim)
		{
			$this->CachedHeight = $widthHeight[1];
			NolohInternal::SetProperty("style.height", $this->CachedHeight."px", $this);
		}
	}
	
	function Show()
	{
		$initialProperties = parent::Show();
		//$initialProperties .= ",'style.wordWrap','break-word','style.overflow','hidden'";
		NolohInternal::Show("DIV", $initialProperties, $this);
		return $initialProperties;
	}
}
?>