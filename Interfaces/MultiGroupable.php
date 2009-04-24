<?php
/**
 * MultiGroupable interface
 * 
 * The MultiGroupable interface specifies that instances of the class implementing the interface can be
 * Added to a Group, and more generally, have the Selected property. Moreover, unlike the Groupable interface,
 * more than one MultiGroupable element can be Selected at any given time within the same Group. 
 * 
 * The canonical example of a MultiGroupable object is a CheckBox as more than one CheckBox can be Selected
 * at any given time from the same Group.
 * 
 * Note that although this is an interface that has functions (which normally means you have to
 * define them), for a MultiGroupable class that is a subclass of Control, it is not necessary to define them.
 * The functions serve more as a reminder of the capabilities of Groupable objects rather than a 
 * template, e.g., for overloading them. On the other hand, it is possible to define MultiGroupable classes 
 * that are not subclasses of Control, though this should be considered an advanced technique.
 * 
 * @package Interfaces
 */
interface MultiGroupable
{
	/**
	 * Returns whether or not the Groupable element is currently Selected.
	 * @return boolean
	 */
	function GetSelected();
	/**
	 * Sets whether or not the Groupable element is currently Selected.
	 * @param boolean $bool
	 */
	function SetSelected($bool);
	/**
	 * Returns the Group to which this Element belongs.
	 * @return string
	 */
	function GetGroupName();
	/**
	 * Returns the Group to which this Element belongs. This function will automatically be called when this object is Added to a Group.
	 * @param string $groupName
	 */
	function SetGroupName($groupName);
}
?>