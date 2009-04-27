<?php
/**
 * Layout class
 *
 * The Layout class contains constants that relates to the way by which Controls are positioned.
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
	 * A possible value for the Layout property of a Control, Relative specifies that the Control will be positioned within its parent container by the block model, with the Left and Top properties ignored.
	 */
	const Web = 2;
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