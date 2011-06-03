<?php
/**
 * RolloverImage class
 *
 * A RolloverImage is an Image that allows for Out, Over Down, and Selected states. 
 * 
 * Basic Example:
 * <pre>
 * $navButton = new RolloverImage('navOut.gif', 'navOver.gif');
 * //optional Down state
 * $navButton->DownPath = 'navDown.gif;
 * </pre>
 * When using RolloverImages as part of a Group, you can set the Selected state.
 * <pre>
 * $group = new Group();
 * $home = new RolloverImage('homeOut.gif', 'homeOver.gif');
 * $home->SelectedPath = 'homeSelected.gif';
 * $about = new RolloverImage('aboutOut.gif', 'aboutOver.gif');
 * $about->SelectedPath = 'aboutSelected.gif';
 * 
 * $group->AddRange($home, $about);
 * </pre>
 * @package Controls/Extended
 */
class RolloverImage extends Image implements Groupable
{
	private $OutSrc;
	private $OverSrc;
	private $DownSrc;
	private $SelectedSrc;
	private $TogglesOff;
	/**
	 * Constructor
	 * 
	 * @param string $outPath The path to the image for the Out state, can be a url, or file path relative to the index.php file of your application.
	 * @param string $overPath The path to the image for the Over state, can be a url, or file path relative to the index.php file of your application.
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element, by default the proper measurement is calculated for you. Use # to denote a percentage of the image width, ex. 50# for 50% of the full Image width.
	 * @param integer $height The Height dimension of this element, by default the proper measurement is calculated for you. Use # to denote a percentage of the image height, ex. 50# for 50% of the full Image height.
	 */
	function RolloverImage($outPath=null, $overPath=null, $left=0, $top=0, $width=System::Auto, $height=System::Auto)
	{
		parent::Image($outPath, $left, $top, $width, $height);
		$click = parent::GetClick();
		$click['System'] = new Event();
		$click['User'] = new Event();
		$select = parent::GetSelect();
		$select['System'] = new Event();
		$select['User'] = new Event();
		$deselect = parent::GetDeselect();
		$deselect['System'] = new Event();
		$deselect['User'] = new Event();
		
		$this->SetOverPath($overPath);
	}
	/**
	 * Returns the path to the image that is shown during the out state
	 * @return string
	 */
	function GetPath()								{return $this->OutSrc;}
	/**
	 * Returns the path to the image that is shown during the over state
	 * @return string
	 */
	function GetOverPath()								{return $this->OverSrc;}
	/**
	 * Returns the path to the image that is shown during the down state
	 * @return string
	 */
	function GetDownPath()								{return $this->DownSrc;}
	/**
	 * Returns the path to the image that is shown during the selected state
	 * @return string
	 */
	function GetSelectedPath()							{return $this->SelectedSrc;}
	/**
	 * Returns the path to the image that is shown during the out state
	 * @deprecated use Path instead
	 * @return string
	 */
	function GetSrc()								{return $this->Src;}
	/**
	 * Returns the path to the image that is shown during the over state
	 * @deprecated use OverPath instead
	 * @return string
	 */
	function GetOverSrc()								{return $this->OverSrc;}
	/**
	 * Returns the path to the image that is shown during the down state
	 * @deprecated use DownPath instead
	 * @return string
	 */
	function GetDownSrc()								{return $this->DownSrc;}
	/**
	 * Returns the path to the image that is shown during the selected state
	 * @deprecated use SelectedPath instead
	 * @return string
	 */
	function GetSelectedSrc()							{return $this->SelectedSrc;}
	/**
	 * Sets the path to the image that is shown during the out state
	 * The path is relative to your main file 
	 * <b>!Important!</b> If Overriding, make sure to call parent::SetSrc($newSrc)
	 * @param string $outPath
	 * @return string 
	 */
	function SetPath($outPath)
	{
		parent::SetPath($outPath);
		$this->OutSrc = $outPath;
		NolohInternal::SetProperty('Out', $outPath, $this);
		if($outPath)
			$this->MouseOut['Out'] = new ClientEvent('_NRlImgTgl', $this->Id, 'Out');
	}
	/**
	 * Sets the path to the image that is shown during the over state
	 * The path is relative to your main file 
	 * @param string $overPath
	 * @return string 
	 */
	function SetOverPath($overPath)
	{
		if($overPath)
		{
			$this->OverSrc = $overPath;
			NolohInternal::SetProperty('Ovr', $overPath, $this);
			$this->MouseOver['Over'] = new ClientEvent('_NRlImgTgl', $this->Id, 'Ovr');
		}
	}
	/**
	 * Sets the path to the image that is shown during the down state
	 * The path is relative to your main file 
	 * @param string $downPath
	 * @return string 
	 */
	function SetDownPath($downPath)
	{
		if($downPath)
		{
			$this->DownSrc = $downPath;
			NolohInternal::SetProperty('Dwn', $downPath, $this);
		
			$this->MouseDown['Down'] = new ClientEvent('_NRlImgTgl', $this->Id, 'Dwn');
			$this->MouseUp['Down'] = new ClientEvent('_NRlImgTgl', $this->Id, 'Out');
		}
	}
	/**
	 * Sets the path to the image that is shown during the selected state
	 * The path is relative to your main file 
	 * @param string $selectedPath
	 * @return string 
	 */
	function SetSelectedPath($selectedPath)
	{
		$this->SelectedSrc = $selectedPath;
		NolohInternal::SetProperty('Slct', $selectedPath, $this);
		if($selectedPath)
		{
			$click = parent::GetClick();
			$click['System'] = new ClientEvent("_NSet('{$this->Id}','Selected', this.Tgl?this.Selected!=true:true);");
			$select = parent::GetSelect();
			$select['System'] = new ClientEvent("_NRlImgTgl('{$this->Id}','Slct');");
			$deselect = parent::GetDeselect();
			$deselect['System'] = new ClientEvent("_NRlImgTgl('{$this->Id}','Out');");
		}
	}
	 /**
	 * Sets the path to the image that is shown during the out state
	 * The path is relative to your main file 
	 * <b>!Important!</b> If Overriding, make sure to call parent::SetSrc($newSrc)
	 * @deprecated use Path instead
	 * @param string $outSrc
	 * @return string 
	 */
	function SetSrc($outSrc)	{$this->SetPath($outSrc);}
	/**
	 * Sets the path to the image that is shown during the over state
	 * The path is relative to your main file 
	 * @deprecated use OverPath instead
	 * @param string $overSrc
	 * @return string 
	 */
	function SetOverSrc($overSrc)	{$this->SetOverPath($overSrc);}
	/**
	 * Sets the path to the image that is shown during the down state
	 * The path is relative to your main file 
	 * @deprecated use DownPath instead
	 * @param string $downSrc
	 * @return string 
	 */
	function SetDownSrc($downSrc)	{$this->SetDownPath($downSrc);}
	/**
	 * Sets the path to the image that is shown during the selected state
	 * The path is relative to your main file 
	 * @deprecated use SelectedPath instead
	 * @param string $selectedSrc
	 * @return string 
	 */
	function SetSelectedSrc($selectedSrc)	{$this->SetSelectedPath($selectedSrc);}
	/**
	 * @ignore
	 */
	function GetClick()
	{
		$click = parent::GetClick();
		return $click['User'];
	}
	/**
	 * @ignore
	 */
	function SetClick($event)
	{
		$click = parent::GetClick();
		$click['User'] = $event;
	}
	/**
	 * @ignore
	 */
	function GetSelect()
	{
		$select = parent::GetSelect();
		return $select['User'];
	}
	/**
	 * @ignore
	 */
	function SetSelect($event)
	{
		$select = parent::GetSelect();
		$select['User'] = $event;
	}
	/**
	 * @ignore
	 */
	function GetDeselect()
	{
		$deselect = parent::GetDeselect();
		return $deselect['User'];
	}
	/**
	 * @ignore
	 */
	function SetDeselect($event)
	{
		$deselect = parent::GetDeselect();
		$deselect['User'] = $event;
	}	
	/**
	 * Sets whether the CollapsePanel can Toggle itself being Selected, or whether something else must be deselected for it to deselect.
	 * @param boolean $bool
	 */
	function SetTogglesOff($bool)		{NolohInternal::SetProperty('Tgl', ($this->TogglesOff = $bool), $this);}
	/**
	 * Returns whether the CollapsePanel can Toggle itself being Selected, or whether something else must be deselected for it to deselect.
	 * @return boolean
	 */
	function GetTogglesOff()			{return ($this->TogglesOff==true);}
	/**
	 * @ignore
	 */
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