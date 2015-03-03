<?php
/**
 * CheckBox class
 *
 * A Control for a conventional web checkbox. Checkboxes are usually used to indicate
 * whether a condition is on/off, yes/no, or true/false. Checkbox is simlar to RadioButton,
 * except that RadioButton's are usually limited to one choice, while a CheckBox allows for
 * multiple choices.
 * 
 * The following is an example of instantiating and adding a CheckBox
 * <pre>
 * function Foo()
 * {
 *     $check = new CheckBox("CheckBox1", 0,0);
 *     //Adds a CheckBox to the Controls class of some Container object
 *     $this->Controls->Add($check);
 * }
 * </pre>
 * 
 * @package Controls/Core
 */
class CheckBox extends CheckControl implements MultiGroupable
{
    /**
     * Constructor.
	 * Be sure to call this from the constructor of any class that extends CheckBox
	 *	<pre> $check = new CheckBox("Check Me", 0, 0, 100, 24);</pre>
     * @param string $text The text of the CheckBox
     * @param integer $left The left coordinate of this element.
     * @param integer $top The top coordinate of this element.
     * @param mixed $width The Width of this element, possible values are
     * integer, percentage, System::Auto
     * @param mixed $height The Height of this element, possible values are
     * integer, percentage, System::Auto
     * @return CheckBox
     */
	function CheckBox($text='', $left = 0, $top = 0, $width = 50, $height = 20)
	{
		parent::CheckControl($text, $left, $top, $width, $height);
		//$this->Caption->Click = new ClientEvent('_NCBCptClk("'.$this->Id.'");');
	}
	/**
	 * @ignore
	 *
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === 'Click')
			return 
		//	return '_NSet(\'' . $this->Id . '\',\'Selected\', true);' . parent::GetEventString($eventTypeAsString);
		//if($eventTypeAsString === 'Click' || $eventTypeAsString === 'Change')
			//return '_NCBSave("'.$this->Id.'");' . parent::GetEventString($eventTypeAsString);
		return parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		/*
		// Weird AddNolohScriptSrc for Mixed browsers
		if(!isset($_SESSION['_NScriptSrcs']['CheckBox.js']))
		{
			$_SESSION['_NScriptSrc'] .= file_get_contents(System::NOLOHPath().'/JavaScript/Mixed/CheckBox'.(UserAgent::IsIE()?'IE.js':(UserAgent::GetBrowser()==='op'?'Op.js':'FFSa.js')));
			$_SESSION['_NScriptSrcs']['CheckBox.js'] = true;
		}
		*/
		parent::Show();
		$initialProperties = '\'type\',\'checkbox\',\'defaultChecked\','.($this->Checked?'true':'false').',\'onclick\',\'_NChkCtrlTgl("'.$this->Id.'",true);\'';
        //if($this->GroupName === null)
        //    $initialProperties .= ',\'name\',\''.$this->Id.'\'';
		NolohInternal::Show('INPUT', $initialProperties, $this, $this->Id, $this->Id.'I');

        /*
		$initialProperties = parent::Show();
		$initialProperties .= ",'type','checkbox'";
		$initialProperties .= parent::GetEventString(null);
		NolohInternal::Show('INPUT', $initialProperties, $this);    */
		//return $initialProperties;
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShow($indent);
		if($str !== false)
			echo $indent, '<INPUT type="checkbox" ', $str, '>', $this->Text, "</INPUT>\n";
	}
}
?>