<?php
/**
 * ListControl class
 *
 * A ListControl is a Control that has Items in it that may become selected. For example, {@see ComboBox}, {@see ListBox}, and {@see CheckListBox}
 * all extend ListControl, and it is ListControl's purpose to provide functionality that is common to both ComboBox, ListBox, and CheckListBox
 * as well as for proper organization and inheritance.
 * It is not recommended that you extend ListControl directly, instead, you should extend ComboBox, ListBox, or CheckListBox.
 *
 * @package Controls/Core
 */
abstract class ListControl extends Control
{
	/**
	* Items, An ArrayList containing the Items of the ListControl
	* @var ArrayList
	*/
	public $Items;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends ListControl.
	* @param integer $left
	* @param integer $top
	* @param integer $width
	* @param integer $height
	*/
	function ListControl($left=0, $top=0, $width=0, $height=0)
	{
		Control::Control($left, $top, $width, $height);
		$this->Items = new ImplicitArrayList($this, 'AddItem', 'RemoveItemAt', 'ClearItems');
		$this->Items->InsertFunctionName = 'InsertItem';
	}
	/**
	* @ignore
	*/
	abstract public function GetSelectedIndex();
	/**
	 * Selects an item based on the index
	 * @param integer|string $index
	 */
	public function SetSelectedIndex($index)
	{
		//AddScript("_N('$this->Id').options[$index].selected=true");
		//NolohInternal::SetProperty("options[$index].selected", true, $this);
		//$tmpIndex = $index == "first"?0:$index;
		//QueueClientFunction($this, "_N('$this->Id').options[$index].selected=true;void", array(0), true, Priority::Low);
		if(!isset($this->Items[$index]))
			BloodyMurder('Trying to set index ' . $index . ' to selected of the ' . get_class($this) . ' of id ' . $this->Id . ', but no such index exists.');
		QueueClientFunction($this, '_NLstCtrSel', array('\''.$this->Id.'\'', $index), false);
		if(/*$this->GetSelectedIndex() !== $index && */!$this->Change->Blank() /*&& $index != "first"*/)
			$this->Change->Exec();
	}
	/**
	 * Gets the Value of a selected Item
	 * <br> Can also be called as a property
	 * <pre>$tempVal = $this->SelectedValue</pre>
	 * @return string
	 */
	function GetSelectedValue()
	{
		$selectedIndex = $this->SelectedIndex;
		if($selectedIndex >= 0)
			return $this->Items->Elements[$selectedIndex]->Value;
		return null;
	}
	/**
	 * Selects an item whose Value matches the value passed in
	 * Note:This sets the SelectedIndex to the <b>FIRST</b> occurence of the Value in the Items ArrayList
	 * <br> Can also be called as a property
	 * <pre>$this->SelectedValue = 42;</pre>
	 * @param string $value
	 * @return mixed If found, the value passed in; otherwise null.
	 */
	function SetSelectedValue($value)
	{
		$itemCount = $this->Items->Count();
		for($i=0; $i<$itemCount; ++$i)
			if($this->Items->Elements[$i]->Value == $value)
			{
				$this->SetSelectedIndex($i);
				return $i;
			}
		return null;
	}
	/**
	 * Returns the SelectedValue. This is a convenient alias because different types of Controls may have different interpretations of "Value."
	 * @return string
	 */
	function GetValue()			{return $this->GetSelectedValue();}
	/**
	 * Sets the SelectedValue. This is a convenient alias because different types of Controls may have different interpretations of "Value."
	 * @param string $value
	 */
	function SetValue($value)	{return $this->SetSelectedValue($value);}
	/**
	 * Gets the Text of the selected Item
	 * <br> Can also be called as a property
	 * <pre>$tempText = $this->SelectedText</pre>
	 * @return string
	 */
	function GetSelectedText()
	{
		$selectedIndex = $this->SelectedIndex;
		if($selectedIndex >= 0)
			return $this->Items->Elements[$selectedIndex]->Text;
		return null;
	}
	/**
	 * Selects an item whose Text matches the text passed in
	 * Note:This sets the SelectedIndex to the <b>FIRST</b> occurence of the Text in the Items ArrayList
	 * <br> Can also be called as a property
	 * <pre>$this->SelectedText = "Asher";</pre>
	 * @param string $text specifies the text to set.
	 * @return mixed If found, the text passed in; otherwise null.
	 */
	function SetSelectedText($text)
	{
		$itemCount = $this->Items->Count();
		for($i=0; $i<$itemCount; ++$i)
			if($this->Items->Elements[$i]->Text == $text)
			{
				$this->SetSelectedIndex($i);
				return $i;
			}
		return null;
	}
	/**
	 * Adds an Item to the Items ArrayList.
	 * <br> This is equivalent to:
	 * <pre>$this->Items->Add($item)</pre>
	 * @param Item $item
	 */
	public function AddItem($item)
	{
		//Alert("Adding Items");
		//if(func_num_args()==1)
		if(is_string($item))
			$item = new Item($item, $item);
		$this->Items->Add($item, true);
		//QueueClientFunction($this, "_N('$this->Id').options.add", array("new Option('$item->Text','$item->Value')"), false);
		QueueClientFunction($this, '_NLstCtrAdd', array('\''.$this->Id.'\'', '\''.addslashes($item->Text).'\'', '\''.$item->Value.'\''), false);
		//AddScript("_N('$this->Id').options.add(new Option('$item->Text','$item->Value'))");
	}
	/**
	 * Inserts an Item into a particular index of the Items ArrayList.
	 * <br> This is equivalent to:
	 * <pre>$this->Items->Insert($item, $index)</pre>
	 * @param Item $item
	 */
	public function InsertItem($item, $index)
	{
		$this->Items->Insert($item, $index, true);
		//QueueClientFunction($this, "_N('$this->Id').options.add", array("new Option('$item->Text','$item->Value')", $index), false);
		QueueClientFunction($this, '_NLstCtrAdd', array('\''.$this->Id.'\'', '\''.$item->Text.'\'', '\''.$item->Value.'\'', is_numeric($index)?$index:('\''.$index.'\'')), false);
		//AddScript("_N('$this->Id').options.add(new Option('$item->Text','$item->Value'),$index)");
	}
	/**
	 * Removes an Item from a particular index of the Items ArrayList.
	 * <br> This is equivalent to:
	 * <pre>$this->Items->RemoveAt($index)</pre>
	 * @param integer $index
	 */
	public function RemoveItemAt($index)
	{
		//We should decide whether this should be commented - Asher
		//if(func_num_args()==1)
			$this->Items->RemoveAt($index, true);
		//QueueClientFunction($this, "_N('$this->Id').options.remove", array($index), false);
		QueueClientFunction($this, '_NLstCtrRem', array('\''.$this->Id.'\'', is_numeric($index)?$index:('\''.$index.'\'')), false);
		//AddScript("_N('$this->Id').remove($index)");
	}
	/**
	 * Clears the Items ArrayList.
	 * <br> This is equivalent to:
	 * <pre>$this->Items->Clear()</pre>
	 * @param Item $item
	 */
	public function ClearItems()
	{
		//if(func_num_args()==0)
			$this->Items->Clear(true);
		//AddScript("_N('$this->Id').options.length=0);");
		//Changed previos line to SetProperty
		//NolohInternal::SetProperty("options.length", 0, $this);
		//QueueClientFunction($this, "_N('$this->Id').options.length=0;void", array(0), false);
		QueueClientFunction($this, '_NLstCtrClr', array('\''.$this->Id.'\''), false);
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === null)
			return ',\'onchange\',\''.$this->GetEventString('Change').'\'';
		return parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function Set_NItems($items)
	{
		$this->Items->Clear(true);
		if($items)
		{
			$optionsArray = explode('~d3~', $items);
			$optionsCount = count($optionsArray);
			for($i=0; $i<$optionsCount; ++$i)
			{
				$option = explode('~d2~', $optionsArray[$i]);
				$this->Items->Add(new Item($option[0], $option[1]));
			}
		}
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		foreach($this->Items as $item)
			echo $item->Text, ' ', $item->Value, ' ';
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$indent .= '  ';
		foreach($this->Items as $item)
			echo $indent, '<OPTION value="', $item->Value, '">', $item->Text, "</OPTION>\n";
	}
}

?>