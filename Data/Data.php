<?php
/**
 * Data class
 * 
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
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
	/**
	 * @ignore
	 */
	function __get($name)
	{
		if(isset($_SESSION['_NDataLinks']) && isset($_SESSION['_NDataLinks'][$name]))
			return $_SESSION['_NDataLinks'][$name];
		return null;
	}
	/**
	 * @ignore
	 */
	function __set($name, $value)
	{
		return $_SESSION['_NDataLinks'][$name] = $value;
	}
}
Data::$Links = new Data();
?>