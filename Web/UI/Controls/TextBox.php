<?php
/**
 * @package UI
 * @subpackage Controls
 */
class TextBox extends Control
{
	public $Password;
	public $Hidden;
	private $MaxLength;
		
	function TextBox($whatLeft = 0, $whatTop = 0, $whatWidth = 83, $whatHeight = 16)  
	{
		parent::Control($whatLeft, $whatTop, $whatWidth, $whatHeight);
		$this->Password = false;
		$this->Hidden = false;
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
		$this->Parent->Controls->RemoveItem($this);
	}
	
	function GetMaxLength()
	{
		return $this->MaxLength;
	}
	
	function SetMaxLength($newMaxLength)
	{
		$this->MaxLength = $newMaxLength;
		NolohInternal::SetProperty("maxLength", $newMaxLength, $this);
	}
	
	function SetText($newText)
	{
		parent::SetText($newText);
		NolohInternal::SetProperty("value", $newText, $this);
	}
	
	function GetEventString($whatEventTypeAsString)
	{
		if($whatEventTypeAsString === null)
			return ",'onchange','".$this->GetEventString("Change")."'";

		$preStr = "";
		if($whatEventTypeAsString == "Change")
			$preStr = "_NSave(\"$this->Id\",\"value\");";
		return $preStr . parent::GetEventString($whatEventTypeAsString);
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
		if(!empty($this->MaxLength))
			$initialProperties .= ",'MaxLength','$this->MaxLength'";
		$initialProperties .= $this->GetEventString(null);
		NolohInternal::Show("INPUT", $initialProperties, $this);
	}
}
?>