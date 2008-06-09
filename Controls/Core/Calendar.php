<?php
/**
* Calendar class
* 
* A Calendar is a Panel which shows the days of the year sorted by month in a table where
* the columns correspond to days of the week, as in a conventional calendar. Furthermore,
* the user can scroll between months and years, as well as select a date.
* 
* <code>
* // Instantiates a new Calendar object
* $calendar = new Calendar();
* // Adds it to the Controls ArrayList
* $this->Controls->Add($calendar);
* </code>
* 
* One may also set a Change Event on the calendar. This Event will be triggered when any
* date is selected.
* 
* <code>
* // Sets the Calendar object's Change Event to call the AlertDate function with itself as a parameter
* $calendar->Change = new ServerEvent($this, 'AlertDate', $calendar);
* // A function which will alert a Calendar's FullDate
* function AlertDate($calendar)
* {
* 	Alert($calendar->FullDate);
* }
* // Thus, when a new date is selected on the Calendar, that date will be Alerted
* </code>
* 
* @package Controls/Core
*/
class Calendar extends Panel 
{
	/**
	 * The Label which displays the Date, at the top of the Calendar
	 * @var Label
	 */
	public $DateDisplay;
	private $ViewMonth;
	private $ViewYear;
	private $Date;
	private $Month;
	private $Year;
	private $Format;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Calendar
	 *	<code> $cal = new Calendar(0, 0, 80, 24, 1000000);</code>
	 *
	 * @param integer $left The left coordinate of this Control
	 * @param integer $top The top coordinate of this Control
	 * @param integer $width The width of this Control
	 * @param integer $height The height of this Control
	 * @param integer $timestamp The selected date, given in the number of seconds since the UNIX Epoch, i.e., January 1 1970 00:00:00 GMT. A value of null corresponds to today.
	 * @return Calendar
	 */
	function Calendar($left=0, $top=0, $width=215, $height=200, $timestamp=null)
	{
		parent::Panel($left, $top, $width, $height);
		$this->Border = '1px solid #000000';
		$this->BackColor = '#FFFFCC';
		$daysOfWeek = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
		$this->DateDisplay = new Label('Out of Service', 0, 0, $width, 25);
		$this->DateDisplay->SetCSSClass('NCalHead');
		$leftYear = new Button('<<', 0, 0, 25, 25);
		$leftYear->Click = new ClientEvent('LastYear(\''.$this->Id.'\');');
		$rightYear = new Button('>>', $width-25, 0, 25, 25);
		$rightYear->Click = new ClientEvent('NextYear(\''.$this->Id.'\');');
		$leftMonth = new Button('<', 25, 0, 25, 25);
		$leftMonth->Click = new ClientEvent('LastMonth(\''.$this->Id.'\');');
		$rightMonth = new Button('>', $width-50, 0, 25, 25);
		$rightMonth->Click = new ClientEvent('NextMonth(\''.$this->Id.'\');');
		$this->Controls->AddRange($this->DateDisplay, $leftYear, $rightYear, $leftMonth, $rightMonth);
		for($i=6; $i>=0; --$i)
		{
			$this->Controls->Add($lbl = &new Label($daysOfWeek[$i], $i*31, 33, 31));
			$lbl->SetCSSClass('NCalColHead');
		}
		for($i=1; $i<7; ++$i)
			for($j=0; $j<7; ++$j)
			{
				$this->Controls->Add($lbl = &new Label('', $j*31, 33+23*$i, 31));
				$lbl->SetCSSClass('NCalCell');
				$lbl->SetMouseUp(new ClientEvent('CalSelectDate(event,\''.$this->Id.'\')'));
			}
		$this->SetTimestamp($timestamp);
	}
	/**
	 * Returns the month that is currently being viewed, which is not necessarily the same as the one that is selected. Returned as an integer from 0 to 11.
	 * @return integer
	 */
	function GetViewMonth()
	{
		return $this->ViewMonth;
	}
	/**
	 * Sets the month that is currently being viewed, which is not necessarily the same as the one that is selected. Must be an integer from 0 to 11.
	 * @param integer $viewMonth
	 */
	function SetViewMonth($viewMonth)
	{
		$this->ViewMonth = $viewMonth;
		$this->UpdateClient();
	}
	/**
	 * Returns the year that is currently being viewed, which is not necessarily the same as the one that is selected.
	 * @return integer
	 */
	function GetViewYear()
	{
		return $this->ViewYear;
	}
	/**
	 * Sets the year that is currently being viewed, which is not necessarily the same as the one that is selected.
	 * @param integer $viewYear
	 */
	function SetViewYear($viewYear)
	{
		$this->ViewYear = $viewYear;
		$this->UpdateClient();
	}
	/**
	 * Returns the selected date
	 * @return integer
	 */
	function GetDate()
	{
		return $this->Date;
	}
	/**
	 * Sets the selected date
	 * @param integer $date
	 */
	function SetDate($date)
	{
		$this->Date = $date;
		$this->UpdateClient();
	}
	/**
	 * Returns the selected month, from 0 to 11
	 * @return integer
	 */
	function GetMonth()
	{
		return $this->Month;
	}
	/**
	 * Sets the selected month, from 0 to 11
	 * @param integer $month
	 */
	function SetMonth($month)
	{
		$this->Month = $this->ViewMonth = $month;
		$this->UpdateClient();
	}
	/**
	 * Returns the selected year
	 * @return integer
	 */
	function GetYear()
	{
		return $this->Year;
	}
	/**
	 * Sets the selected year
	 * @param integer $year
	 */
	function SetYear($year)
	{
		$this->Year = $this->ViewYear = $year;
		$this->UpdateClient();
	}
	/**
	 * Returns the format in which dates will be displayed, using the same formatting codes as PHP's native date() {@link PHP_Manual#date} function.
	 * @return string
	 */
	function GetFormat()
	{
		return $this->Format == null ? 'l, F d, Y' : $this->Format;
	}
	/**
	 * Sets the format in which dates will be displayed, using the same formatting codes as PHP's native date() {@link PHP_Manual#date} function.
	 * @param string $format
	 */
	function SetFormat($format)
	{
		$this->Format = $format == 'l, F d, Y' ? null : $format;
	}
	/**
	 * Gets the Calendar's currently selected day, in the number of seconds since the UNIX Epoch, i.e., January 1 1970 00:00:00 GMT
	 * @return integer
	 */
	function GetTimestamp()
	{
		return mktime(0, 0, 0, $this->Month+1, $this->Date, $this->Year);
	}
	/**
	 * Sets the Calendar's currently selected day, in the number of seconds since the UNIX Epoch, i.e., January 1 1970 00:00:00 GMT. A value of null corresponds to today.
	 * @param integer $timestamp
	 */
	function SetTimestamp($timestamp=null)
	{
		if($timestamp==null)
			$timestamp = date('U');

		$this->Date = date('d', $timestamp);
		$this->Month = $this->ViewMonth = date('n', $timestamp)-1;
		$this->Year = $this->ViewYear = date('Y', $timestamp);
		$this->UpdateClient();
	}
	/**
	 * Returns the the selected day as formatted according to the Format property.
	 * @return string 
	 */
	function GetFullDate()
	{
		return date($this->GetFormat(), $this->GetTimestamp());
	}
	/**
	 * @ignore
	 */
    function SetWidth($width)
    {
        parent::SetWidth($width);
        if($this->DateDisplay)
        {
            $this->DateDisplay->SetWidth($width);
            $this->Controls->Elements[2]->SetLeft($width - 25);
            $this->Controls->Elements[4]->SetLeft($width - 50);
        }
    }
	/**
	 * @ignore
	 */
	function UpdateClient()
	{
		QueueClientFunction($this, 'ShowCalendar', array('\''.$this->Id.'\'', $this->ViewMonth, $this->ViewYear, $this->Date, $this->Month, $this->Year), true, Priority::High);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		parent::Show();
		AddNolohScriptSrc('Calendar.js', true);
		$this->UpdateClient();
	}
}

?>