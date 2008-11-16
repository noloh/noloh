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
	private $OutSrc;
	private $OverSrc;
	private $DownSrc;
	private $SelectedSrc;
	private $TogglesOff;
	
	function RolloverImage($outSrc=null, $overSrc=null, $left=0, $top=0, $width=System::Auto, $height=System::Auto)
	{
		parent::Image($outSrc, $left, $top, $width, $height);
		$click = parent::GetClick();
		$click['System'] = new Event();
		$click['User'] = new Event();
		$select = parent::GetSelect();
		$select['System'] = new Event();
		$select['User'] = new Event();
		$deselect = parent::GetDeselect();
		$deselect['System'] = new Event();
		$deselect['User'] = new Event();
		
		$this->SetOverSrc($overSrc);
	}
	function GetOutSrc()								{return $this->Src;}
	function GetOverSrc()								{return $this->OverSrc;}
	function GetDownSrc()								{return $this->DownSrc;}
	function GetSelectedSrc()							{return $this->SelectedSrc;}
	
	function SetSrc($outSrc)
	{
		parent::SetSrc($outSrc);
		$this->OutSrc = $outSrc;
		NolohInternal::SetProperty('Out', $outSrc, $this);
		if($outSrc)
			$this->MouseOut['Out'] = new ClientEvent('_NRlImgTgl', $this->Id, 'Out');
	}
	function SetOverSrc($overSrc)
	{
		if($overSrc)
		{
			$this->OverSrc = $overSrc;
			NolohInternal::SetProperty('Ovr', $overSrc, $this);
			$this->MouseOver['Over'] = new ClientEvent('_NRlImgTgl', $this->Id, 'Ovr');
		}
	}
	function SetDownSrc($downSrc)
	{
		if($downSrc)
		{
			$this->DownSrc = $downSrc;
			NolohInternal::SetProperty('Dwn', $downSrc, $this);
		
			$this->MouseDown['Down'] = new ClientEvent('_NRlImgTgl', $this->Id, 'Dwn');
			$this->MouseUp['Down'] = new ClientEvent('_NRlImgTgl', $this->Id, 'Out');
		}
	}
	function SetSelectedSrc($selectSrc)
	{
		$this->SelectedSrc = $selectSrc;
		NolohInternal::SetProperty('Slct', $selectSrc, $this);
		if($selectSrc)
		{
			$click = parent::GetClick();
			$click['System'] = new ClientEvent("_NSetProperty('{$this->Id}','Selected', this.Tgl?this.Selected!=true:true);");
			$select = parent::GetSelect();
			$select['System'] = new ClientEvent("_NRlImgTgl('{$this->Id}','Slct');");
			$deselect = parent::GetDeselect();
			$deselect['System'] = new ClientEvent("_NRlImgTgl('{$this->Id}','Out');");
		}
	}
	function GetClick()
	{
		$click = parent::GetClick();
		return $click['User'];
	}
	function SetClick($event)
	{
		$click = parent::GetClick();
		$click['User'] = $event;
	}
	function GetSelect()
	{
		$select = parent::GetSelect();
		return $select['User'];
	}
	function SetSelect($event)
	{
		$select = parent::GetSelect();
		$select['User'] = $event;
	}
	function GetDeselect()
	{
		$deselect = parent::GetDeselect();
		return $deselect['User'];
	}
	function SetDeselect($event)
	{
		$deselect = parent::GetDeselect();
		$deselect['User'] = $event;
	}	
	//function GetSelected()				{return $this->Selected != null;}
	function SetTogglesOff($bool)		{NolohInternal::SetProperty('Tgl', ($this->TogglesOff = $bool), $this);}
	function GetTogglesOff()			{return ($this->TogglesOff==true);}
	function SetSelected($bool)
	{			
		if($this->GetSelected() != $bool)
		{
			parent::SetSelected($bool);
			if(!$bool)
			{
				NolohInternal::SetProperty('src', $this->OutSrc, $this->Id);
				NolohInternal::SetProperty('Cur', 'Out', $this);
			}
		}
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		parent::Show();
//		NolohInternal::SetProperty('Cur', 'Out', $this);
		AddNolohScriptSrc('RolloverImage.js');
	}
}
?>