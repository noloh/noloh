<?php
/**
 * Priority class
 *
 * This class contains various constants relating to the priorities of elements in a priority queue structure.
 * These can be used with, for example, static methods of the ClientScript class such as ClientScript::Queue.
 * 
 * @package Statics
 */
final class Priority
{
	/**
	 * The highest priority
	 */
	const High = 0;
	/**
	 * A balanced priority
	 */
	const Medium = 1;
	/**
	 * The lowest priority
	 */
	const Low = 2;
	
	private function Priority(){}
}
?>