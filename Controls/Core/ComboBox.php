<?php
/**
 * ComboBox class
 *
 * A Control for a conventional web ComboBox. A ComboBox allows a user to select exactly one Item from a dropdown menu. The menu will not pull
 * down until a user explicitly clicks on it to view the options. That is one fundamental way in which it differs from a Group of RadioButtons,
 * another possible way of allowing a user to select exactly one string of text out of many, but will display all the options at once without
 * a menu.
 * 
 * @package Controls/Core
 */
class ComboBox extends ListControl 
{
	private $SelectedIndex;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends ComboBox
	 * @param integer $left The left coordinate of this element
	 * @param integer $top The top coordinate of this element
	 * @param integer $width The width of this element
	 * @param integer $height The height of this element
	 * @return ComboBox
	 */
	function ComboBox($left = 0, $top = 0, $width = 83, $height = 20)
	{
		parent::ListControl($left, $top, $width, $height);
		//$this->SetSelectedIndex(0);
	}
	/**
	 * Returns the index of the Item that is selected, or -1 if none are selected.
	 * @return integer
	 */
	function GetSelectedIndex()
	{
		return ($this->SelectedIndex === null)?-1:$this->SelectedIndex;
	}
	/**
	 * Sets an Item of a particular index as selected
	 * @param integer $index
	 */
	function SetSelectedIndex($index)
	{
		if($this->GetSelectedIndex() != $index)
		{
			$this->SelectedIndex = $index;
			parent::SetSelectedIndex($index);
			ClientScript::Queue($this, '_NLstCtrSaveSelInd', array($this), true, Priority::Low);
		}
	}
	/**
	 * Returns the Item that is selected, or null if none are selected
	 * @return Item
	 */
	function GetSelectedItem()
	{
		return $this->SelectedIndex != -1 ? $this->Items->Elements[$this->SelectedIndex] : null;
	}
	/**
	 * @ignore
	 */
	function AddItem($item)
	{
		parent::AddItem($item);
		if($this->Items->Count == 1)
			$this->SetSelectedIndex(0);
	}
	/**
	 * @ignore
	 */
	public function ClearItems()
	{
		parent::ClearItems();
		$this->SelectedIndex = null;
		ClientScript::Queue($this, '_NLstCtrSaveSelInd', array($this), true, Priority::Low);
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		$preStr = '';
		if($eventTypeAsString == 'Change')
			$preStr = '_NSave("'.$this->Id.'","selectedIndex");';
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		ClientScript::AddNOLOHSource('ListControl.js');
		NolohInternal::Show('SELECT', parent::Show() . $this->GetEventString(null), $this);
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = Control::NoScriptShowIndent($indent);
		if($str !== false)
			echo $indent, '<SELECT ', $str, ">\n", ListControl::NoScriptShow($indent), $indent, "</INPUT>\n";
	}
}

?>