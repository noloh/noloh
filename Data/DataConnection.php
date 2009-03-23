<?php
/**
 * DataConnection class
 *
 * A DataConnection represents a connection to your database. DataConnection is usually used in conjuction with Data::$Links.
 * Each Link residing in Data::$Links should be an instantiated DataConnection. It is also possible to use DataConnections independently
 * of Data::$Links.
 * 
 * The following example shows the use of a DataConnection independently of Data::$Links:
 * <pre>
 *     $peopleDatabase = new DataConnection(Data::Postgres, 'new_york_people');
 *     //Passes the $peopleDatabase DataConnection to be used in a DataCommand.
 *     $query = new DataCommand($peopleDatabase, 'SELECT * FROM people');
 *     //Executes the DataCommand and stores the resulting DataReader in $results
 *     $results = $query->Execute();
 * </pre>
 * 
 * This example shows the use of DataConnection in conjuction with Data::$Links.
 * <pre>
 *     Data::$Links->People = new DataConnection(Data::Postgres, 'new_york_people');
 *     //Executes the SQL query and stores the resulting DataReader in $results
 *     $results = Data::$Links->ExecSQL('SELECT * FROM people'); 
 * </pre>
 * 
 * @package Data
 */
class DataConnection extends Object
{
	/**
	 * @var string The username used to connect to your database
	 */
	public $Username;
	/**
	 * @var string The name of your database
	 */
	public $DatabaseName;
	/**
	 * @var string The password used to connect to your datbase
	 */
	public $Password;
	/**
	 * @var mixed Your database host, e.g; localhost, http://www.noloh.com, etc.
	 */
	public $Host;
	/**
	 * @var mixed The port you use to connect to your database. 
	 */
	public $Port;
	/**
	 * @var resource Your database link identifier resource.
	 */
	public $ActiveConnection;
	private $Type;
	
	/**
	 * @param mixed Data::Postgres|Data::MySQL|Data::MSSQL|Data::ODBC $type The type of the database you're connecting to.
	 * @param string $databaseName The name of your database
	 * @param string $username The username used to connect to your database
	 * @param string $password The password used to connect to your datbase
	 * @param mixed $host Your database host, e.g; localhost, http://www.noloh.com, etc.
	 * @param mixed $port The port you use to connect to your database. 
	 */
	function DataConnection($type = Data::Postgres, $databaseName='',  $username='', $password='', $host='localhost', $port='5432')
	{
		$this->Username = $username;
		$this->DatabaseName = $databaseName;
		$this->Host = $host;
		$this->Port = $port;
		$this->Password = $password;
		$this->Type = $type;
	}
	/**
	 * Attempts to create a connection to your database.
	 * @return resource The database link identifier.
	 */
	function Connect()
	{
		if($this->Type == Data::Postgres)
		{
			if(!is_resource($this->ActiveConnection) || pg_connection_status($this->ActiveConnection) === PGSQL_CONNECTION_BAD)
			{
				$connectString = 'dbname = ' . $this->DatabaseName . ' user='.$this->Username .' host = '.$this->Host. ' port = '. $this->Port .' password = ' .$this->Password;
				$this->ActiveConnection = pg_connect($connectString);
			}
		}
		elseif($this->Type == Data::MySQL)
		{
			$this->ActiveConnection = mysql_connect($this->Host, $this->Username, $this->Password);
			mysql_select_db($this->DatabaseName, $this->ActiveConnection);
		}
		return $this->ActiveConnection;
	}
	/**
	 * Attempts to close the connection to your database. Note: In most circumstances, this is done automatically.
	 * @return bool Whether the connection was closed successfully.
	 */
	function Close()
	{
		if(is_resource($this->ActiveConnection))
			if($this->Type == Data::Postgres)
				return pg_close($this->ActiveConnection);
			elseif($this->Type == Data::MySQL)
				return mysql_close($this->ActiveConnection);
		return false;
	}
	/*
	 * @param mixed Data::Postgres|Data::MySQL|Data::MSSQL|Data::ODBC $type The type of the database you're connecting to.
	 */
	function SetType($type)	{$this->Type = $type;}
	/**
	 * 
	 *
	 * @return Data::Postgres|Data::MySQL|Data::MSSQL|Data::ODBC The currently set type.
	 */
	function GetType()		{return $this->Type;}
	
