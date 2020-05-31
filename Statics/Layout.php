<?php
/**
 * Layout class
 *
 * The Layout class contains constants that relates to the way by which Controls are positioned.
 * 
 * Most often, they are associated with a Control's Layout property, as follows:
 * 
 * <pre>
 * $button->Layout = Layout::Relative;
 * </pre>
 * 
 * But they are also sometimes associated with some Control's Align or VAlign properties, or a ControlPair's Orientation property.
 * Thus, it is a ubiquitous source of position-related constants.
 * 
 * @package Statics
 */
final class Layout
{
	/**
	 * A possible (and default) value for the Layout property of a Control, Absolute specifies that the Control will be positioned within its parent container by its Left and Top properties.
	 */
	const Absolute = 0;
	/**
	 * A possible value for the Layout property of a Control, Relative specifies that the Control will be positioned within its parent container by the block model, but offset by its Left and Top property values.
	 */
	const Relative = 1;
	/**
	 * A possible value for the Layout property of a Control, Web specifies that the Control will be positioned within its parent container by the block model, with the Left and Top properties ignored.
	 */
	const Web = 2;
	/**
	 * A possible value for the Layout property of a Control, Fixed is similar to Absolute except that the coordinates are taken relative to the WebPage instead of the immediate parent.
	 */
	const Fixed = 3;
	/**
	 * A possible value for the Layout property of a Control, Sticky is similar to Fixed, however it starts it's Fixed behavior after it's top property.
	 */
	const Sticky = 4;
	/**
	 * A possible value for the Layout property of a Control, None specifies no layout property allowing you to specify it completely via CSS.
	 */
	const None = -1;
	/**
	 * A possible value for the Align property of some Controls.
	 */
	const Left = 'left';
	/**
	 * A possible value for the Align property of some Controls.
	 */
	const Center = 'center';
	/**
	 * A possible value for the Align property of some Controls.
	 */
	const Right = 'right';
	/**
	 * A possible value for the VAlign property of some Controls.
	 */
	const Top = 'top';
	/**
	 * A possible value for the VAlign property of some Controls.
	 */
	const Baseline = 'baseline';
	/**
	 * A possible value for the VAlign property of some Controls.
	 */
	const Bottom = 'bottom';
	/**
	 * A constant for descriminating between the horizontal or vertical axes. E.g., for the Orientation of a ControlPair.
	 */
	const Horizontal = 0;
	/**
	 * A constant for descriminating between the horizontal or vertical axes. E.g., for the Orientation of a ControlPair.
	 */
	const Vertical = 1;
	
	private function Layout(){}
}
?>
