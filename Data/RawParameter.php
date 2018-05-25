<?php
/**
 * Raw Parameter
 *
 * The Raw Parameter class is used to inject code into SQL statements that don't need to be parsed
 * such as SQL function calls or object names like tables, columns, databases, etc
 */
class RawParameter extends Object
{
	public $Parameter;
	
	function RawParameter($param)
	{
		$this->Parameter = $param;
	}
}