<?php
/**
 * @package Web.UI.Controls
 * Checkbox class file.
 */
/**
 * CheckBox class
 *
 * A Control for a conventional web checkbox. Checkbox's are usually used to indicate
 * whether a condition is on/off, yes/no, or true/false. Checkbox is simlar to RadioButton,
 * except that RadioButton's are usually limited to one choice, while a CheckBox allows for
 * multiple choices.
 * 
 * The following is an example of instantiating and adding a CheckBox
 * <code>
 * function Foo()
 * {
 *     $tmpCheck = new CheckBox("CheckBox1", 0,0);
 *     //Adds a CheckBox to the Controls class of some Container object
 *     $this->Controls->Add($tmpCheck);
 * }
 * </code>
 * 
 */
class CheckBox extends GroupedInputControl
{
    /**
     *Constructor.
	 * for inherited components, be sure to call the parent constructor first
	 * so that the component properties and events are defined.
	 * Example
	 *	<code> $tmpCheck = new CheckBox("Check Me", 0, 0, 100, 24);</code>
     * @param string[optional] $text The text of the CheckBox
     * @param integer[optional] $left The left coordinate of this element.
     * @param integer[optional] $top The top coordinate of this element.
     * @param mixed[optional] $width The Width of this element, possible values are
     * integer, percentage, System::Auto
     * @param mixed[optional] $height The Height of this element, possible values are
     * integer, percentage, System::Auto
     * @return CheckBox
     */
	function CheckBox($text='', $left = 0, $top = 0, $width = 50, $height = 20)
	{
		parent::GroupedInputControl($text, $left, $top, $width, $height);
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		$preStr = '';
		if($eventTypeAsString == 'Click')
			$preStr = "_NSave(\"$this->Id\",\"checked\");";
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		$initialProperties = parent::Show();
		$initialProperties .= ",'type','checkbox'";
		$initialProperties .= parent::GetEventString(null);
		NolohInternal::Show('INPUT', $initialProperties, $this);
		return $initialProperties;
	}
}	
?>