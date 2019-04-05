<?php
/**
 * Raw Parameter
 *
 * The Raw Parameter class is used to inject code into SQL statements that don't need to be parsed
 * such as SQL function calls or object names like tables, columns, databases, etc
 */
class RawParameter extends Base
{
	protected $Parameter;
	public $Values;
	
	function RawParameter($paramsDotDotDot)
	{
		$args = func_get_args();

		$this->Parameter = array_shift($args);
		$this->Values = $args;
	}
	function GetParameter(DataConnection $connection)
	{
		$search = array();
		$replace = array();
		foreach ($this->Values as $i => $value)
		{
			$search[] = '$' . ++$i;
			$replace[] = $connection->ConvertValueToSQL($value);
		}

		return str_replace($search, $replace, $this->Parameter);
	}
}