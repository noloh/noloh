<?php
/**
 * RolloverLabel class
 *
 * A RolloverLabel is a Label that allows color changes during Out, Over Down, and Selected states. 
 * 
 * Basic Example:
 * <pre>
 * $navButton = new RolloverLabel('Home', 'red', 'green', 'blue');
 * //optional Down state
 * $navButton->DownColor = 'yellow';
 * </pre>
 * @package Controls/Extended
 */
class RolloverLabel extends Label implements Groupable
{
	private $OutColor;
	private $OverColor;
	private $DownColor;
	private $SelectedColor;
	private $TogglesOff;
	/**
	 * Constructor 
	 * @param string $text The text displayed in the Label
	 * @param string|array $outColor The color displayed during the out state
	 * @param string|array $overColor The color displayed during the over state
	 * @param string|array $selectedColor The color displayed during the selected state
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 */
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
	/**
	 * Returns the color of the out state
	 * @return string|array
	 */
	function GetOutColor()	{return $this->OutColor;}
	/**
	 * Sets the color to be used during the out state
	 * @param string|array $color The color used during the out state. If set to an array, the first index is the background, the second index is the text color.
	 */
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
	/**
	 * Returns the color of the down state
	 * @return string|array
	 */
	function GetDownColor()	{return $this->DownColor;}
	/**
	 * Sets the color to be used during the Down state
	 * @param string|array $color The color used during the out state. If set to an array, the first index is the background, the second index is the text color.
	 */
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
	/**
	 * Returns the color of the over state
	 * @return string|array
	 */
	function GetOverColor()	{return $this->OverColor;}
	/**
	 * Sets the color to be used during the Over state
	 * @param string|array $color The color used during the out state. If set to an array, the first index is the background, the second index is the text color.
	 */
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
	/**
	 * Returns the color of the selected state
	 * @return string|array
	 */
	function GetSelectedColor()	{return $this->SelectedColor;}
	/**
	 * Sets the color to be used during the Selected state
	 * @param string|array $color The color used during the out state. If set to an array, the first index is the background, the second index is the text color.
	 */
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
	/**
	 * Sets whether the RollvoerLabel can Toggle itself being Selected, or whether something else must be deselected for it to deselect.
	 * @param boolean $bool
	 */
	function SetTogglesOff($bool)		{NolohInternal::SetProperty('Tgl', ($this->TogglesOff = $bool), $this);}
	/**
	 * Returns whether the RoloverLabel can Toggle itself being Selected, or whether something else must be deselected for it to deselect.
	 * @return boolean
	 */
	function GetTogglesOff()			{return ($this->TogglesOff==true);}
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
	 * @ignore
	 */
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