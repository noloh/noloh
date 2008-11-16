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
	private $TogglesOff;
	
	function RolloverLabel($text, $outColor='#000000', $overColor='#FFFFFF', $selectedColor='#FFFFFF', $left = 0, $top = 0, $width = System::Auto, $height = 18)
	{
		parent::Label($text, $left, $top, $width, $height);
		$click = parent::GetClick();
		$click['System'] = new Event();
		$click['User'] = new Event();
		$select = parent::GetSelect();
		$select['System'] = new Event();
		$select['User'] = new Event();
		$deselect = parent::GetDeselect();
		$deselect['System'] = new Event();
		$deselect['User'] = new Event();
		
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
		$this->MouseOut['Out'] = new ClientEvent('_NRlLblTgl', $this->Id, 'Out');
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
		$this->MouseDown['Down'] = new ClientEvent('_NRlLblTgl', $this->Id, 'Dwn');
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
		$this->MouseOver['Over'] = new ClientEvent('_NRlLblTgl', $this->Id, 'Ovr');
	}
	function GetSelectedColor()	{return $this->SelectedColor;}
	function SetSelectedColor($color)
	{
		$this->SelectedColor = $color;
		if(is_array($color))
			QueueClientFunction($this, '_N(\''.$this->Id.'\').Slct=Array', array('\''.$color[0].'\'', '\''.$color[1].'\''));
		else
			NolohInternal::SetProperty('Slct', $color, $this);

		$click = parent::GetClick();
		$click['System'] = new ClientEvent("_NSetProperty('{$this->Id}','Selected', this.Tgl?this.Selected!=true:true);");
		$select = parent::GetSelect();
		$select['System'] = new ClientEvent("_NRlLblTgl('{$this->Id}','Slct');");
		$deselect = parent::GetDeselect();
		$deselect['System'] = new ClientEvent("_NRlLblTgl('{$this->Id}','Out');");
	}	
	function SetTogglesOff($bool)		{NolohInternal::SetProperty('Tgl', ($this->TogglesOff = $bool), $this);}
	function GetTogglesOff()			{return ($this->TogglesOff==true);}
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
	function SetSelected($bool)
	{
		if($this->GetSelected() != $bool)
		{
			parent::SetSelected($bool);
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