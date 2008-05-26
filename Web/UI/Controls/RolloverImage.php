<?php
/**
 * @package Web.UI.Controls
 */	
class RolloverImage extends Image implements Groupable
{
	private $GroupName;
	private $OutSrc;
	private $OverSrc;
	private $DownSrc;
	private $SelectSrc;
	private $Selected;
	private $TogglesOff;
	
	function RolloverImage($outSrc=null, $overSrc=null, $left=0, $top=0, $width=System::Auto, $height=System::Auto)
	{
		parent::Image($outSrc, $left, $top, $width, $height);
		$this->SetOverSrc($overSrc);
	}
	function GetOutSrc()								{return $this->Src;}
	function GetOverSrc()								{return $this->OverSrc;}
	function GetDownSrc()								{return $this->DownSrc;}
	function GetSelectSrc()								{return $this->SelectSrc;}
	
	function SetSrc($outSrc)
	{
		parent::SetSrc($outSrc);
		$this->OutSrc = $outSrc;
		NolohInternal::SetProperty('Out', $outSrc, $this);
		if($outSrc)
			$this->MouseOut['Out'] = new ClientEvent('_NTglRlOvrImg', $this->Id, 'Out');
	}
	function SetOverSrc($overSrc)
	{
		if($overSrc)
		{
			$this->OverSrc = $overSrc;
			NolohInternal::SetProperty('Ovr', $overSrc, $this);
			$this->MouseOver['Over'] = new ClientEvent('_NTglRlOvrImg', $this->Id, 'Ovr');
		}
	}
	function SetDownSrc($downSrc)
	{
		if($downSrc)
		{
			$this->DownSrc = $downSrc;
			NolohInternal::SetProperty('Dwn', $downSrc, $this);
		
			$this->MouseDown['Down'] = new ClientEvent('_NTglRlOvrImg', $this->Id, 'Dwn');
			$this->MouseUp['Down'] = new ClientEvent('_NTglRlOvrImg', $this->Id, 'Out');
		}
	}
	function SetSelectSrc($selectSrc)
	{
		$this->SelectSrc = $selectSrc;
		NolohInternal::SetProperty('Slct', $selectSrc, $this);
		if($selectSrc && $this->Click['Select'] == null)
			$this->Click['Select'] = new ClientEvent('_NTglRlOvrImg', $this->Id, 'Slct');
	}
	//Select Event Functions
	function GetSelect()				{return $this->GetEvent('Select');}
	function SetSelect($newSelect)		{$this->SetEvent($newSelect, 'Select');}
	//Groupable Functions
	function GetGroupName()				{return $this->GroupName;}
	function SetGroupName($groupName)	{$this->GroupName = $groupName;}
	function GetSelected()				{return $this->Selected != null;}
	function SetTogglesOff($bool)		{NolohInternal::SetProperty('Tgl', ($this->TogglesOff = $bool), $this);}
	function GetTogglesOff($bool)		{return ($this->TogglesOff==true);}
	function SetSelected($bool)
	{			
		parent::SetSelected($bool);
		$selected = $bool ? true : null;
		if($this->Selected != $selected)
		{
			/*if($this->SelectSrc)
			{
				$this->MouseOut['Out']->Enabled = !$bool;
				$this->MouseOver['Over']->Enabled = !$bool;
				if($this->MouseDown['Down'])
					$this->MouseDown['Down']->Enabled = !$bool;
				$this->Click['Select']->Enabled = !$bool;
			}*/
			//Trigger Select Event if $bool is true, i.e. Selected
			if($bool)
			{
				NolohInternal::SetProperty('src', $this->SelectSrc, $this->Id);
//				$this->Src = $this->SelectSrc;
				$sel = $this->GetSelect();
				if(!$sel->Blank())
					$sel->Exec();
			}
			else
				NolohInternal::SetProperty('src', $this->OutSrc, $this->Id);
//				$this->Src = $this->OutSrc;
			$this->Selected = $selected;
		}
	}
	function Show()
	{
		parent::Show();
		NolohInternal::SetProperty('Cur', 'Out', $this);
		AddNolohScriptSrc('RolloverImage.js');
	}
}
?>