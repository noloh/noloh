<?php
/**
 * DatePicker class
 *
 * DatePicker is a Panel with a {@see Calendar} that can be pulled up and down via a drop-down, and keeps track of the date selected.
 *
 * For example:
 * <pre>
 * // Instantiates a new DatePicker object
 * $datePicker = new DatePicker();
 * // Adds it to the Controls ArrayList
 * $this->Controls->Add($datePicker);
 * </pre>
 * 
 * @property-read string $FullDate The full date of the selected day. It will be formatted according to the Format property.
 * @property integer $Timestamp The currently selected day of the DatePicker in the number of seconds since the UNIX Epoch, i.e., January 1 1970 00:00:00 GMT
 * @property string $Format The format of the DatePicker display, using the same formatting codes as PHP's native date() {@link PHP_Manual#date} function. 
 * NOLOH's default is 'l, F d, Y' which means that a typical date will look like 'Tuesday, August 14, 2007'
 * 
 * @package Controls/Extended
 */
class DatePicker extends Panel
{
	/**
 	* PullDown, the ComboBox of the DatePicker for pulling down the Calendar
 	* @var ComboBox
 	*/
	public $PullDown;
	/**
 	* Calendar, the Calendar of the DatePicker
 	* @var Calendar
 	*/
	public $Calendar;
	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends DatePicker
 	*	<pre> $tempVar = new DatePicker(15, 15, 219, 21);</pre>
	* @param integer $left The left coordinate of this Control
	* @param integer $top The top coordinate of this Control
	* @param integer $width The width of this Control
	* @param integer $height The height of this Control
	*/
	function DatePicker($left = 0, $top = 0, $width = 219, $height = 21)
	{
		parent::Panel($left, $top, $width, $height);
		$this->PullDown = new ComboBox(0, 0, $width, 20);
		$this->Calendar = new Calendar(0, 21, 217, 200);
		$this->Calendar->Buoyant = true;
		$this->Calendar->Visible = System::Vacuous;
		$this->SetFormat('l, F d, Y');
        switch(GetBrowser())
        {
            case 'ie':
                $this->PullDown->Click = new ClientEvent('TogglePull(\''.$this->Calendar->Id.'\');');
                $this->Calendar->Click = new ClientEvent('window.event.cancelBubble=true;');
                break;
            case 'sa':
                $this->PullDown->MouseDown = new ClientEvent('TogglePull(\''.$this->Calendar->Id.'\',\''.$this->PullDown->Id.'\',event);return false;');
                $this->Calendar->MouseDown = new ClientEvent('event.stopPropagation();');
                break;
            default:
                $this->PullDown->Click = new ClientEvent('TogglePull(\''.$this->Calendar->Id.'\',\''.$this->PullDown->Id.'\',event);');
                $this->Calendar->Click = new ClientEvent('event.stopPropagation();');
        }
		$this->Controls->Add($this->PullDown);
		$this->Controls->Add($this->Calendar);
	}
	/**
	 * Returns the the selected day as formatted according to the Format property.
	 * @return string 
	 */
	function GetFullDate()
	{
		return $this->Calendar->GetFullDate();
	}
	/**
	 * Gets the currently selected day of the DatePicker in the number of seconds since the UNIX Epoch, i.e., January 1 1970 00:00:00 GMT
	 * @return integer
	 */
	function GetTimestamp()
	{
		return $this->Calendar->GetTimestamp();
	}
	/**
	 * Sets the current day of the DatePicker in the number of seconds since the UNIX Epoch, i.e., January 1 1970 00:00:00 GMT. A value of null corresponds to today.
	 * @param integer $TimestampTime
	 * <pre>
	 * // Sets the $datePicker to Monday, January 12, 1970
	 * $datePicker->Timestamp = 1000000;
	 * </pre>
	 */
	function SetTimestamp($timestamp)
	{
		$this->Calendar->SetTimestamp($timestamp);
		QueueClientFunction($this, '_N(\''.$this->Calendar->Id.'\').onchange.call', array());
	}
	/**
	 * Returns the currently used format of the display of the DatePicker
	 * @return string The format of the DatePicker display, using the same formatting codes as PHP's native date() {@link PHP_Manual#date} function. 
 	 * NOLOH's default is 'l, F d, Y' which means that a typical date will look like 'Tuesday, August 14, 2007'
	 */
	function GetFormat()									
	{
		return $this->Calendar->GetFormat();
	}
	/**
	 * Sets the format of the display of the DatePicker.
	 * @param string $format This expects the same kind of format as PHP's native date() {@link PHP_Manual#date} function
	 */
	function SetFormat($format)
	{
		$this->Calendar->SetFormat($format);
		$this->Calendar->Change = new ClientEvent('PickerSelectDate("'.$this->Calendar->Id.'","'.$this->PullDown->Id.'","'.$format.'");');
	}
	/**
	* @ignore
	*/
	function Show()
	{
		parent::Show();
		if($this->PullDown->Items->Count()==0)
			AddScript('ShowDatePicker("'.$this->Calendar->Id.'","'.$this->PullDown->Id.'","'.$this->Calendar->GetFormat().'")');
	}
}

?>