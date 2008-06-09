<?php
/**
 * Data class
 * 
 * This class is for data
 * 
 * @package Data
 */
final class Data extends Object
{	
	const Postgres = 'postgres';
	const MySQL = 'mysql';
	const MSSQL = 'mssql';
	const ODBC = 'odbc';
	
	const Assoc = 1;
	const Num = 2;
	const Both = 3;
	
	static $Links;		

	function __get($name)
	{
		if(isset($_SESSION['_NDataLinks']) && isset($_SESSION['_NDataLinks'][$name]))
			return $_SESSION['_NDataLinks'][$name];
		return null;
	}
	function __set($name, $value)
	{
		return $_SESSION['_NDataLinks'][$name] = $value;
	}
}
Data::$Links = new Data();
?>