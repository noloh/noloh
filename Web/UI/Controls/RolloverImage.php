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
		
	function RolloverImage($outSrc=null, $overSrc=null, $left=0, $top=0, $width=System::Auto, $height=System::Auto)
	{
		parent::Image($outSrc, $left, $top, $width, $height);
		$this->SetOutSrc($outSrc);
		$this->SetOverSrc($overSrc);
	}
	function GetOutSrc()								{return $this->OutSrc;}
	function GetOverSrc()								{return $this->OverSrc;}
	function GetDownSrc()								{return $this->DownSrc;}
	function GetSelectSrc()								{return $this->SelectSrc;}
	
	function SetOutSrc($outSrc)
	{
		$this->OutSrc = $outSrc;
		if(!empty($outSrc))
			$this->MouseOut['Out'] = new ClientEvent('ChangeImage(\'' . $this->Id . '\',\'' . $this->OutSrc .'\');');
	}
	function SetOverSrc($overSrc)
	{
		$this->OverSrc = $overSrc;
		if($overSrc)
			$this->MouseOver['Over'] = new ClientEvent('ChangeImage(\'' . $this->Id . '\',\'' . $this->OverSrc .'\');');
	}
	function SetDownSrc($downSrc)
	{
		$this->DownSrc = $downSrc;
		if($downSrc)
			$this->MouseDown['Down'] = new ClientEvent('ChangeImage(\'' . $this->Id . '\',\'' . $this->DownSrc .'\');');
	}
	function SetSelectSrc($selectSrc)
	{
		$this->SelectSrc = $selectSrc;
		if($selectSrc && $this->Click['Select'] == null)
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
			if($this->SelectSrc)
			{
				$this->MouseOut['Out']->Enabled = !$bool;
				$this->MouseOver['Over']->Enabled = !$bool;
				if($this->MouseDown['Down'])
					$this->MouseDown['Down']->Enabled = !$bool;
				$this->Click['Select']->Enabled = !$bool;
			}
			//Trigger Select Event if $bool is true, i.e. Selected
			if($bool)
			{
				if($this->GroupName != null)
					GetComponentById($this->GroupName)->DeSelect();
					//GetComponentById($this->GroupName)->SetSelectedElement($this);
				$this->Src = $this->SelectSrc;
				$sel = $this->GetSelect();
				if(!$sel->Blank())
					$sel->Exec();
			}
			else
				$this->Src = $this->OutSrc;
			$this->Selected = $selected;
		}
	}
}
?>