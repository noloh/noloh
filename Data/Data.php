<?php
/**
 * Data class
 * 
 * The Data class is used as a conduit to your program's active data connections. The class is final and thus never instantiated, 
 * rather you can use certain statics of the class in conjuction with the methods of the other Data related classes.
 * 
 * @package Data
 */
final class Data extends Object
{	
	/**
	 * Represents the PostgreSQL database type, to be used in conjuction with other data related functions.
	 */
	const Postgres = 'postgres';
	/**
	 * Represents the MySQL database type, to be used in conjuction with other data related functions.
	 */
	const MySQL = 'mysql';
	/**
	 * Represents the Microsoft SQL Server datbase type, to be used in conjuction with other data related functions.
	 */
	const MSSQL = 'mssql';
	/**
	 * Represents a generic ODBC database connector type, to be used in conjuction with other data related functions.
	 */
	const ODBC = 'odbc';
	/**
	 * Data is returned associatively, this means that all data indices will be indexed by their column names, and not
	 * their numeric indices.
	 */
	const Assoc = 1;
	/**
	 * Data is returned numerically, this means that all data indices will be indexed by the column number, starting with 0.
	 * Associate indexes will not be accessible.
	 */
	const Num = 2;
	/**
	 * Data is returned with both it's associative and numeric indicies. This means that you can access the data either through
	 * a column name, or the column number. However, it's important to kep in mind that the count of the columns will not
	 * accurately represent the number of columns as there will be twice as many columns.
	 */
	const Both = 3;
	/**
	 * Specifies that your query or command is SQL.
	 */
	const SQL = 'sql';
	/**
	 * Specifies that your query or command is accessing a view.
	 */
	const View = 'view';
	/**
	 * Specified that your query or command is accessing a function in your database. 
	 * Stored function and stored procedures are examples of this.
	 */
	const Func = 'function';
	/**
	 * $Links is a direct conduit to access any open Data Links/Connections. Multiple DataConnections can be accessed through 
	 * the $Links Data object.
	 * 
	 * Lets look at the following example:
	 * <pre>
	 * 	   //This will create and store a DataConnection to a PostgreSQL database with the name of 'new_york_stores'
	 *     Data::$Links->Stores = new DataConnection(Data::Postgres, 'new_york_stores');
	 *     //This will create and store a DataConnection to a MySQL database with the name of 'new_york_people'
	 *     Data::$Links->People = new DataConnection(Data::MySQL, 'new_york_people');
	 *  </pre>
	 *  
	 *  Now at any point in our program we can access any of our DataConnections through the Data::$Links conduit.
	 * <pre>
	 *     Data::$Links->Stores->ExecFunction('public.sp_add_store', 'The NOLOH Store', '21 Jump Street');
	 *     Data::$Links->People->ExecFunction('public.sp_add_person', 'Woody', 'Allen');
	 * </pre>
	 * 
	 * @see Please see the Data::$Links articles for more information.
	 * @var Data
	 */
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