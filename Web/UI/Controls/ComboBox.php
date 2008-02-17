<?php
/**
 * @package Web.UI.Controls
 * ComboBox class file.
 */
/**
 * ComboBox class
 *
 * A Control for ComboBox. A ComboBox allows you to select items from a dropdown menu.a conventional web checkbox. Checkbox's are usually used to indicate
 * whether a condition is on/off, yes/no, or true/false. Checkbox is simlar to RadioButton,
 * except that RadioButton's are usually limited to one choice, while a CheckBox allows for
 * multiple choices.
 * 
 * The following is an example of instantiating and adding a CheckBox
 * <code>
 *
 *      function Foo()
 *      {
 *          $tmpCheck = new CheckBox("CheckBox1", 0,0);
 *          //Adds a button to the Controls class of some Container object
 * 		    $this->Controls->Add($tmpCheck);
 *      }
 *      function SomeFunc()
 *      {
 *          Alert("Click event was triggered");
 *      }
 *		
 * </code>
 * 
 * @property string $Type The type of the button
 * The Type of this Button, the Default is "Normal", can also be set to "Submit".
 */
class ComboBox extends ListControl 
{
	private $SelectedIndex;
	
	function ComboBox($left = 0, $top = 0, $width = 83, $height = 20)
	{
		parent::ListControl($left, $top, $width, $height);
		//$this->SetSelectedIndex(0);
	}
	function GetSelectedIndex()
	{
		return ($this->SelectedIndex === null)?-1:$this->SelectedIndex;
	}
	function SetSelectedIndex($index)
	{
		if($this->GetSelectedIndex() != $index)
		{
			$this->SelectedIndex = $index;
			parent::SetSelectedIndex($index);
		}
	}
	function GetSelectedItem()
	{
		return $this->SelectedIndex != -1 ? $this->Items->Elements[$this->SelectedIndex] : null;
	}
	function GetEventString($eventTypeAsString)
	{
		$preStr = '';
		if($eventTypeAsString == 'Change')
			$preStr = "_NSave(\"$this->Id\",\"selectedIndex\");";
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
	function AddItem($item)
	{
		parent::AddItem($item);
		if($this->Items->Count == 1)
			$this->SetSelectedIndex(0);
	}
	function Show()
	{
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/ListControl.js");
		AddNolohScriptSrc('ListControl.js');
		$initialProperties = parent::Show();
		$initialProperties .= $this->GetEventString(null);
		NolohInternal::Show('SELECT', $initialProperties, $this);
	}
}

?>