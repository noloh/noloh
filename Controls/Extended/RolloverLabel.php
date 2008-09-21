<?php
/**
 * RolloverLabel class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Extended
 */
class RolloverLabel extends Label implements Groupable
{
	private $OutColor;
	private $OverColor;
	private $DownColor;
	private $SelectedColor;
	private $GroupName;
	private $TogglesOff;
	
	function RolloverLabel($text, $outColor='#000000', $overColor='#FFFFFF', $selectedColor='#FFFFFF', $left = 0, $top = 0, $width = System::Auto, $height = 18)
	{
		parent::Label($text, $left, $top, $width, $height);
		$this->SetOutColor($outColor);
		$this->SetOverColor($overColor);
		$this->SetSelectedColor($selectedColor);
	}
	function GetOutColor()	{return $this->OutColor;}
	function SetOutColor($color)
	{
		$this->OutColor = $color;
		if(is_array($color))
		{
			QueueClientFunction($this, '_N(\''.$this->Id.'\').Out=Array', array('\''.$color[0].'\'', '\''.$color[1].'\''));
//			NolohInternal::SetProperty('Out', 'Array(\'' . $color[0] . '\', \''. $color[1] .'\')', $this);
			$this->SetColor($color[0]);
			$this->SetBackColor($color[1]);
		}
		else
		{
			$this->SetColor($this->OutColor = $color);
			NolohInternal::SetProperty('Out', $color, $this);
		}
		$this->MouseOut['Out'] = new ClientEvent('_NTglRlOvrLbl', $this->Id, 'Out');
	}
	function GetDownColor()	{return $this->DownColor;}
	function SetDownColor($color)
	{
		$this->DownColor = $color;
		if(is_array($color))
			QueueClientFunction($this, '_N(\''.$this->Id.'\').Dwn=Array', array('\''.$color[0].'\'', '\''.$color[1].'\''));
			//NolohInternal::SetProperty('Dwn', 'Array(\'' . $color[0] . '\', \''. $color[1] .'\')', $this);
		else
			NolohInternal::SetProperty('Dwn', $color, $this);
		$this->MouseDown['Down'] = new ClientEvent('_NTglRlOvrLbl', $this->Id, 'Dwn');
	}
	function GetOverColor()	{return $this->OverColor;}
	function SetOverColor($color)
	{
		$this->OverColor = $color;
		if(is_array($color))
			QueueClientFunction($this, '_N(\''.$this->Id.'\').Ovr=Array', array('\''.$color[0].'\'', '\''.$color[1].'\''));
			//NolohInternal::SetProperty('Ovr', 'Array(\'' . $color[0] . '\', \''. $color[1] .'\')', $this);
		else
			NolohInternal::SetProperty('Ovr', $color, $this);
		$this->MouseOver['Over'] = new ClientEvent('_NTglRlOvrLbl', $this->Id, 'Ovr');
	}
	function GetSelectedColor()	{return $this->SelectedColor;}
	function SetSelectedColor($color)
	{
		$this->SelectedColor = $color;
		if(is_array($color))
			QueueClientFunction($this, '_N(\''.$this->Id.'\').Slct=Array', array('\''.$color[0].'\'', '\''.$color[1].'\''));
//			NolohInternal::SetProperty('Slct', 'Array(\'' . $color[0] . '\', \''. $color[1] .'\')', $this);
		else
			NolohInternal::SetProperty('Slct', $color, $this);
		$this->Click['Select'] = new ClientEvent('_NTglRlOvrLbl', $this->Id, 'Slct');
	}	
	function SetTogglesOff($bool)		{NolohInternal::SetProperty('Tgl', ($this->TogglesOff = $bool), $this);}
	function GetTogglesOff()			{return ($this->TogglesOff==true);}
	//Select Event Functions
	function GetSelect()				{return $this->GetEvent('Select');}
	function SetSelect($newSelect)		{$this->SetEvent($newSelect, 'Select');}
	//Groupable Functions
	/**
	 * @ignore
	 */
	function GetGroupName()				{return $this->GroupName;}
	/**
	 * @ignore
	 */
	function SetGroupName($groupName)	{$this->GroupName = $groupName;}
//	function GetSelected()				{return $this->Selected != null;}
	function SetSelected($bool)
	{
		if($this->GetSelected() != $bool)
		{
			parent::SetSelected($bool);
			if($bool)
			{
				$sel = $this->GetSelect();
				if(!$sel->Blank())
					$sel->Exec();
			}
			if(is_array($this->OutColor) || is_array($this->SelectedColor))	
			{
				$this->Color = (!$bool)?$this->OutColor[0]:(($this->SelectedColor != null)?$this->SelectedColor[0]:$this->OverColor[0]);
				$this->BackColor = (!$bool)?$this->OutColor[1]:(($this->SelectedColor != null)?$this->SelectedColor[1]:$this->OverColor[1]);
			}
			else
				$this->Color = (!$bool)?$this->OutColor:(($this->SelectedColor != null)?$this->SelectedColor:$this->OverColor);
		}
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		parent::Show();
		NolohInternal::SetProperty('Cur', 'Out', $this);
		AddNolohScriptSrc('RolloverLabel.js');
	}
}
?>