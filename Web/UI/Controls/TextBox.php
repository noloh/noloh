<?php
/**
 * @package UI
 * @subpackage Controls
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
	
	function EditComplete($labId)
	{
		$lab = GetComponentById($labId);
		$lab->ClientVisible = true;
		/*Old Way
		if($lab->Text != $this->Text && isset($lab->Change))
		{
			$lab->Text = $this->Text; //Was line 22
			$lab->Change->Exec();
		}*/
		//New Way , Asher
		if($lab->Text != $this->Text)
		{
			$lab->Text = $this->Text; //Was line 22
			if($lab->Change != null)
				$lab->Change->Exec();
		}
		//$Lab->Text = $this->Text;
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
	
	function SetText($newText)
	{
		parent::SetText($newText);
		NolohInternal::SetProperty('value', $newText, $this);
	}
	
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === null)
			return ",'onchange','".$this->GetEventString('Change')."'";

		$preStr = '';
		if($eventTypeAsString == 'Change')
			$preStr = "_NSave(\"$this->Id\",\"value\");";
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
		
	function Show()
	{
		$initialProperties = parent::Show();
			
		$initialProperties .= ",'type','";
		if($this->Password == true)
			$initialProperties .= "password'";
		elseif($this->Hidden == true)
			$initialProperties .= "hidden'";
		else
			$initialProperties .= "text'";
		
		//$tempStr = str_repeat("  ", $IndentLevel) . "<INPUT "  . $parentShow . "' ";
		//if(!is_null($this->Text))
		//if(!empty($this->MaxLength))
		//	$initialProperties .= ",'MaxLength','$this->MaxLength'";
		$initialProperties .= $this->GetEventString(null);
		NolohInternal::Show('INPUT', $initialProperties, $this);
	}
}
?>