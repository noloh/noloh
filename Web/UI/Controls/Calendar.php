<?php

class Calendar extends Panel 
{
	public $MonthYearLabel;
	public $CalendarTable;
	private $ViewMonth;
	private $ViewYear;
	private $Date;
	private $Month;
	private $Year;
	
	function Calendar($left=0, $top=0, $width=215, $height=200, $UNIXEpochTime=null)
	{
		parent::Panel($left, $top, $width, $height);
		//$this->SelectFix = true;
		$this->Border = "1px solid #000000";
		$this->BackColor = "#FFFFCC";
		$DaysOfWeek = array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
		$this->MonthYearLabel = new Label("Out of Service", 0, 0, $width, 25);
		$this->MonthYearLabel->SetCSSClass("NCalHead");
		$LeftYear = new Button("<<", 0, 0, 25, 25);
		$LeftYear->Click = new ClientEvent("LastYear('" . $this->DistinctId . "')");
		$RightYear = new Button(">>", $width-25, 0, 25, 25);
		$RightYear->Click = new ClientEvent("NextYear('" . $this->DistinctId . "')");
		$LeftMonth = new Button("<", 25, 0, 25, 25);
		$LeftMonth->Click = new ClientEvent("LastMonth('" . $this->DistinctId . "')");
		$RightMonth = new Button(">", $width-50, 0, 25, 25);
		$RightMonth->Click = new ClientEvent("NextMonth('" . $this->DistinctId . "')");
		$this->Controls->AddRange($this->MonthYearLabel, $LeftYear, $RightYear, $LeftMonth, $RightMonth/*, $this->CalendarTable*/);
		for($i=6; $i>=0; --$i)
		{
			$this->Controls->Add($lbl = &new Label($DaysOfWeek[$i], $i*31, 33, 31));
			$lbl->SetCSSClass("NCalColHead");
		}
		for($i=1; $i<7; ++$i)
			for($j=0; $j<7; ++$j)
			{
				$this->Controls->Add($lbl = &new Label("", $j*31, 33+23*$i, 31));
				$lbl->SetCSSClass("NCalCell");
				$lbl->SetMouseUp(new ClientEvent('CalSelectDate(event,"'. $this->DistinctId . '")'));
			}
		$this->SetUNIXEpoch($UNIXEpochTime);
	}
	
	function GetViewMonth()
	{
		return $this->ViewMonth;
	}
	
	function SetViewMonth($newViewMonth)
	{
		$this->ViewMonth = $newViewMonth;
		$this->UpdateClient();
	}
	
	function GetViewYear()
	{
		return $this->ViewYear;
	}
	
	function SetViewYear($newViewYear)
	{
		$this->ViewYear = $newViewYear;
		$this->UpdateClient();
	}
	
	function GetDate()
	{
		return $this->Date;
	}
	
	function SetDate($newDate)
	{
		$this->Date = $newDate;
		$this->UpdateClient();
	}
	
	function GetMonth()
	{
		return $this->Month;
	}
	
	function SetMonth($newMonth)
	{
		$this->Month = $newMonth;
		$this->UpdateClient();
	}
	
	function GetYear()
	{
		return $this->Year;
	}
	
	function SetYear($newYear)
	{
		$this->Year = $newYear;
		$this->UpdateClient();
	}	
	
	function GetUNIXEpoch()
	{
		return mktime(0, 0, 0, $this->Month+1, $this->Date, $this->Year);
	}
	
	function SetUNIXEpoch($UNIXEpochTime)
	{
		if($UNIXEpochTime==null)
			$UNIXEpochTime = date('U');
	
		$dateM = date('n', $UNIXEpochTime)-1;
		$dateY = date('Y', $UNIXEpochTime);
		$this->ViewMonth = $dateM;
		$this->ViewYear = $dateY;
		$this->Date = date('d', $UNIXEpochTime);
		$this->Month = $dateM;
		$this->Year = $dateY;
		$this->UpdateClient();
	}
	
	function GetFullDate()
	{
		$UNIXEpoch = $this->GetUNIXEpoch();
		$date = getdate($UNIXEpoch);
		return $date["weekday"].', '.$date["month"].' '.$date["mday"].', '.$date["year"];
	}
	
	function UpdateClient()
	{
		//QueueClientFunction($this, "ShowCalendar", "'$this->DistinctId'", $this->ViewMonth, $this->ViewYear, $this->Date, $this->Month, $this->Year);
		QueueClientFunction($this, "ShowCalendar", array("'$this->DistinctId'", $this->ViewMonth, $this->ViewYear, $this->Date, $this->Month, $this->Year), true, Priority::High);
		/*if($this->HasShown())
			AddScript('ShowCalendar("' . $this->DistinctId . '", ' . $this->ViewMonth . ', ' . $this->ViewYear . ', ' .
				$this->Date . ', ' . $this->Month . ', ' . $this->Year . ');'/*, Priority::High);*/
	}
	
	function Show()
	{
		$parentShow = parent::Show();
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/".(GetBrowser() == "ie"?"IE":"Mozilla")."CalendarScript.js");
		$this->UpdateClient();
	}
}

?>