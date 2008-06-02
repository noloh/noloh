<?php
/**
 * RolloverImage class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Extended
 */
class RolloverImage extends Image implements Groupable
{
	private $GroupName;
	private $OutSrc;
	private $OverSrc;
	private $DownSrc;
	private $SelectedSrc;
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
	function GetSelectedSrc()								{return $this->SelectedSrc;}
	
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
	function SetSelectedSrc($selectSrc)
	{
		$this->SelectedSrc = $selectSrc;
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
	//function GetSelected()				{return $this->Selected != null;}
	function SetTogglesOff($bool)		{NolohInternal::SetProperty('Tgl', ($this->TogglesOff = $bool), $this);}
	function GetTogglesOff($bool)		{return ($this->TogglesOff==true);}
	function SetSelected($bool)
	{			
		parent::SetSelected($bool);
		$selected = $bool ? true : null;
		if($this->Selected != $selected)
		{
			//Trigger Select Event if $bool is true, i.e. Selected
			if($bool)
			{
				$src= $this->SelectedSrc != null?$this->SelectedSrc:$this->OutSrc;
				NolohInternal::SetProperty('src', $src, $this->Id);
				NolohInternal::SetProperty('Cur', 'Slct', $this);
			
				$sel = $this->GetSelect();
				if(!$sel->Blank())
					$sel->Exec();
			}
			else
			{
				NolohInternal::SetProperty('src', $this->OutSrc, $this->Id);
				NolohInternal::SetProperty('Cur', 'Out', $this);
			}
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