	//Database Query Helper Functions
	private function GenerateSqlString($spName, $paramsArray)
	{
		if($spName == null)
			return null;
			
		$query = 'SELECT ';
		if($this->Type == Data::Postgres)
			$query .= ' * FROM ';
		$query .= $spName . '(';
		$numArgs = count($paramsArray);
		if($this->Type == Data::Postgres)
		{
			for($i = 0; $i < $numArgs; ++$i)
			{
				$tmpArg = self::ConvertTypeToPostgres($paramsArray[$i]);
				$query .= $tmpArg . ",";		
			}
		}
		else
		{
			$resource = $this->Connect();
			for($i = 0; $i < $numArgs; ++$i)
			{
				$tmpArg = self::ConvertTypeToMySQL($paramsArray[$i], "'", $resource);
				$query .= $tmpArg . ",";		
			}
			$this->Close();
		}
		if($numArgs > 0)
			$query = rtrim($query, ',');
		$query .= ");";
		return $query;
	}
	/**
	 * @ignore
	 */
	public function ConvertValueToSQL($value)
	{
		if($this->Type == Data::Postgres)
			$formattedValue = self::ConvertTypeToPostgres($value);
		else
		{
			$resource = $this->Connect();
			$formattedValue = self::ConvertTypeToMySQL($value, "'", $resource);
			$this->Close();
		}
		return $formattedValue;
	}
	/**
	 * @ignore
	 */
	private static function ConvertTypeToPostgres($value, $quote="'")
	{
		if(is_string($value))
		{
			$value = pg_escape_string($value);
			$tmpArg = "$quote" . $value ."$quote";
		}
		elseif(is_int($value))
			$tmpArg = (int)$value;
		elseif(is_double($value))
			$tmpArg = (double)$value;
		elseif(is_bool($value))
			$tmpArg = ($value)?'true':'false';
		elseif(is_array($value))
			$tmpArg = self::ConvertToPostgresArray($value);
		elseif($value === null || $value == 'null') 
			$tmpArg = 'null';
		return $tmpArg;
	}
	private static function ConvertTypeToMySQL($value, $quote="'", $resource)
	{
		if(is_string($value))
		{
			$value = mysql_real_escape_string($value, $resource);
			$tmpArg = "$quote" . $value ."$quote";
		}
		elseif(is_int($value))
			$tmpArg = (int)$value;
		elseif(is_double($value))
			$tmpArg = (double)$value;
		elseif(is_bool($value))
			$tmpArg = ($value)?'true':'false';
		elseif($value === null || $value == 'null') 
			$tmpArg = 'null';
		return $tmpArg;
	}
	private static function ConvertToPostgresArray($array)
	{
		$args = array();
		$tmpArr = "'{";
		foreach($array as $arg)
			$args[] = self::ConvertTypeToPostgres($arg, "\"");
		$tmpArr .= implode(',', $args) . "}'";
		return $tmpArr;
	}
	/**
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Optional: The format of the data column indices returned by the function.
	 * @param string $sqlString The SQL query.
	 * 
	 * @return DataReader A DataReader containing the resulting data of your query.
	 * 
	 * Note: The first parameter is optional, we can execute this funciton in the following 2 ways:
	 * <pre>
	 *     $people = Data::$Links->People->ExecSQL(Data::Assoc, 'SELECT * FROM people');
	 * </pre>
	 * Or
	 * <pre>
	 *     $people = Data::$Links->People->ExecSQL('SELECT * FROM people');
	 * </pre>
	 */
	function ExecSQL($resultType, $sqlString='')
	{
		$args = func_get_args();
		$sql = $resultType;
		if($hasResultOption = is_int($resultType))
		{
			$resultOption = $resultType;
			$sql = $args[1];
		}
		$dbCmd = new DataCommand($this, $sql);
		$tmpReturn = $dbCmd->Execute($hasResultOption?$resultOption:Data::Both);
//		$dbCmd->Connection->Close();
		return $tmpReturn;
	}
	/**
	 * Executes a stored procedure or stored function in your database. You can natively pass in as many parameters to your function as you wish
	 * through the dotdotdot syntactic sugar.
	 * 
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Optional: The format of the data column indices returned by the function.
	 * @param string $spName The name of the database stored procedure or stored function that you wish to execute. Note: If your database
	 * supports schemas and you want to access a non-public schema make sure you prefix the name with your schema name, e.g; 'cars.sp_get_convertibles'.
	 * @param mixed $paramsDotDotDot Optional: The parameters of your function. NOLOH takes care of formatting the value properly for your database.
	 * @return DataReader A DataReader containing the resulting data of your query.
	 * 
	 * Note: The first parameter is optional, we can execute this function in the following ways:
	 * <pre>
	 *     $city = 'Brooklyn';
	 *     $state = 'New York';
	 * </pre>
	 * <pre>
	 *     $people = Data::$Links->People->ExecFunction(Data::Assoc, 'sp_get_people' $city, $state);
	 * </pre>
	 * If you don't want to specify the column indices type.
	 * <pre>
	 *     $people = Data::$Links->People->ExecFunction('sp_get_people' $city, $state);
	 * </pre>
	 * If you don't have any parameters to your function.
	 * <pre>
	 *     $people = Data::$Links->People->ExecFunction('sp_get_people');
	 * </pre>
	 * 
	 * Please see the Data::$Links article for more information and examples.
	 */
	function ExecFunction($spName, $paramsDotDotDot = null)
	{
		$args = func_get_args();
		if($hasResultOption = is_int($spName))
		{
			$resultOption = $spName;
			$spName = $args[1];
		}
		$query = self::GenerateSqlString($spName, array_slice($args, $hasResultOption?2:1));
		$dbCmd = new DataCommand($this, $query, $resultOption);
		$tmpReturn = $dbCmd->Execute($hasResultOption?$resultOption:Data::Both);
//		$dbCmd->Connection->Close();
		return $tmpReturn;
	}
	/**
	 * Executes a view in your database. 
	 * 
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Optional: The format of the data column indices returned by the function.
	 * @param string $view The name of the database stored procedure or stored function that you wish to execute. Note: If your database
	 * supports schemas and you want to access a non-public schema make sure you prefix the name with your schema name, e.g; 'cars.v_get_all_convertibles'.
	 * @param integer $offset An optional offset to return results that are offset this many rows.
	 * @param integer $limit An optional limit to cap the number of rows you wish to return.
	 * @return DataReader A DataReader containing the resulting data of your query.
	 * 
	 * Note: The first parameter is optional, we can execute this function in the following ways:
	 * <pre>
	 *     $people = Data::$Links->People->ExecFunction(Data::Assoc, 'v_get_all_people');
	 * </pre>
	 * If you don't want to specify the column indices type.
	 * <pre>
	 *     $people = Data::$Links->People->ExecFunction('v_get_all_people');
	 * </pre>
	 * If you want to offset and limit the data returned.
	 * <pre>
	 *     //This will offset the result by 10 rows and limit the result to a maximum of 100 rows.
	 *     $people = Data::$Links->People->ExecFunction('v_get_all_people', 10, 100);
	 * </pre>
	 * 
	 * Please see the Data::$Links article for more information and examples.
	 */
	function ExecView($view, $offset=null, $limit=null)
	{
		$args = func_get_args();
		if(($hasResultOption = is_int($view)))
		{
			$resultOption = $view;
			$view = $args[1];
			if(isset($args[2]))
				$offset = $args[2];
			if(isset($args[3]))
				$limit = $args[3];
		}
		$query = "SELECT * FROM $view";
		if($offset != null && is_numeric($offset))
			$query .= " OFFSET $offset";
		if($limit != null && is_numeric($limit))
			$query .= " LIMIT $limit";
		$query .= ';';
		$dbCmd = new DataCommand($this, $query);
		$tmpReturn = $dbCmd->Execute($hasResultOption?$resultOption:Data::Both);
//		$dbCmd->Connection->Close();
		
		return $tmpReturn;
	}
	/**
	 * Creates a command based on a stored procedure or stored function in your database. You can natively pass in as many parameters to your function as you wish
	 * through the dotdotdot syntactic sugar.
	 * 
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Optional: The format of the data column indices returned by the function.
	 * @param string $spName The name of the database stored procedure or stored function that you wish to execute. Note: If your database
	 * supports schemas and you want to access a non-public schema make sure you prefix the name with your schema name, e.g; 'cars.sp_get_convertibles'.
	 * @param mixed $paramsDotDotDot Optional: The parameters of your function. NOLOH takes care of formatting the value properly for your database.
	 * @return DataCommand A DataCommand to be executed later. This can also be used in conjuction with Bind functions in certain Controls.
	 * 
	 * Note: The first parameter is optional, we can execute this function in the following ways:
	 * <pre>
	 *     $city = 'Brooklyn';
	 *     $state = 'New York';
	 * </pre>
	 * <pre>
	 *     $peopleCommand = Data::$Links->People->CreateCommand(Data::Assoc, 'sp_get_people' $city, $state);
	 * </pre>
	 * If you don't want to specify the column indices type.
	 * <pre>
	 *     $peopleCommand = Data::$Links->People->ExecFunction('sp_get_people' $city, $state);
	 * </pre>
	 * If you don't have any parameters to your function.
	 * <pre>
	 *     $peopleCommand = Data::$Links->People->ExecFunction('sp_get_people');
	 * </pre>
	 * 
	 * At a later point we can execute the command:
	 * <pre>
	 *     $people = $peopleCommand->Execute();
	 * </pre>
	 * Or we can Bind against it:
	 * <pre>
	 *     $listView = new ListView();
	 *     $listView->Bind($peopleCommand);
	 *     //See ListView Bind() for more information on Bind.
	 * </pre>
	 * 
	 * Please see the Data::$Links article for more information and examples.
	 */
	function CreateCommand($spName, $paramsDotDotDot)
	{
		$args = func_get_args();
		if($hasResultOption = is_int($spName))
		{
			$resultOption = $spName;
			$spName = $args[1];
		}
		$query = self::GenerateSqlString($spName, array_slice($args, $hasResultOption?2:1));
		$dbCmd = new DataCommand($this, $query, $hasResultOption?$resultOption:Data::Both);
		return $dbCmd;
	}
	/**
	 * @ignore
	 */
	function __call($name, $args)
	{
		array_splice($args, 0, 0, $name);
		call_user_func_array(array($this, 'ExecFunction'), $args);
	}
}
?>