<?php
/**
 * @package UI
 * @subpackage Controls
 */

/**
 * An Eventee is a MarkupItem that is capable of recieving events.<br>
 * This is used only with connection with EventMarkupPanel.<br>
 * This class should never be instantiated by the developer. Use only the Eventees that EventMarkupPanel::GetEventees() returns.<br>
 * See {@see EventMarkupPanel} and {@see EventMarkupPanel::GetEventees()} for more information.<br>
 * 
 * The possible events are:
 * 	Click
 * 	DoubleClick
 * 	MouseDown
 * 	MouseOut
 * 	MouseOver
 * 	MouseUp
 * 	RightClick
 * 
 * <code>
 * // A function which takes an EventMarkupPanel object that we'll locally name $eventMarkupPanel
 * function SetEventeeClicks(EventMarkupPanel $eventMarkupPanel)
 * {
 * 	// Iterates through all Eventees of $eventMarkupPanel
 * 	foreach($eventMarkupPanel->GetEventees() as $eventee)
 * 		// Gives each Eventee a Click ServerEvent which calls AlertValue and passes in that Eventee's value as a parameter
 * 		$eventee->Click = new ServerEvent($this, "AlertValue", $eventee->Value);
 * }
 * function AlertValue($value)
 * {
 * 	Alert("That Eventee's value is $value");
 * }
 * </code>
 */
class Eventee extends MarkupItem 
{
	/**
	 * @ignore
	 */
	function Eventee($id, $keyword, $value, $panelId)
	{
		parent::MarkupItem($id, $keyword, $value, $panelId);
	}
	/**
	 * Gets the Event associated with clicking on the Eventee. This entails pressing the left mouse button down and then releasing it over the Eventee. 
	 * @return Event
	 */
	function GetClick()
	{
		return GetComponentById($this->PanelId)->GetEvent('Click', $this->Id);
	}
	/**
	 * Sets the Event associated with clicking on the Eventee. This entails pressing the left mouse button down and then releasing it over the Eventee.
	 * @param Event $newClick
	 */
	function SetClick($newClick)
	{
		GetComponentById($this->PanelId)->SetEvent($newClick, 'Click', $this->Id);
	}
	/**
	 * Gets the Event associated with double-clicking on the Eventee
	 * @return Event
	 */
	function GetDoubleClick()
	{
		return GetComponentById($this->PanelId)->GetEvent('DoubleClick', $this->Id);
	}
	/**
	 * Sets the Event associated with double-clicking on the Eventee
	 * @param Event $newDoubleClick
	 */
	function SetDoubleClick($newDoubleClick)
	{
		GetComponentById($this->PanelId)->SetEvent($newDoubleClick, 'DoubleClick', $this->Id);
	}
	/**
	 * Gets the Event associated with pressing the left mouse button down on the Eventee. Note that this differs from a click in that a click involves pressing the button down and up.
	 * @return Event
	 */
	function GetMouseDown()
	{
		return GetComponentById($this->PanelId)->GetEvent('MouseDown', $this->Id);
	}
	/**
	 * Sets the Event associated with pressing the left mouse button down on the Eventee. Note that this differs from a click in that a click involves pressing the button down and up.
	 * @param Event $newMouseDown
	 */
	function SetMouseDown($newMouseDown)
	{
		GetComponentById($this->PanelId)->SetEvent($newMouseDown, 'MouseDown', $this->Id);
	}
	/**
	 * Gets the Event associated with moving the mouse cursor out of the Eventee
	 * @return Event
	 */
	function GetMouseOut()
	{
		return GetComponentById($this->PanelId)->GetEvent('MouseOut', $this->Id);
	}
	/**
	 * Sets the Event associated with moving the mouse cursor out of the Eventee
	 * @param Event $newMouseOut
	 */
	function SetMouseOut($newMouseOut)
	{
		GetComponentById($this->PanelId)->SetEvent($newMouseOut, 'MouseOut', $this->Id);
	}	
	/**
	 * Gets the Event associated with moving the mouse cursor over the Eventee
	 * @return Event
	 */
	function GetMouseOver()
	{
		return GetComponentById($this->PanelId)->GetEvent('MouseOver', $this->Id);
	}
	/**
	 * Sets the Event associated with moving the mouse cursor over the Eventee
	 * @param Event $newMouseOver
	 */
	function SetMouseOver($newMouseOver)
	{
		GetComponentById($this->PanelId)->SetEvent($newMouseOver, 'MouseOver', $this->Id);
	}
	/**
	 * Gets the Event associated with releasing the left mouse button over the Eventee. Note that this differs from a click in that a click involves pressing the button down and up.
	 * @return Event
	 */
	function GetMouseUp()
	{
		return GetComponentById($this->PanelId)->GetEvent('MouseUp', $this->Id);
	}
	/**
	 * Sets the Event associated with releasing the left mouse button over the Eventee. Note that this differs from a click in that a click involves pressing the button down and up.
	 * @param Event $newMouseUp
	 */
	function SetMouseUp($newMouseUp)
	{
		GetComponentById($this->PanelId)->SetEvent($newMouseUp, 'MouseUp', $this->Id);
	}
	/**
	 * Gets the Event associated with right-clicking on the Eventee
	 * @return Event
	 */
	function GetRightClick()
	{
		return GetComponentById($this->PanelId)->GetEvent('RightClick', $this->Id);
	}
	/**
	 * Sets the Event associated with right-clicking on the Eventee
	 * @param Event $newRightClick
	 */
	function SetRightClick($newRightClick)
	{
		GetComponentById($this->PanelId)->SetEvent($newRightClick, 'RightClick', $this->Id);
	}
}

?>