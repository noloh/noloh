<?php
/**
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
	
	public static function URL($str)
	{
		return "url($str)";
	}
}

?>