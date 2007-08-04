<?php

/**
 * @package UI
 * @subpackage Controls
 * ListControl class
 *
 * The base class for ListControls, this class is usually extended. 
 * <br>
 * The Show() <b>MUST</b> be overriden.
 *
 * Properties
 * - <b>SelectedIndex</b>, Integer, 
 *   <br>Gets or Sets the SelectedIndex of the ListControl
 * - <b>SelectedValue</b>, mixed, 
 *   <br>Gets or Sets the SelectedValue of the ListControl
 * - <b>SelectedText</b>, string, 
 *   <br>Gets or Sets the SelectedText of the ListControl
 * - <b>Items</b>, ArrayList, 
 *   <br>An ArrayList containing the Items of the ListControl
 * 
 * You can use the ListControl as follows
 * <code>
 *		class MyOwnList extends ListControl
 * 		{
 *			function MyOwnList()
 *			{
 *				parent::ListControl(0,0,100,100);
 *				$this->Items->Add(new Item(1, "SomeItem"));
 *			}	
 * </code>
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
	* for inherited components, be sure to call the parent constructor first
	* <br>The Show() <b>MUST</b> be overriden
 	* so that the component properties and events are defined.
	* @param integer|optional
	* @param integer|optional
	* @param integer|optional
	* @param integer|optional
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
	 * Gets the SelectedValue of the ListControl
	 * <br> Can also be called as a property
	 * <code>$tempVal = $this->SelectedValue</code>
	 */
	function GetSelectedValue()
	{
		$selectedIndex = $this->SelectedIndex;
		if($selectedIndex >= 0)
			return $this->Items->Item[$selectedIndex]->Value;
		return null;
	}
	/**
	 * Sets the SelectedValue of the ListControl.
	 * Note:This sets the SelectedIndex to the <b>FIRST</b> occurence of the Value in the Items ArrayList
	 * <br> Can also be called as a property
	 * <code>$this->SelectedValue = 42;</code>
	 * @param mixed|specifies the value to set.
	 * @return mixed|if found, the value passed in; otherwise null.
	 */
	function SetSelectedValue($value)
	{
		$itemCount = $this->Items->Count();
		for($i=0; $i<$itemCount; $i++)
			if($this->Items->Item[$i]->Value == $value)
			{
				$this->SetSelectedIndex($i);
				return $i;
			}
		return null;
	}
	/**
	 * Gets the SelectedText of the ListControl
	 * <br> Can also be called as a property
	 * <code>$tempText = $this->SelectedText</code>
	 */
	function GetSelectedText()
	{
		$selectedIndex = $this->SelectedIndex;
		if($selectedIndex >= 0)
			return $this->Items->Item[$selectedIndex]->Text;
		return null;
	}
	/**
	 * Sets the SelectedText of the ListControl.
	 * Note:This sets the SelectedIndex to the <b>FIRST</b> occurence of the Text in the Items ArrayList
	 * <br> Can also be called as a property
	 * <code>$this->SelectedText = "Asher";</code>
	 * @param string|specifies the text to set.
	 * @return mixed|if found, the text passed in; otherwise null.
	 */
	function SetSelectedText($text)
	{
		$itemCount = $this->Items->Count();
		for($i=0; $i<$itemCount; $i++)
			if($this->Items->Item[$i]->Text == $text)
			{
				$this->SetSelectedIndex($i);
				return $i;
			}
		return null;
	}
	
	public function SetSelectedIndex($index)
	{
		//AddScript("document.getElementById('$this->Id').options[$whatIndex].selected=true");
		//NolohInternal::SetProperty("options[$whatIndex].selected", true, $this);
		//$tmpIndex = $index == "first"?0:$index;	
		//QueueClientFunction($this, "document.getElementById('$this->Id').options[$index].selected=true;void", array(0), true, Priority::Low);
		QueueClientFunction($this, '_NListSel', array("'$this->Id'", $index), false);
		if(/*$this->GetSelectedIndex() !== $index && */$this->Change != null /*&& $index != "first"*/)
			$this->Change->Exec();
	}
	
	public function AddItem($item)
	{
		//Alert("Adding Items");
		//if(func_num_args()==1)
			$this->Items->Add($item, true, true);
		//QueueClientFunction($this, "document.getElementById('$this->Id').options.add", array("new Option('$item->Text','$item->Value')"), false);
		QueueClientFunction($this, '_NListAdd', array("'$this->Id'", "'$item->Text'", "'$item->Value'"), false);
		//AddScript("document.getElementById('$this->Id').options.add(new Option('$item->Text','$item->Value'))");
	}
	
	public function InsertItem($item, $index)
	{
		$this->Items->Insert($item, $index, true);
		//QueueClientFunction($this, "document.getElementById('$this->Id').options.add", array("new Option('$item->Text','$item->Value')", $index), false);
		QueueClientFunction($this, '_NListAdd', array("'$this->Id'", "'$item->Text'", "'$item->Value'", $index), false);
		//AddScript("document.getElementById('$this->Id').options.add(new Option('$item->Text','$item->Value'),$index)");
	}
	
	public function RemoveItemAt($index)
	{
		//We should decide whether this should be commented - Asher
		//if(func_num_args()==1)
			$this->Items->RemoveAt($index, true);
		//QueueClientFunction($this, "document.getElementById('$this->Id').options.remove", array($index), false);
		QueueClientFunction($this, '_NListRem', array("'$this->Id'", $index), false);
		//AddScript("document.getElementById('$this->Id').remove($index)");
	}
	
	public function ClearItems()
	{
		//if(func_num_args()==0)
			$this->Items->Clear(true);
		//AddScript("document.getElementById('$this->Id').options.length=0);");
		//Changed previos line to SetProperty
		//NolohInternal::SetProperty("options.length", 0, $this);
		//QueueClientFunction($this, "document.getElementById('$this->Id').options.length=0;void", array(0), false);
		QueueClientFunction($this, '_NListClr', array("'$this->Id'"), false);
	}
	
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === null)
			return ",'onchange','".$this->GetEventString("Change")."'";
		return parent::GetEventString($eventTypeAsString);
	}
	
	function SearchEngineShow()
	{
		foreach($this->Items as $item)
			print($item->Text . ' ' . $item->Value . ' ');
	}
}
	
?>