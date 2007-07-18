<?php
/**
 * DatePicker class file.
 */
 
/**
 * DatePicker class
 *
 * DatePicker is a Panel with a ComboBox and a Calendar Control in it.
 *
 * Properties
 * - <b>DatePickerCombo</b>, ComboBox, 
 *   <br>The ComboBox in the DatePicker
 * - <b>DatePickerCalendar</b>, Calendar,
 *   <br>The Calendar in the DatePicker
 * - <b>UnixEpoch</b>, Integer
 *   <br>Gets or sets the UnixEpoch of the DatePicker
 * - <b>FullDate</b>, ReadOnly, String
 * - <br>Gets the FullDate selected</b>, Integer
 
 * You can use the DatePicker as follows
 * <code>
 *
 *		function Foo()
 *		{
 *			$tempDatePicker = new DatePicker(0,0);
 *			$tempDatePicker->UnixEpoch = 1095379198; //Sets the UnixEpoch
 *		}
 *		
 * </code>
 */

class DatePicker extends Panel
{
	/**
 	* DatePickerCombo, DatePicker's ComboBox
 	* @var ComboBox
 	*/
	public $DatePickerCombo;
	/**
 	* DatePickerCalendar, DatePicker's Calendar
 	* @var Calendar
 	*/
	public $DatePickerCalendar;
	/**
 	* Format of the calendar displays as a string
 	* @var string
 	*/
	private $Format;
	//public $DateChange;
	/**
	* Constructor.
	* for inherited components, be sure to call the parent constructor first
 	* so that the component properties and events are defined.
 	* Example
 	*	<code> $tempVar = new DatePicker(15, 15, 219, 21);</code>
	* @param integer|optional
	* @param integer|optional
	* @param integer|optional
	* @param integer|optional
	*/
	function DatePicker($whatLeft = 0, $whatTop = 0, $whatWidth = 219, $whatHeight = 21)
	{
		parent::Panel($whatLeft, $whatTop, $whatWidth, $whatHeight);
		//$this->Load = new ClientEvent('ShowCalendar("' . $this->Id . '", ' . $this->ViewMonth . ', ' . $this->ViewYear . ', ' .
		//	$this->Date . ', ' . $this->Month . ', ' . $this->Year . ');');
		$this->DatePickerCombo = new ComboBox(0,0,$whatWidth,20);
		$this->DatePickerCalendar = new Calendar(0, 21, 217, 200);
		$this->SetFormat("l, F d, Y");
		//$this->DatePickerCalendar->ClientVisible = false;
		if(GetBrowser() == "ie")
			$this->DatePickerCombo->Click = new ClientEvent("TogglePull('{$this->Id}')");
		else 
			$this->DatePickerCombo->Click = new ClientEvent("TogglePull('{$this->Id}', '{$this->DatePickerCombo->Id}')");
		//$this->LoadImage->ClientVisible = "NoDisplay";
		$this->Controls->Add($this->DatePickerCombo);
		$this->Controls->Add($this->DatePickerCalendar);
		//$this->FullDate = &$this->DatePickerCombo->Text;
		//Needs to be something like this // $this->FullDate = $this->DatePickerCombo->GetSelectedText();
		//$this->Controls->AddRangeArray(true, array(&$this->DatePickerCombo, &$this->DatePickerCalendar));
		//$this->Date = &$this->DatePickerCalendar->Date;
		//$this->Date = &$_SESSION[$this->DatePickerCalendar->Id]->Date;
	}
	/**
	*<b>Note:</b>Can also be called as a property.
	*<code> $tempFullDate = $this->FullDate;</code>
	* @return string|FullDate
	*/
	function GetFullDate()
	{
		return $this->DatePickerCalendar->GetFullDate();
	}
	/**
	*<b>Note:</b>Can also be called as a property.
	*<code> $tempEpoch = $this->UNIXEpoch;</code>
	* @return integer|UnixEpoch
	*/
	function GetUNIXEpoch()
	{
		return $this->DatePickerCalendar->GetUNIXEpoch();
	}
	/**
	*<b>Note:</b>Can also be called as a property.
	*<code> $this->UNIXEpoch = 1095379198;</code>
	* @return integer|UnixEpoch
	*/
	function SetUNIXEpoch($UNIXEpochTime)
	{
		$this->DatePickerCalendar->SetUNIXEpoch($UNIXEpochTime);
		QueueClientFunction($this, "document.getElementById('{$this->DatePickerCalendar->Id}').onchange.call", array());
	}
	function GetFormat()									
	{
		return $this->Format;
	}
	function SetFormat($newFormat)
	{
		$this->Format = $newFormat;
		$this->DatePickerCalendar->Change = new ClientEvent('var calObj = document.getElementById("' . $this->DatePickerCalendar->Id . '"); var ds = GetDateString(calObj.id,"'.$this->Format.'"); document.getElementById("' . $this->DatePickerCombo->Id . '").options[0] = new Option(ds,ds); document.getElementById("' . $this->Id . '").style.height="21px";');
	}
	/**
	* @ignore
	*/
	function Show()
	{
		parent::Show();
		//$this->DatePickerCalendar->Show();
		if($this->DatePickerCombo->Items->Count()==0)
			AddScript('ShowDatePicker("'.$this->DatePickerCalendar->Id.'","'.$this->DatePickerCombo->Id.'","'.$this->Format.'")'/*, Priority::High*/);
	}
}

?>