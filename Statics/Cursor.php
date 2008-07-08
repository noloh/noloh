<?php
/**
 * Cursor class
 *
 * This class contains various constants and static functions relating to the mouse's cursor.
 * 
 * @package Statics
 */
final class Cursor
{
	const Arrow = 'default';
	const Crosshair = 'crosshair';
	const Hand = 'pointer';
	const EastResize = 'e-resize';
	const Help = 'help';
	const Move = 'move';
	const NorthResize = 'n-resize';
	const NorthEastResize = 'ne-resize';
	const NorthWestResize = 'nw-resize';
	const SouthResize = 's-resize';
	const SouthEastResize = 'se-resize';
	const SouthWestResize = 'sw-resize';
	const Text = 'text';
	const WestResize = 'w-resize';
	const Wait = 'wait';
	
	private function Cursor(){}
	/**
	 * Provides a URL for the image of a mouse cursor 
	 * @param string $str
	 * @return mixed
	 */
	public static function URL($str)
	{
		return 'url('.$str.')';
	}
}

?>