<?php
/**
 * TextBox class
 *
 * A TextBox is a Control for a conventional web text input field. It allows the user to type in a single line of text.
 * If you want to allow the user to type in more than one line of text, then you are looking for the TextArea Control
 * instead.
 * 
 * @package Controls/Core
 */
class TextBox extends Control
{
	private $Password;
	private $Hidden;
	private $MaxLength;
	private $Filter;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends TextBox
	 * @param integer $left The left coordinate of this element
	 * @param integer $top The top coordinate of this element
	 * @param integer $width The width of this element
	 * @param integer $height The height of this element
	 * @return TextBox
	 */
	function TextBox($left = 0, $top = 0, $width = 83, $height = 16)  
	{
		parent::Control($left, $top, $width, $height);
	}
	/**
	 * @ignore
	 */
	function EditComplete($labId)
	{
		$lab = GetComponentById($labId);
		$lab->SetVisible(true);
		if($lab->Text != $this->Text)
		{
			$lab->Text = $this->Text; 
			if($lab->Change != null)
				$lab->Change->Exec();
		}
		$this->Parent->Controls->Remove($this);
	}
	/**
	 * Returns the maximum number of characters that are allowed in the TextBox
	 * @return integer
	 */
	function GetMaxLength()
	{
		return $this->MaxLength;
	}
	/**
	 * Sets the maximum number of characters that are allowed in the TextBox
	 * @param integer $maxLength
	 */
	function SetMaxLength($maxLength)
	{
		$this->MaxLength = $maxLength;
		NolohInternal::SetProperty('maxLength', $maxLength, $this);
	}
	/**
	 * Returns whether or not the TextBox is a password field. If it is, the user's input will appear censored to him via asterisks.
	 * @return boolean
	 */
	function GetPassword()
	{
		return $this->Password != null;
	}
	/**
	 * Sets whether or not the TextBox is a password field. If it is, the user's input will appear censored to him via asterisks.
	 * @param boolean $bool
	 */
	function SetPassword($bool)
	{
		$this->Password = $bool ? true : null;
		NolohInternal::SetProperty('type', $bool ? 'password' : 'text', $this);
	}
	/**
	 * @ignore
	 */
	function SetText($text)
	{
		parent::SetText($text);
		NolohInternal::SetProperty('value', $text, $this);
	}
	/**
	 * Returns the regular expression  which filters out user input. E.g., '/^\d*$/' indicates numeric only
	 * @return string
	 */
	function GetFilter()
	{
		return $this->Filter;
	}
	/**
	 * Sets the regular expression  which filters out user input. E.g., '/^\d*$/' indicates numeric only
	 * @param string $filter
	 */
	function SetFilter($filter)
	{
		AddNolohScriptSrc('Filter.js', true);
		$this->Filter = $filter;
		$this->UpdateEvent('KeyPress');
	}
	/**
	 * Returns the string of text that was highlighted by the user
	 * @return string
	 */
    function GetSelectedText()
    {
        return Event::$FocusedComponent == $this->Id ? Event::$SelectedText : '';
    }
    /**
     * @ignore
     */
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === null)
			return ',\'onchange\',\''.$this->GetEventString('Change').'\',\'onfocus\',\''.$this->GetEventString('Focus').'\'';

		$preStr = '';
		if($eventTypeAsString == 'Click' || $eventTypeAsString == 'Change' || $eventTypeAsString == 'DoubleClick' || $eventTypeAsString == 'LoseFocus')
			$preStr = '_NSave("'.$this->Id.'","value");';
        elseif($eventTypeAsString == 'Focus')
            $preStr = '_NFocus="'.$this->Id.'";';
        elseif($eventTypeAsString == 'KeyPress' && $this->Filter)
        {
        	preg_match('/^(.)\^?(.*?)\$?\1([a-zA-Z]*)$/', $this->Filter, $matches);
        	$preStr = '_NFilter('.(UserAgent::IsIE()?'"':'event,"').$this->Id.'","'.str_replace('\\','\\\\\\\\',$matches[2]).'","'.$matches[3].'");';
        }
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		$initialProperties = parent::Show();
		
		$initialProperties .= ',\'type\',\'';
		if($this->Password)
			$initialProperties .= 'password\'';
		else
			$initialProperties .= 'text\'';
		
		//$tempStr = str_repeat("  ", $IndentLevel) . "<INPUT "  . $parentShow . "' ";
		//if(!is_null($this->Text))
		//if(!empty($this->MaxLength))
		//	$initialProperties .= ",'MaxLength','$this->MaxLength'";
		$initialProperties .= $this->GetEventString(null);
		NolohInternal::Show('INPUT', $initialProperties, $this);
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShow($indent);
		echo $indent, '<INPUT type="text" ', $str, "></INPUT>\n";
	}
}
?>