<?php
/**
 * TextArea class
 *
 * A TextArea is a Control for a conventional web text area. It allows the user to type in multiple lines of text.
 * If you want to allow the user to type in only a single line of text, then you are looking for the TextBox Control
 * instead.
 * 
 * @package Controls/Core
 */
class TextArea extends Control 
{
	private $MaxLength;
	private $Scrolling;	
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends TextArea
	 * @param string $text The Text of this element
	 * @param integer $left The left coordinate of this element
	 * @param integer $top The top coordinate of this element
	 * @param integer $width The width of this element
	 * @param integer $height The height of this element
	 * @param integer $maxLength The maximum number of characters that are allowed in the TextArea
	 * @return TextArea
	 */
	function TextArea($text = null, $left = 0, $top = 0, $width = 200, $height = 100, $maxLength = null)  
	{
		parent::Control($left, $top, $width, $height);
		$this->SetMaxLength($maxLength);
		if($text != null)
			$this->SetText($text);
	}
	/**
	 * Returns the maximum number of characters that are allowed in the TextArea
	 * @return integer
	 */
	function GetMaxLength()
	{
		return $this->MaxLength;
	}
	/**
	 * Sets the maximum number of characters that are allowed in the TextArea
	 * @param integer $maxLength
	 */
	function SetMaxLength($maxLength)
	{
		$this->MaxLength = $maxLength;
		NolohInternal::SetProperty('MaxLength', $maxLength===null ? -1 : $maxLength, $this);
	}
	/**
	 * @ignore
	 */
	function SetText($text)
	{
		parent::SetText($text);
		QueueClientFunction($this, '_NTATxt', array('\''.$this->Id.'\'', '\''.preg_replace("(\r\n|\n|\r)", '<Nendl>', addslashes($text)).'\''));
	}
	/*function GetScrollLeft()
	{
		return $this->ScrollLeft;
	}*/
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
    /*function GetScrollTop()
    {
    	return $this->ScrollTop;
    }*/
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
     * Returns the Scroll Event, which gets launched when a user scrolls through the TextArea
     * @return Event
     */
	function GetScroll()							{return $this->GetEvent('Scroll');}
	/**
	 * Sets the Scroll Event, which gets launched when a user scrolls through the TextArea
	 * @param Event $scroll
	 */
	function SetScroll($scroll)						{$this->SetEvent($scroll, 'Scroll');}
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === null)
			return ',\'onchange\',\''.$this->GetEventString('Change').'\',\'onfocus\',\''.$this->GetEventString('Focus')/*."','onblur','".$this->GetEventString('LoseFocus')*/."'" .
				(UserAgent::IsIE()
				?
					',\'onkeypress\',\'_NTAPress("'.$this->Id.'",this.MaxLength);\'' .
					',\'onpaste\',\'_NTAPaste("'.$this->Id.'",this.MaxLength);\''
				:
					',\'onkeypress\',\'_NTAPress();\'');

		$preStr = '';
        switch($eventTypeAsString)
        {
			case 'Click':
            case 'Change':
			case 'DoubleClick':
			case 'LoseFocus':
                $preStr = '_NSave("'.$this->Id.'","value");';
                break;
            case 'Focus':
                $preStr = '_N.EventVars.FocusedComponent="'.$this->Id.'";';
                break;
        }
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
	/**
	 * Returns the kind of scroll bars the TextArea will have, if any
	 * @return mixed
	 */
	function GetScrolling()
	{
		return $this->Scrolling;
	}
	/**
	 * Sets the kind of scroll bars the TextArea will have, if any
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
		elseif($scrollType)
			$tmpScroll = 'scroll';
		else//if(!$scrollType)
			$tmpScroll = 'hidden';
		//Alert($tmpScroll);
		NolohInternal::SetProperty('style.overflow', $tmpScroll, $this);
	}
	/**
	 * Returns the string of text that was highlighted by the user
	 * @return string
	 */
    function GetSelectedText()
    {
        return Event::$FocusedComponent->Id == $this->Id ? Event::$SelectedText : '';
    }
    /**
     * @ignore
     */
	function Show()
	{
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/".(GetBrowser() == "ie"?"IE":"Mozilla")."TextAreaScripts.js");
		AddNolohScriptSrc('TextArea.js', true);
		NolohInternal::Show('TEXTAREA', parent::Show() . $this->GetEventString(null), $this);
		if(!UserAgent::IsIE())
			AddScript('_N(\''.$this->Id.'\').addEventListener(\'input\',_NTAInput,false)');
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShow($indent);
		if($str !== false)
			echo $indent, '<TEXTAREA ', $str, "></TEXTAREA>\n";
	}
}

?>