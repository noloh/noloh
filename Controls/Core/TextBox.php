<?php
/**
 * TextBox class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Core
 */
class TextBox extends Control
{
	private $Password;
	private $Hidden;
	private $MaxLength;
		
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
		$lab->ClientVisible = true;
		if($lab->Text != $this->Text)
		{
			$lab->Text = $this->Text; 
			if($lab->Change != null)
				$lab->Change->Exec();
		}
		$this->Parent->Controls->Remove($this);
	}
	
	function GetMaxLength()
	{
		return $this->MaxLength;
	}
	
	function SetMaxLength($newMaxLength)
	{
		$this->MaxLength = $newMaxLength;
		NolohInternal::SetProperty('maxLength', $newMaxLength, $this);
	}
	
	function GetPassword()
	{
		return $this->Password != null;
	}
	
	function SetPassword($bool)
	{
		$this->Password = $bool ? true : null;
	}
	
	function GetHidden()
	{
		return $this->Hidden != null;
	}
	
	function SetHidden($bool)
	{
		$this->Hidden = $bool ? true : null;
	}
	/**
	 * @ignore
	 */
	function SetText($newText)
	{
		parent::SetText($newText);
		NolohInternal::SetProperty('value', $newText, $this);
	}

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
		elseif($this->Hidden)
			$initialProperties .= 'hidden\'';
		else
			$initialProperties .= 'text\'';
		
		//$tempStr = str_repeat("  ", $IndentLevel) . "<INPUT "  . $parentShow . "' ";
		//if(!is_null($this->Text))
		//if(!empty($this->MaxLength))
		//	$initialProperties .= ",'MaxLength','$this->MaxLength'";
		$initialProperties .= $this->GetEventString(null);
		NolohInternal::Show('INPUT', $initialProperties, $this);
	}
}
?>