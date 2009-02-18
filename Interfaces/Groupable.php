<?php
/**
 * Groupable interface
 * 
 * The Groupable interface specifies that instances of the class implementing the interface can be
 * Added to a Group, and more generally, have the Selected property. Moreover, <b>at most one</b>
 * Groupable object can be Selected at a time within a single Group. If another element of that
 * Group will be Selected, the first object will automatically be Deselected. For an interface that 
 * specifies that more than one element can be Selected at a given time, please see the MultiGroupable interface.
 * 
 * The canonical example of a Groupable object is a RadioButton as only one RadioButton
 * may be Selected at any given time from the same Group.
 * 
 * Note that although this is an interface that has functions (which normally means you have to
 * define them), for a Groupable class that is a subclass of Control, it is not necessary to define them.
 * The functions serve more as a reminder of the capabilities of Groupable objects rather than a 
 * template, e.g., for overloading them. On the other hand, it is possible to define Groupable classes 
 * that are not subclasses of Control, though this should be considered an advanced technique.
 * 
 * @package Interfaces
 */
interface Groupable
{
	/**
	 * Returns whether or not the Groupable element is currently Selected.
	 */
	function GetSelected();
	/**
	 * Sets whether or not the Groupable element is currently Selected.
	 * @param bool $bool
	 */
	function SetSelected($bool);
	/**
	 * Returns the Group to which this Element belongs.
	 */
	function GetGroupName();
	/**
	 * Returns the Group to which this Element belongs. This function will automatically be called when this object is Added to a Group.
	 * @param string $groupName
	 */
	function SetGroupName($groupName);
}
?>