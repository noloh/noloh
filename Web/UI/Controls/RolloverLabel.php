<?php
/**
 * @package Web.UI.Controls
 */
class RolloverLabel extends Label
{
	private $OutColor;
	private $OverColor;
	private $SelectedColor;
	private $Selected;
	private $GroupName;
	
	function RolloverLabel($text, $outColor="#000000", $overColor="#FFFFFF", $selectedColor="#FFFFFF", $whatLeft = 0, $whatTop = 0, $whatWidth = System::Auto, $whatHeight = 18)
	{
		parent::Label($text, $whatLeft, $whatTop, $whatWidth, $whatHeight);
		$this->SetOutColor($outColor);
		$this->SetOverColor($overColor);
		$this->SetSelectedColor($selectedColor);
	}
	function GetOutColor()	{return $this->OutColor;}
	function SetOutColor($color)
	{
		if(is_array($color))
		{
			$this->OutColor = $color;
			$this->MouseOut = new ClientEvent("ChangeAndSave('$this->Id', 'style.color', '{$color[0]}');");
			$this->MouseOut[] = new ClientEvent("ChangeAndSave('$this->Id', 'style.background', '{$color[1]}');");
			$this->CSSColor = $color[0];
			$this->CSSBackground = $color[1];
		}
		else
		{
			$this->CSSColor = $this->OutColor = $color;
			$this->MouseOut = new ClientEvent("ChangeAndSave('$this->Id', 'style.color', '$color');");
		}
	}
	function GetOverColor()	{return $this->OverColor;}
	function SetOverColor($color)
	{
		$this->OverColor = $color;
		if(is_array($color))
		{
			$this->MouseOver = new ClientEvent("ChangeAndSave('$this->Id', 'style.color', '{$color[0]}');");
			$this->MouseOver[] = new ClientEvent("ChangeAndSave('$this->Id', 'style.background', '{$color[1]}');");
		}
		else
			$this->MouseOver = new ClientEvent("ChangeAndSave('$this->Id', 'style.color', '$color');");
	}
	function GetSelectedColor()	{return $this->SelectedColor;}
	function SetSelectedColor($color){$this->SelectedColor = $color;}
	function SetGroupName($groupName)
	{
		$this->GroupName = $groupName;
		$sel = $this->GetEvent("Click");
		if($sel->Blank()) // && $fireEvents && $this->Click != null)
		{
			//Alert("Empty Click");
			$this->Click = new ServerEvent($this, "SetSelected", true);
		}
		else 
		{
			$this->Click[] = new ServerEvent($this, "SetSelected", true);
			//Alert("Has Click");
		}
	}
	function GetGroupName()	{return $this->GroupName;}
	function GetSelected(){return $this->Selected;}
	function SetSelected($bool)
	{
		if(isset($this->GroupName) && $bool)
		{
			$tempGroup = GetComponentById($this->GroupName);
			$tmpSelectedLabel = $tempGroup->SelectedRolloverLabel;
			if($tmpSelectedLabel === $this)
				return;
			elseif($tmpSelectedLabel != null)
				$tempGroup->SelectedRolloverLabel->SetSelected(false);
		}
		if($bool)
		{
			$sel = $this->GetEvent("Select");
			if(!$sel->Blank()) // && $fireEvents && $this->Click != null)
				$sel->Exec();
		}
		$this->Selected = $bool;	
		//if($this->MouseOut != null)
			$this->MouseOut->Enabled = !$bool;
		//if($this->MouseOver != null)
			$this->MouseOver->Enabled = !$bool;
		//if($this->MouseDown != null)
			$this->MouseDown->Enabled = !$bool;
		//if($this->Click != null)
			$this->Click->Enabled = !$bool;
		if(is_array($this->OutColor) || is_array($this->SelectedColor))	
		{
			$this->CSSColor = (!$bool)?$this->OutColor[0]:(($this->SelectedColor != null)?$this->SelectedColor[0]:$this->OverColor[0]);
			$this->CSSBackground = (!$bool)?$this->OutColor[1]:(($this->SelectedColor != null)?$this->SelectedColor[1]:$this->OverColor[1]);
		}
		else
			$this->CSSColor = (!$bool)?$this->OutColor:(($this->SelectedColor != null)?$this->SelectedColor:$this->OverColor);
		
	}
	function GetSelect() {return $this->GetEvent("Select"); /*$this->Select;*/}
	function SetSelect($newSelect)
	{
		$this->SetEvent($newSelect, "Select");
		//$this->Select = $newSelect;
	}
}
?>