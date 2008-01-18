<?php
/**
 * @package Web.UI.Controls
 */
class RolloverLabel extends Label implements Groupable
{
	private $OutColor;
	private $OverColor;
	private $DownColor;
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
			$this->MouseOut['Out'] = new ClientEvent("ChangeAndSave('$this->Id', 'style.color', '{$color[0]}');ChangeAndSave('$this->Id', 'style.background', '{$color[1]}');");
			$this->CSSColor = $color[0];
			$this->CSSBackground = $color[1];
		}
		else
		{
			$this->CSSColor = $this->OutColor = $color;
			$this->MouseOut['Out'] = new ClientEvent("ChangeAndSave('$this->Id', 'style.color', '$color');");
		}
	}
	function GetDownColor()	{return $this->DownColor;}
	function SetDownColor($color)
	{
		if(is_array($color))
		{
			$this->DownColor = $color;
			$this->MouseDown['Down'] = new ClientEvent("ChangeAndSave('$this->Id', 'style.color', '{$color[0]}');ChangeAndSave('$this->Id', 'style.background', '{$color[1]}');");
		}
		else
		{
			$this->CSSColor = $this->OutColor = $color;
			$this->MouseDown['Down'] = new ClientEvent("ChangeAndSave('$this->Id', 'style.color', '$color');");
		}
	}
	function GetOverColor()	{return $this->OverColor;}
	function SetOverColor($color)
	{
		$this->OverColor = $color;
		if(is_array($color))
			$this->MouseOver['Over'] = new ClientEvent("ChangeAndSave('$this->Id', 'style.color', '{$color[0]}'); ChangeAndSave('$this->Id', 'style.background', '{$color[1]}');");
		else
			$this->MouseOver['Over'] = new ClientEvent("ChangeAndSave('$this->Id', 'style.color', '$color');");
	}
	function GetSelectedColor()	{return $this->SelectedColor;}
	function SetSelectedColor($color)
	{
		$this->SelectedColor = $color;
		if($color && $this->Click['Select'] == null)
			$this->Click['Select'] = new ServerEvent($this, 'SetSelected', true);
	}	
	//Select Event Functions
	function GetSelect()				{return $this->GetEvent('Select');}
	function SetSelect($newSelect)		{$this->SetEvent($newSelect, 'Select');}
	//Groupable Functions
	function GetGroupName()				{return $this->GroupName;}
	function SetGroupName($groupName)	{$this->GroupName = $groupName;}
	function GetSelected()				{return $this->Selected != null;}
	function SetSelected($bool)
	{			
		$selected = $bool ? true : null;
		if($this->Selected != $selected)
		{
			$this->MouseOut['Out']->Enabled = !$bool;
			if($this->MouseDown['Over'])
				$this->MouseOver['Over']->Enabled = !$bool;
			if($this->MouseDown['Down'])
				$this->MouseDown['Down']->Enabled = !$bool;
			if($this->SelectSrc)
				$this->Click['Select']->Enabled = !$bool;
			//Trigger Select Event if $bool is true, i.e. Selected
			if($bool && $this->GroupName != null)
			{
				GetComponentById($this->GroupName)->Deselect();
				//GetComponentById($this->GroupName)->SetSelectedElement($this);
				$sel = $this->GetSelect();
				if(!$sel->Blank())
					$sel->Exec();
			}
			if(is_array($this->OutColor) || is_array($this->SelectedColor))	
				{
					$this->CSSColor = (!$bool)?$this->OutColor[0]:(($this->SelectedColor != null)?$this->SelectedColor[0]:$this->OverColor[0]);
					$this->CSSBackground = (!$bool)?$this->OutColor[1]:(($this->SelectedColor != null)?$this->SelectedColor[1]:$this->OverColor[1]);
				}
				else
					$this->CSSColor = (!$bool)?$this->OutColor:(($this->SelectedColor != null)?$this->SelectedColor:$this->OverColor);
			$this->Selected = $selected;
		}
	}
}
?>