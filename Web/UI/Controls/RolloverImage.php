<?php
	
class RolloverImage extends Image
{
	public $GroupName;
	private $OutSrc;
	private $OverSrc;
	private $DownSrc;
	private $SelectSrc;
	private $Selected;
	
	function RolloverImage($whatOutSrc="", $whatOverSrc="", $whatLeft=0, $whatTop=0, $whatWidth=System::Auto, $whatHeight=System::Auto)
	{
		parent::Image($whatOutSrc, $whatLeft, $whatTop, $whatWidth, $whatHeight);
		$this->SetOutSrc($whatOutSrc);
		$this->SetOverSrc($whatOverSrc);
	}
	function GetOutSrc()								{return $this->OutSrc;}
	function GetOverSrc()								{return $this->OverSrc;}
	function GetDownSrc()								{return $this->DownSrc;}
	function GetSelectSrc()								{return $this->SelectSrc;}
	function GetSelected()								{return $this->Selected;}
	
	function SetOutSrc($whatOutSrc)
	{
		$this->OutSrc = $whatOutSrc;
		if(!empty($whatOutSrc))
			$this->MouseOut = new ClientEvent("ChangeImage('{$this->DistinctId}','{$this->OutSrc}');");
	}
	function SetOverSrc($whatOverSrc)
	{
		$this->OverSrc = $whatOverSrc;
		if(!empty($whatOverSrc))
			$this->MouseOver = new ClientEvent("ChangeImage('{$this->DistinctId}','{$this->OverSrc}');");
	}
	function SetDownSrc($whatDownSrc)
	{
		$this->DownSrc = $whatDownSrc;
		if(!empty($whatDownSrc))
			$this->MouseDown = new ClientEvent("ChangeImage('{$this->DistinctId}','{$this->DownSrc}');");
	}
	function SetSelectSrc($whatSelectSrc)
	{
		$this->SelectSrc = $whatSelectSrc;
		if(!empty($whatSelectSrc))
			if(empty($this->Click))
				$this->Click = new ServerEvent($this, "SetSelected(true)");
			else 
				$this->Click[] = new ServerEvent($this, "SetSelected(true)");
	}
	function SetSelected($bool)
	{			
		if(isset($this->GroupName))
			if($whatBool)
				GetComponentById($this->GroupName)->SelectedRolloverImage->SetSelected(false);
			elseif(GetComponentById($this->GroupName)->GetSelectedIndex() == -1)
				GetComponentById($this->GroupName)->SetSelectedIndex(0);
				
		$this->Selected = $bool;
				
		if($this->MouseOut != null)
			$this->MouseOut->Enabled = !$bool;
		if($this->MouseOver != null)
			$this->MouseOver->Enabled = !$bool;
		if($this->MouseDown != null)
			$this->MouseDown->Enable = !$bool;
	}
/*	function Show()
	{
		if($this->Selected)
			$this->Src = $this->SelectSrc;
		else 
			$this->Src = $this->OutSrc;
			
		parent::Show();
	}*/
}
?>