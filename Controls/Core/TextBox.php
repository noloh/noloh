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
	 * Returns the Text. This is a convenient alias because different types of Controls may have different interpretations of "Value."
	 * @return string
	 */
	function GetValue()			{return $this->GetText();}
	/**
	 * Sets the Text. This is a convenient alias because different types of Controls may have different interpretations of "Value."
	 * @param string $value
	 */
	function SetValue($value)	{return $this->SetText($value);}
	/**
	 * @ignore
	 */
	function SetText($text)
	{
		$oldText = $this->GetText();
		parent::SetText($text);
		NolohInternal::SetProperty('value', $text, $this);
		if($oldText != $text)
		{
			$change = $this->GetChange();
			if(!$change->Blank())
				$change->Exec();
		}
	}
	/**
	 * Returns the regular expression which filters out user input. E.g., '/^\d*$/' indicates numeric only. Since regular expressions come in several slightly different flavors, it is worth noting that this expects a "JavaScript" type of regular expression.
	 * @return string
	 */
	function GetFilter()
	{
		return $this->Filter;
	}
	/**
	 * Sets the regular expression which filters out user input. E.g., '/^\d*$/' indicates numeric only. Since regular expressions come in several slightly different flavors, it is worth noting that this expects a "JavaScript" type of regular expression.
	 * @param string $filter
	 */
	function SetFilter($filter)
	{
		ClientScript::AddNOLOHSource('Filter.js', true);
		ClientScript::AddNOLOHSource('KeyEvents.js', true);
		$this->Filter = $filter;
		$this->UpdateEvent('KeyPress');
	}
     /**
     * @ignore
     */
    function GetChange()
    {
        $change = $this->GetEvent('Change');
        if(UserAgent::GetDevice()===UserAgent::Mobile && UserAgent::GetBrowser()===UserAgent::Opera && ($version=UserAgent::GetVersion())>=9 && $version<11)
        {
            ClientScript::AddNOLOHSource('Mixed/KeyEventsOpPPC.js');
            if(!isset($change['_N']))
                $change['_N'] = new ClientEvent('_NKeyEvntsMoTimeout', $this);
            Alert('inside');
        }
        return $change;
    }
    /**
     * @ignore
     */
    function SetChange($change)
    {
        if(UserAgent::GetDevice()===UserAgent::Mobile && UserAgent::GetBrowser()===UserAgent::Opera && ($version=UserAgent::GetVersion())>=9 && $version<11)
        {
            ClientScript::AddNOLOHSource('Mixed/KeyEventsOpPPC.js');
            $event = new Event();
            $event['User'] = $change;
            if(!isset($event['_N']))
                $event['_N'] = new ClientEvent('_NKeyEvntsMoTimeout', $this);
        }
        else
            $event = $change;
        return $this->SetEvent($event, 'Change');
        // return parent::SetChange($event);
    }
	/**
	 * Returns the TypePause Event, which gets launched when a user has the TextBox focused, types something, and pauses typing for half a second
	 * @return Event
	 */
	function GetTypePause()
	{
		$typePause = $this->GetEvent('TypePause');
		if(UserAgent::GetDevice()===UserAgent::Mobile && UserAgent::GetBrowser()===UserAgent::Opera && ($version=UserAgent::GetVersion())>=9 && $version<11)
		{
			ClientScript::AddNOLOHSource('Mixed/KeyEventsOpPPC.js');
			if(!isset($typePause['_N']))
				$typePause['_N'] = new ClientEvent('_NKeyEvntsMoTimeout', $this, true);
		}
		else
			ClientScript::AddNOLOHSource('KeyEvents.js', true);
		return $typePause;
	}
	/**
	 * Sets the TypePause Event, which gets launched when a user has the TextBox focused, types something, and pauses typing for half a second
	 * @param Event $typePause
	 */
	function SetTypePause($typePause)
	{
		if(UserAgent::GetDevice()===UserAgent::Mobile && UserAgent::GetBrowser()===UserAgent::Opera && ($version=UserAgent::GetVersion())>=9 && $version<11)
		{
			ClientScript::AddNOLOHSource('Mixed/KeyEventsOpPPC.js');
			$event = new Event();
			$event['User'] = $typePause;
			if(!isset($event['_N']))
				$event['_N'] = new ClientEvent('_NKeyEvntsMoTimeout', $this, true);
			$this->SetEvent($event, 'TypePause');
		}
		else
		{
			ClientScript::AddNOLOHSource('KeyEvents.js', true);
			$this->SetEvent($typePause, 'TypePause');
		}
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
	 * Gives the TextBox the active Focus. Optionally, its Text can also be highlighted.
	 * @param boolean $highlight
	 */
	function Focus($highlight = true)
	{
		if(UserAgent::GetDevice()===UserAgent::Mobile && UserAgent::GetBrowser()===UserAgent::Opera && ($version=UserAgent::GetVersion())>=9 && $version<11)
		{
			ClientScript::Queue($this, '_NKeyEvntsMoTimeout', $this);
		}
		else
			parent::Focus();
		if($highlight)
			ClientScript::Queue($this, '_N("'.$this->Id.'").select', array(), false, Priority::Low);
//			QueueClientFunction($this, '_N("'.$this->Id.'").select', array(), false, Priority::Low);
	}
    /**
     * @ignore
     */
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === null)
			return ',\'onchange\',\''.$this->GetEventString('Change').'\',\'onfocus\',\''.$this->GetEventString('Focus').'\'';

		$preStr = '';
		if($eventTypeAsString === 'Change' || $eventTypeAsString === 'Click' || $eventTypeAsString === 'DoubleClick' || $eventTypeAsString === 'LoseFocus')
			$preStr = '_NSave("'.$this->Id.'","value");';
        elseif($eventTypeAsString === 'Focus')
            $preStr = '_N.EventVars.FocusedComponent="'.$this->Id.'";';
        elseif($eventTypeAsString === 'KeyPress' && $this->Filter)
        {
        	preg_match('/^(.)\^?(.*?)\$?\1([a-zA-Z]*)$/', $this->Filter, $matches);
        	$preStr = '_NFilter("'.$this->Id.'","'.str_replace('\\','\\\\\\\\',$matches[2]).'","'.$matches[3].'");';
        }
    	elseif($eventTypeAsString === 'ReturnKey' && UserAgent::GetBrowser()==='op')
        	$preStr = 'this.onchange.call();';
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		if(UserAgent::GetDevice()===UserAgent::Mobile && UserAgent::GetBrowser()===UserAgent::Opera && ($version=UserAgent::GetVersion())>=9 && $version<11)
		{
			ClientScript::AddNOLOHSource('Mixed/KeyEventsOpPPC.js');
		}
		elseif(UserAgent::GetBrowser() === 'op')
		{
			ClientScript::AddNOLOHSource('KeyEvents.js', true);
			$this->UpdateEvent('ReturnKey');
		}
		$initialProperties = parent::Show() . '\'type\',\'' . ($this->Password?'password\'':'text\'') . $this->GetEventString(null);
		//$tempStr = str_repeat("  ", $IndentLevel) . "<INPUT "  . $parentShow . "' ";
		//if(!is_null($this->Text))
		//if(!empty($this->MaxLength))
		//	$initialProperties .= ",'MaxLength','$this->MaxLength'";
		NolohInternal::Show('INPUT', $initialProperties, $this);
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShow($indent);
		if($str !== false)
			echo $indent, '<INPUT type="text" ', $str, "></INPUT>\n";
	}
}
?>