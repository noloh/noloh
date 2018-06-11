<?php
/**
 * Raw Parameter
 *
 * The Raw Parameter class is used to inject code into SQL statements that don't need to be parsed
 * such as SQL function calls or object names like tables, columns, databases, etc
 */
class RawParameter extends Object
{
	protected $Parameter;
	
	function RawParameter($param)
	{
		if (preg_match("/^[a-zA-Z0-9_\(\)]*$/", $param))
		{
			$this->Parameter = $param;
		}
		else
		{
			BloodyMurder('RawParameter parameter can only contain valid function, table, or column names');
		}
	}
	function GetParameter()
	{
		return $this->Parameter;
	}
}