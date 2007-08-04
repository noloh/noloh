<?php
/**
 * @package UI
 * @subpackage Controls
 * DatePicker class file.
 */
 
/**
 * DatePicker class
 *
 * DatePicker is a Panel with a Calendar Control in it that can be pulled up and down, and keeps track of the Date selected.
 *
 * You can use the DatePicker as follows
 * <code>
 *
 *	$datePicker = new DatePicker();
 *	$datePicker->Timestamp = 1095379198; //Sets the Timestamp
 *		
 * </code>
 * 
 * @property-read string $FullDate The full date of the selected day. It will be formated according to the Format property.
 * @property integer $Timestamp The currently selected day of the DatePicker in the number of seconds since the UNIX Epoch, i.e., January 1 1970 00:00:00 GMT
 * @property string $Format The format of the DatePicker display, the same kind as PHP's native date() function. NOLOH's default is 'l, F d, Y'.
 */

class DatePicker extends Panel
{
	/**
 	* DatePickerCombo, the ComboBox of the DatePicker for pulling down the Calendar
 	* @var ComboBox
 	*/
	public $DatePickerCombo;
	/**
 	* DatePickerCalendar, the Calendar of the DatePicker
 	* @var Calendar
 	*/
	public $DatePickerCalendar;
	/**
 	* Format of the calendar displays as a string
 	* @access private
 	* @var string
 	*/
	private $Format;
	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends DatePicker
 	* Example
 	*	<code> $tempVar = new DatePicker(15, 15, 219, 21);</code>
	* @param integer[optional] $left
	* @param integer[optional] $top
	* @param integer[optional] $width
	* @param integer[optional] $height
	*/
	function DatePicker($left = 0, $top = 0, $width = 219, $height = 21)
	{
		parent::Panel($left, $top, $width, $height);
		$ar = new ArrayList();
		$this->DatePickerCombo = new ComboBox(0,0,$width,20);
		$this->DatePickerCalendar = new Calendar(0, 21, 217, 200);
		$this->SetFormat('l, F d, Y');
		if(GetBrowser() == 'ie')
			$this->DatePickerCombo->Click = new ClientEvent("TogglePull('{$this->Id}')");
		else 
			$this->DatePickerCombo->Click = new ClientEvent("TogglePull('{$this->Id}', '{$this->DatePickerCombo->Id}')");
		$this->Controls->Add($this->DatePickerCombo);
		$this->Controls->Add($this->DatePickerCalendar);
	}
	/**
	 * Returns the full date of the selected day. It will be formated according to the Format property.
	 * @return string 
	 */
	function GetFullDate()
	{
		return $this->DatePickerCalendar->GetFullDate();
	}
	/**
	 * Gets the currently selected day of the DatePicker in the number of seconds since the UNIX Epoch, i.e., January 1 1970 00:00:00 GMT
	 * @return integer
	 */
	function GetTimestamp()
	{
		return $this->DatePickerCalendar->GetTimestamp();
	}
	/**
	 * Sets the current day of the DatePicker in the number of seconds since the UNIX Epoch, i.e., January 1 1970 00:00:00 GMT
	 * @param integer $TimestampTime
	 */
	function SetTimestamp($TimestampTime)
	{
		$this->DatePickerCalendar->SetTimestamp($TimestampTime);
		QueueClientFunction($this, "document.getElementById('{$this->DatePickerCalendar->Id}').onchange.call", array());
	}
	/**
	 * Returns the currently used format of the display of the DatePicker
	 * @return string This returns the same kind of format as PHP's native date() function. NOLOH's default is 'l, F d, Y'.
	 */
	function GetFormat()									
	{
		return $this->Format;
	}
	/**
	 * Sets the format of the display of the DatePicker.
	 * @param string $format This expects the same kind of format as PHP's native date() function
	 */
	function SetFormat($format)
	{
		$this->Format = $format;
		$this->DatePickerCalendar->Change = new ClientEvent('var calObj = document.getElementById("' . $this->DatePickerCalendar->Id . '"); var ds = GetDateString(calObj.id,"'.$format.'"); document.getElementById("' . $this->DatePickerCombo->Id . '").options[0] = new Option(ds,ds); document.getElementById("' . $this->Id . '").style.height="21px";');
	}
	/**
	* @ignore
	*/
	function Show()
	{
		parent::Show();
		if($this->DatePickerCombo->Items->Count()==0)
			AddScript('ShowDatePicker("'.$this->DatePickerCalendar->Id.'","'.$this->DatePickerCombo->Id.'","'.$this->Format.'")'/*, Priority::High*/);
	}
}

?>