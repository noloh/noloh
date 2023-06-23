<?php
/**
 * Priority class
 *
 * This class contains various constants relating to the priorities of elements in a priority queue structure.
 * These can be used with, for example, static methods of the ClientScript class such as ClientScript::Queue, as follows:
 * 
 * <pre>
 * ClientScript::Queue($button, 'alert', 'Foo', false, Priority::Low);
 * ClientScript::Queue($button, 'alert', 'Bar', false, Priority::High);
 * // Bar will be alerted before Foo, despite the order of the calls, because Bar has a higher priority.
 * // However, under the same priorities, it will depend on the order of the calls.
 * </pre>
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
	
	private function __construct(){}
}
?>