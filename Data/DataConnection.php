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
	 * The username used to connect to your database
	 * @var string 
	 */
	public $Username;
	/**
	 * The name of your database
	 * @var string 
	 */
	public $DatabaseName;
	/**
	 * The password used to connect to your database
	 * @var string 
	 */
	public $Password;
	/**
	 * Your database host, e.g; localhost, http://www.noloh.com, etc.
	 * @var mixed 
	 */
	public $Host;
	/**
	 * The port you use to connect to your database.
	 * @var mixed  
	 */
	public $Port;
	/**
	 * Your database link identifier resource.
	 * @var resource 
	 */
	public $ActiveConnection;
	private $Type;	
	/**
	 * Constructor
	 * Be sure to call this from the constructor of any class that extends DataConnection.
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
	 * Returns The database link identifier.
	 * @return resource 
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
		elseif($this->Type == Data::MSSQL)
		{
			$host = $this->Port?$this->Host . ':' . $this->Port:$this->Host;
			$this->ActiveConnection = mssql_connect($host, $this->Username, $this->Password);
			mssql_select_db($this->DatabaseName, $this->ActiveConnection);
		}
		return $this->ActiveConnection;
	}
	/**
	 * Attempts to close the connection to your database. Note: In most circumstances, this is done automatically.
	 * Returns whether the connection closed successfully.
	 * @return boolean
	 */
	function Close()
	{
		if(is_resource($this->ActiveConnection))
			if($this->Type == Data::Postgres)
				return pg_close($this->ActiveConnection);
			elseif($this->Type == Data::MySQL)
				return mysql_close($this->ActiveConnection);
			elseif($this->Type == Data::MSSQL)
				return mssql_close($this->ActiveConnection);
		return false;
	}
	/**
	 * Sets the type of the database you're connecting to.
	 * @param Data::Postgres|Data::MySQL|Data::MSSQL|Data::ODBC $type
	 */
	function SetType($type)	{$this->Type = $type;}
	/**
	 * Gets the type of the database you're connecting to.
	 * @return Data::Postgres|Data::MySQL|Data::MSSQL|Data::ODBC 
	 */
	function GetType()		{return $this->Type;}
	//Database Query Helper Functions
	private function GenerateSQL($sql, $paramArray=null)
	{
		$paramCount = count($paramArray);
		$paramNum = 1;
		$search = array();
		$replace = array();
		for($i=0; $i < $paramCount; ++$i)
		{
			$param = $paramArray[$i];
			if(is_array($param))
			{
				$count = count($param);
				if($count === 1)
				{
					$search[] = '/' . preg_quote(key($param), '/') . '\b/';
					$replace[] = $this->ConvertValueToSQL(current($param));
				}
				elseif($count == 2)
				{
					$search[] =  '/' . preg_quote($param[0], '/')  . '\b/';
					$replace[] = $this->ConvertValueToSQL($param[1]);
				}			
			}
			else
			{
				$search[] = '/\$' . $paramNum . '\b/';
				$replace[] = $this->ConvertValueToSQL($param);
			}
			++$paramNum;
		}
//		return str_replace($search, $replace, $sql);
		return preg_replace($search, $replace, $sql);
	}
	private function GenerateFunction($spName, $paramArray=null)
	{
		if($spName == null)
			return null;
			
		if($this->Type != Data::MSSQL)
		{
			$query = 'SELECT ';
			$begin = '(';
			$end = ')';
		}
		else
		{
			$query = 'EXEC ';
			$begin = ' ';
			$end = '';
		}
		if($this->Type == Data::Postgres)
			$query .= ' * FROM ';
		$query .= $spName . $begin;
		$numArgs = count($paramArray);
		if($this->Type == Data::Postgres)
		{
			for($i = 0; $i < $numArgs; ++$i)
			{
				$tmpArg = self::ConvertTypeToPostgres($paramArray[$i]);
				$query .= $tmpArg . ",";		
			}
		}
		elseif($this->Type == Data::MSSQL)
		{
			for($i = 0; $i < $numArgs; ++$i)
			{
				$tmpArg = self::ConvertTypeToMSSQL($paramArray[$i]);
				$query .= $tmpArg . ",";		
			}
		}
		else
		{
			$resource = $this->Connect();
			for($i = 0; $i < $numArgs; ++$i)
			{
				$tmpArg = self::ConvertTypeToMySQL($paramArray[$i], "'", $resource);
				$query .= $tmpArg . ",";		
			}
			$this->Close();
		}
		
		if($numArgs > 0)
			$query = rtrim($query, ',');
		$query .= $end . ';';
		return $query;
	}
	private function GenerateView($view, $offset=null, $limit=null)
	{
		$query = "SELECT * FROM $view";
		if($offset != null && is_numeric($offset))
			$query .= " OFFSET $offset";
		if($limit != null && is_numeric($limit))
			$query .= " LIMIT $limit";
		$query .= ';';
		return $query;
	}
	/**
	 * @ignore
	 */
	public function ConvertValueToSQL($value)
	{
		if($this->Type == Data::Postgres)
			$formattedValue = self::ConvertTypeToPostgres($value);
		elseif($this->Type == Data::MySQL)
		{
			$resource = $this->Connect();
			$formattedValue = self::ConvertTypeToMySQL($value, "'", $resource);
			$this->Close();
		}
		elseif($this->Type == Data::MSSQL)
		{
			$formattedValue = self::ConvertTypeToMSSQL($value);
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
			$tmpArg = $quote . $value . $quote;
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
	private static function ConvertTypeToMSSQL($value, $quote="'")
	{
		if(is_string($value))
		{
			$value = str_replace("'", "''", $value);
//			$value = mysql_real_escape_string($value, $resource);
			$tmpArg = $quote . $value . $quote;
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
	 * Note: The first parameter is optional, we can execute this function in the following 2 ways:
	 * <pre>
	 *     $people = Data::$Links->People->ExecSQL(Data::Assoc, 'SELECT * FROM people');
	 * </pre>
	 * or
	 * <pre>
	 *     $people = Data::$Links->People->ExecSQL('SELECT * FROM people');
	 * </pre>
	 * 
	 * Also you can set replacements to your query using parameters. You can use numbered paramaters, or your own through an array.
	 * 
	 * For example, we can specify replacements for our city and state using numbered replacements. NOLOH will automatically replace your $n using the parameters you specified in sequence.
	 * <pre>
	 *     $people = Data::$Links->People->ExecSQL('SELECT * FROM people WHERE city=$1 and state = $2', 'Brooklyn', 'New York');
	 * </pre>
	 * Alternatively, you can specify replacements using an array with your own names.
	 * <pre>
	 *     $people = Data::$Links->People->ExecSQL('SELECT * FROM people WHERE city=:city and state = :state', array(':city', 'Brooklyn'), array(':state', New York'));
	 * </pre>
	 * You can also specify your array in key => value format.
	 * <pre>
	 *     $people = Data::$Links->People->ExecSQL('SELECT * FROM people WHERE city=:city and state = :state', array(':city' => 'Brooklyn'), array(':state' => New York'));
	 * </pre>
	 * Finally, you can mix and match the different types of replacements.
	 * <pre>
	 *     $people = Data::$Links->People->ExecSQL('SELECT * FROM people WHERE name = $1, city=:city and state = :state', 'Asher', array(':city' => 'Brooklyn'), array(':state', New York'));
	 * </pre>
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Optional: The format of the data column indices returned by the function.
	 * @param string $sql The SQL query.
	 * @param mixed,... $paramsDotDotDot Optional: Replacements to your SQL query. NOLOH takes care of formatting the value properly for your database.
	 * 
	 * @return DataReader A DataReader containing the resulting data of your query.
	 */
	function ExecSQL($resultType, $sql='', $paramsDotDotDot = null)
	{
		$args = func_get_args();
		$sql = $resultType;
		$paramsArg = 1;
		if($hasResultOption = is_int($resultType))
		{
			$resultOption = $resultType;
			$sql = $args[1];
			$paramsArg = 2;
		}
		$sql = self::GenerateSQL($sql, array_slice($args, $paramsArg));
		$dbCmd = new DataCommand($this, $sql);
		return $dbCmd->Execute($hasResultOption?$resultOption:Data::Both);
	}
	/**
	 * Executes a stored procedure or stored function in your database. You can natively pass in as many parameters to your function as you wish
	 * through the dotdotdot syntactic sugar.
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
	 * 
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Optional: The format of the data column indices returned by the function.
	 * @param string $spName The name of the database stored procedure or stored function that you wish to execute. Note: If your database
	 * supports schemas and you want to access a non-public schema make sure you prefix the name with your schema name, e.g; 'cars.sp_get_convertibles'.
	 * @param mixed,... $paramsDotDotDot Optional: The parameters of your function. NOLOH takes care of formatting the value properly for your database.
	 * @return DataReader A DataReader containing the resulting data of your query.
	 */
	function ExecFunction($spName, $paramsDotDotDot = null)
	{
		$args = func_get_args();
		$resultOption = null;
		if($hasResultOption = is_int($spName))
		{
			$resultOption = $spName;
			$spName = $args[1];
		}
		$query = self::GenerateFunction($spName, array_slice($args, $hasResultOption?2:1));
		$dbCmd = new DataCommand($this, $query, $resultOption);
		return $dbCmd->Execute($hasResultOption?$resultOption:Data::Both);
	}
	/**
	 * Executes a view in your database. 
	 * 
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
	 * 
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Optional: The format of the data column indices returned by the function.
	 * @param string $view The name of the database stored procedure or stored function that you wish to execute. Note: If your database
	 * supports schemas and you want to access a non-public schema make sure you prefix the name with your schema name, e.g; 'cars.v_get_all_convertibles'.
	 * @param integer $offset An optional offset to return results that are offset this many rows.
	 * @param integer $limit An optional limit to cap the number of rows you wish to return.
	 * @return DataReader A DataReader containing the resulting data of your query.
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
		$query = self::GenerateView($view, $offset, $limit);
		
		$dbCmd = new DataCommand($this, $query);
		return $dbCmd->Execute($hasResultOption?$resultOption:Data::Both);
	}
	/**
	 * Creates a command based on a SQL string, a View, a Stored Procedure, or Stored Function in your database. 
	 * 
	 * CreateCommand operates similar to the corresponding Exec functions. For instance, 
	 * when using it in the context of SQL, or Function, you can natively pass in as many parameters to your function as you wish
	 * through the dotdotdot syntactic sugar.
	 *
	 * Note: The first parameter is optional when creating a Function command. 
	 * The second, resultType paramater is optional across the board.
	 * 
	 * We can create a command based on a database function in the following ways:
	 * <pre>
	 *     $city = 'Brooklyn';
	 *     $state = 'New York';
	 * </pre>
	 * <pre>
	 *     $peopleCommand = Data::$Links->People->CreateCommand(Data::Assoc, 'sp_get_people' $city, $state);
	 * </pre>
	 * If you don't want to specify the column indices type.
	 * <pre>
	 *     $peopleCommand = Data::$Links->People->CreateCommand('sp_get_people' $city, $state);
	 * </pre>
	 * If you don't have any parameters to your function.
	 * <pre>
	 *     $peopleCommand = Data::$Links->People->CreateCommand('sp_get_people');
	 * </pre>
	 * 
	 * We can create a command based on SQL in the following ways:
	 * 
	 * <pre>
	 *     $peopleCommand = Data::$Links->People->CreateCommand(Data::SQL, Data::Assoc, "SELECT * FROM people WHERE city='Brooklyn' AND state='New York'");
	 * </pre>
	 * 
	 * Furthermore, we can create the command using paramaters for our SQL, like in ExecSQL.
	 * <pre>
	 *     $peopleCommand = Data::$Links->People->CreateCommand(Data::SQL, Data::Assoc, "SELECT * FROM people WHERE city=$1 AND state=$2", $city, $state);
	 * </pre>
	 * 
	 * We can also choose not to specify a column indices type.
	 * <pre>
	 *     $peopleCommand = Data::$Links->People->CreateCommand(Data::SQL, "SELECT * FROM people WHERE city=$1 AND state=$2", $city, $state);
	 * </pre>
	 * 
	 * We can create a command based on a View in the following ways:
	 * <pre>
	 *     $peopleCommand = Data::$Links->People->CreateCommand(Data::View, Data::Assoc, 'v_get_all_people');
	 * </pre>
	 * 
	 * We can also choose not to specify a column indicies type.
	 * <pre>
	 *     $peopleCommand = Data::$Links->People->CreateCommand(Data::View, 'v_get_all_people');
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
	 * 
	 * @param mixed Data::SQL|Data::View|Data::Function $commandType Optional: The type of query you're creating.
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Optional: The format of the data column indices returned by the function.
	 * @param string $spName The name of the database stored procedure or stored function that you wish to execute. Note: If your database
	 * supports schemas and you want to access a non-public schema make sure you prefix the name with your schema name, e.g; 'cars.sp_get_convertibles'.
	 * @param mixed,... $paramsDotDotDot Optional: The parameters of your function. NOLOH takes care of formatting the value properly for your database.
	 * @return DataCommand A DataCommand to be executed later. This can also be used in conjuction with Bind functions in certain Controls.
	 */
	function CreateCommand($spName, $paramsDotDotDot=null)
	{
		$args = func_get_args();
		$index = 0;
		$arg = $args[$index];
		$type = Data::Func;
		if($arg == Data::SQL || $arg == Data::View || $arg == Data::Func)
		{
			$type = $arg;
			$arg = $args[++$index];
		}
		if($hasResultOption = is_int($arg))
		{
			$resultOption = $arg;
			++$index;
		}
		$spName = $args[$index];
		$args = array_slice($args, $index + 1);
		if($type == Data::Func)
			$query = self::GenerateFunction($spName, $args);
		elseif($type == Data::SQL)
			$query = self::GenerateSQL($spName, $args);
		elseif($type == Data::View)
			$query = self::GenerateView($spName);
		return new DataCommand($this, $query, $hasResultOption?$resultOption:Data::Both);
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