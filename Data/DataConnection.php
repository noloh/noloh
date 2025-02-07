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
class DataConnection extends Base
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
	private $Password;
	private $PasswordEncrypted;
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
	/**
	 * Any additional connection parameters that are needed
	 *
	 * @var array
	 */
	public $AdditionalParams;
	/**
	 * Whether the connection should convert your data results to
	 * their PHP real type equivalents, or leave them as strings.
	 *
	 * Defaults to false!
	 *
	 * @var Boolean
	 */
	public $ConvertType;
	private $Type;
	private $Persistent;

	protected $FriendlyCallBack;
	protected $AfterConnectCallBack;
	protected $AfterConnectCalled = false;
	public $Name = '_Default';
	static $TransactionCounts;

	private $ODBCType;
	/**
	 * Constructor
	 * Be sure to call this from the constructor of any class that extends DataConnection.
	 * @param mixed Data::Postgres|Data::MySQL|Data::MSSQL|Data::ODBC $type The type of the database you're connecting to.
	 * @param string $databaseName The name of your database
	 * @param string $username The username used to connect to your database
	 * @param string $password The password used to connect to your datbase
	 * @param mixed $host Your database host or ODBC DSN Name, e.g; localhost, http://www.noloh.com, etc.
	 * @param mixed $port The port you use to connect to your database.
	 * @param bool $passwordEncrypted Whether the password is encrypted or not
	 * @param array $additionalParams additional parameters to be used
	 * @param array $friendlyCallBack callback function for handling SQLFriendlyException
	 * @param array $afterConnectCallBack callback function for after a succesful connection is made
	 */
	function __construct($type = Data::Postgres, $databaseName = '',  $username = '', $password = '', $host = 'localhost', $port = '5432', $passwordEncrypted = false, $additionalParams = array(), $friendlyCallBack = array(), $afterConnectCallBack = array())
	{
		$this->Username = $username;
		$this->DatabaseName = $databaseName;
		$this->Host = $host;
		$this->Port = $port;
		$this->Password = $password;
		$this->PasswordEncrypted = $passwordEncrypted;
		$this->Type = $type;
		$this->AdditionalParams = $additionalParams;
		$this->FriendlyCallBack = $friendlyCallBack;
		$this->AfterConnectCallBack = $afterConnectCallBack;

		if ($type === Data::ODBC)
		{
			if ($databaseName !== null || $port !== null)
			{
				BloodyMurder('DatabaseName and Port not supported for ODBC Connections. Pass DSN Name as Host parameter.');
			}
			if (empty($additionalParams['odbc_type']) || !in_array($additionalParams['odbc_type'], Data::$ODBCTypes, true))
			{
				BloodyMurder('A valid odbc_type is required as an additional param for ODBC connections');
			}

			$this->ODBCType = $additionalParams['odbc_type'];
		}
	}
	/**
	 * Attempts to create a connection to your database.
	 * Returns The database link identifier.
	 * @return resource
	 */
	function Connect()
	{
		$password = $this->Password;
		if ($this->PasswordEncrypted)
		{
			$encryptionKey = Security::GetEncryptionKeyFromPath();
			$password = Security::Decrypt($password, $encryptionKey);
		}
		System::BeginBenchmarking('_N/DataCommand::Connect');
		if (!self::IsActiveConnection($this->ActiveConnection) || ($this->Type === Data::Postgres && pg_connection_status($this->ActiveConnection) === PGSQL_CONNECTION_BAD))
		{
			if ($this->Type == Data::Postgres)
			{
				$connectString = 'dbname = ' . $this->DatabaseName . ' user=' . $this->Username . ' host = ' . $this->Host . ' port = ' . $this->Port . ' password = ' . $password;

				$this->ActiveConnection = @pg_connect($connectString);

				if ($this->ActiveConnection === false)
				{
					throw new Exception("Failed to connect to database: {$this->DatabaseName}");
				}
			}
			elseif ($this->Type == Data::MySQL)
			{
				if ($this->Persistent)
				{
					$this->ActiveConnection = mysql_pconnect($this->Host, $this->Username, $password);
				}
				else
				{
					$this->ActiveConnection = mysql_connect($this->Host, $this->Username, $password);
				}
				mysql_select_db($this->DatabaseName, $this->ActiveConnection);
			}
			elseif ($this->Type == Data::MSSQL)
			{
				$host = $this->Port ? $this->Host . ', ' . $this->Port : $this->Host;

				if (function_exists('sqlsrv_connect'))
				{
					$connectString = array(
						'Database' => $this->DatabaseName, 'ReturnDatesAsStrings' => true
					);

					if (!is_null($password))
					{
						$connectString['PWD'] = $password;
					}

					if (!is_null($this->Username))
					{
						$connectString['UID'] = $this->Username;
					}

					$connectionParams = array_merge($connectString, $this->AdditionalParams);
					$this->ActiveConnection = sqlsrv_connect($host, $connectionParams);

					if (!$this->ActiveConnection)
					{
						$this->ErrorOut(null, null);
					}
				}
				else
				{
					$this->ActiveConnection = mssql_connect($host, $this->Username, $password);
					mssql_select_db($this->DatabaseName, $this->ActiveConnection);
				}
			}
			elseif ($this->Type === Data::ODBC)
			{
				$this->ActiveConnection = odbc_connect($this->Host, $this->Username, $password);
			}
			else
			{
				BloodyMurder("Invalid connection type {$this->Type}");
			}
		}

		if (
			!$this->AfterConnectCalled
			&& empty($_SESSION['_NOmniscientBeing'])	// Only empty after OmniscientBeing finished unserializing properly
			&& self::IsActiveConnection($this->ActiveConnection)
			&& !empty($this->AfterConnectCallBack)
		)
		{
			$this->AfterConnectCalled = true;
			call_user_func_array($this->AfterConnectCallBack, array($this));
		}
		Application::$RequestDetails['total_database_time'] += System::Benchmark('_N/DataCommand::Connect');
		return $this->ActiveConnection;
	}
	/**
	 * Displays the appropriate error to the user and exits the app.
	 */
	function ErrorOut($connection, $sql)
	{
		$type = $this->Type;
		if ($type == Data::Postgres)
		{
			$error = pg_last_error($connection);
		}
		elseif($type == Data::MySQL)
		{
			$error = mysql_error();
		}
		elseif($type == Data::MSSQL)
		{
			if (function_exists('sqlsrv_errors'))
			{
				$error = '';
				$errors = sqlsrv_errors();
				foreach($errors as $errStr)
				{
					$error .= 'State: '.$errStr[ 'SQLSTATE'] . '; ' .
						'Code: ' . $errStr['code'] . '; ' .
						'Message: ' . $errStr[ 'message'] . "\n";
				}
			}
			else
			{
				$error = mssql_get_last_message();
			}
		}
		else if ($type === Data::ODBC)
		{
			$error = odbc_errormsg($connection);
		}

		if (strpos($error, 'SQL_FRIENDLY_EXCEPTION') && !empty($this->FriendlyCallBack))
		{
			$exception = new SqlFriendlyException($error, $this->FriendlyCallBack);
		}
		else
		{
			$exception = new SqlException($error);
		}

		if ($type == Data::Postgres)
		{
			$this->Rollback();
		}
		throw $exception;
	}
	/**
	 * Attempts to close the connection to your database. Note: In most circumstances, this is done automatically.
	 * Returns whether the connection closed successfully.
	 * @return boolean
	 */
	function Close()
	{
		if (self::IsActiveConnection($this->ActiveConnection))
		{
			$ret = false;
			if ($this->Type == Data::Postgres)
			{
				$ret = @pg_close($this->ActiveConnection);
			}
			elseif ($this->Type == Data::MySQL)
			{
				$ret = mysql_close($this->ActiveConnection);
			}
			elseif ($this->Type == Data::MSSQL)
			{
				if (function_exists('sqlsrv_close'))
				{
					$ret = sqlsrv_close($this->ActiveConnection);
				}
				else
				{
					$ret = mssql_close($this->ActiveConnection);
				}
			}
			elseif ($this->Type === Data::ODBC)
			{
				$ret = odbc_close($this->ActiveConnection);
			}

			if ($ret)
			{
				$this->ActiveConnection = null;
			}
			return $ret;
		}
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
		if (!$paramCount)
		{
			return $sql;
		}

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
				$sql = preg_replace('/(?:\$' . $paramNum . ')([^\d.]|$)/', '_N_' . $paramNum . '_N_' . '${1}', $sql);
				$search[] = '/_N_' . $paramNum . '_N_\b/';

				$param = $this->ConvertValueToSQL($param);

				$replace[] = addcslashes($param, '\\$');
			}
			++$paramNum;
		}
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
		if ($value instanceof RawParameter)
		{
			$formattedValue = $value->GetParameter($this);
		}
		else if ($this->Type == Data::Postgres)
		{
			$formattedValue = self::ConvertTypeToPostgres($value);
		}
		else if ($this->Type == Data::MySQL)
		{
			$resource = $this->Connect();
			$formattedValue = self::ConvertTypeToMySQL($value, "'", $resource);
			$this->Close();
		}
		else if ($this->Type == Data::MSSQL)
		{
			$formattedValue = self::ConvertTypeToMSSQL($value);
		}
		else if ($this->Type == Data::ODBC)
		{
			$formattedValue = self::ConvertTypeToGeneric($value, "'", ($this->ODBCType === Data::ODBCAccess));
		}

		return $formattedValue;
	}
	/**
	 * @ignore
	 */
	private static function ConvertTypeToGeneric($value, $quote = "'", $escapeQuoteWithQuote = false)
	{
		if (is_string($value))
		{
			$search = array("\\", "\x00", "\n", "\r",  $quote, "\x1a");
			if ($escapeQuoteWithQuote)
			{
				$replace = array("\\\\", "\\0", "\\n", "\\r", "{$quote}{$quote}", "\\Z");
			}
			else
			{
				$replace = array("\\\\", "\\0", "\\n", "\\r", "\\{$quote}", "\\Z");
			}
			$tmpArg = "$quote" . str_replace($search, $replace, $value) . "$quote";
		}
		elseif (is_int($value))
		{
			$tmpArg = (int)$value;
		}
		elseif (is_double($value))
		{
			$tmpArg = (double)$value;
		}
		elseif (is_bool($value))
		{
			$tmpArg = ($value) ? 'true' : 'false';
		}
		elseif (is_array($value))
		{
			BloodyMurder('Array parameters not supported for this connection type');
		}
		elseif ($value === null || $value == 'null')
		{
			$tmpArg = 'null';
		}
		return $tmpArg;
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
	 * Specifies whether the connection should force persistance. Only works with MySQL connections.
	 * By default connections are NOT forced persistent, but will try to be persistent via normal db methods
	 *
	 * @param bool $persistant Whether the connection should be forced persisatnt
	 */
	function SetPersistent($persistent)
	{
		$this->Persistent = $persistent;
	}
	/**
	 * @ignore
	 */
	function __call($name, $args)
	{
		// Duplicated from Base because this cannot parent::__call
		// Backwards compatibility before PHP8
		if (class_exists($name, false) && is_a($this, $name))
		{
			$constructor = new ReflectionMethod($name, '__construct');
			return $constructor->invokeArgs($this, $args);
		}


		array_splice($args, 0, 0, $name);
		call_user_func_array(array($this, 'ExecFunction'), $args);
	}
	function BeginTransaction()
	{
		static::$TransactionCounts[$this->Name]++;
		if (static::$TransactionCounts[$this->Name] === 1)
		{
			if ($this->Type === Data::Postgres)
			{
				$this->ExecSQL('START TRANSACTION READ WRITE;');
			}
			elseif ($this->Type === Data::MSSQL)
			{
				if ($this->ActiveConnection === null)
				{
					$this->ActiveConnection = $this->Connect();
				}
				sqlsrv_begin_transaction($this->ActiveConnection);
			}
		}
	}
	function Commit()
	{
		static::$TransactionCounts[$this->Name]--;
		if (static::$TransactionCounts[$this->Name] === 0)
		{
			if ($this->Type === Data::Postgres)
			{
				$this->ExecSQL('COMMIT;');
			}
			elseif ($this->Type === Data::MSSQL)
			{
				sqlsrv_commit($this->ActiveConnection);
			}
		}
	}
	function ForceCommit()
	{
		if (isset(static::$TransactionCounts[$this->Name]) &&
			static::$TransactionCounts[$this->Name] > 0)
		{
			static::$TransactionCounts[$this->Name] = 0;

			if ($this->Type === Data::Postgres)
			{
				$this->ExecSQL('COMMIT;');
			}
			elseif ($this->Type === Data::MSSQL)
			{
				sqlsrv_commit($this->ActiveConnection);
			}
		}
	}
	function Rollback()
	{
		if (!empty(static::$TransactionCounts[$this->Name]))
		{
			static::$TransactionCounts[$this->Name] = 0;

			if ($this->Type === Data::Postgres)
			{
				$this->ExecSQL('ROLLBACK;');
			}
			elseif ($this->Type === Data::MSSQL)
			{
				sqlsrv_rollback($this->ActiveConnection);
			}
		}
	}
	/*
	 * Creates either pg_advisory_lock or pg_advisory_xact_lock depending on the $xact perameter.
	 * If $xact is true, pg_advisory_xact_lock is started, as well as a new transaction. To unlock,
	 * call Commit or ForceCommit. If $xact is false, pg_advisory_lock is started and must be explicitly
	 * unlocked via AdvisoryUnlock.
	 *
	 * @param string $str is used to create the 64 bit hash key for the lock
	 * @param bool $xact is used to decide which type of lock to use
	 */
	function AdvisoryLock($key, $xact = true)
	{
		if (in_array($this->Type, array(Data::Postgres, Data::MSSQL)))
		{
			if ($this->Type === Data::Postgres)
			{
				$key = System::Get64BitHash($key);

				if ($xact)
				{
					$this->BeginTransaction();

					$query = <<<SQL
						SELECT pg_advisory_xact_lock($1::BIGINT);
SQL;
				}
				else
				{
					$query = <<<SQL
						SELECT pg_advisory_lock($1::BIGINT);
SQL;
				}

				$this->ExecSQL($query, $key);
			}
			else
			{
				$owner = $xact ? 'Transaction' : 'Session';
				$query = <<<SQL
					EXEC sp_GetAppLock $1, 'Exclusive', $2;
SQL;
				$this->ExecSQL($query, $key, $owner);
			}
		}
		else
		{
			BloodyMurder('Not yet supported for this database type');
		}
	}
	/*
	 * Unlocks pg_advisory_lock
	 *
	 * @param string $str is used to create the 64 bit hash key for the lock
	 */
	function AdvisoryUnlock($key, $xact = true)
	{
		if (in_array($this->Type, array(Data::Postgres, Data::MSSQL)))
		{
			if ($this->Type === Data::Postgres)
			{
				$key = System::Get64BitHash($key);

				$query = <<<SQL
					SELECT pg_advisory_unlock($1::BIGINT);
SQL;
				$this->ExecSQL($query, $key);
			}
			else
			{
				$owner = $xact ? 'Transaction' : 'Session';
				$query = <<<SQL
					EXEC sp_ReleaseAppLock $1, $2;
SQL;
				$this->ExecSQL($query, $key, $owner);
			}
		}
		else
		{
			BloodyMurder('Not yet supported for this database type');
		}
	}
	/**
	 * Returns the escaped version of the password, especially safe for CLI
	 * @param $password string
	 * @return string
	 */
	protected function EscapePassword($password)
	{
		return str_replace('$', '\\$', $password);
	}
	function DBDump($file, $compressionLevel = 5)
	{
		$pass = $this->Password;
		if ($this->PasswordEncrypted)
		{
			$encryptionKey = Security::GetEncryptionKeyFromPath();
			$pass = Security::Decrypt($pass, $encryptionKey);
		}

		$user = $this->Username;
		$host = $this->Host;
		$dbName = $this->DatabaseName;
		$port = $this->Port;

		if (PHP_OS === 'Linux')
		{
			$pass = $this->EscapePassword($pass);
			$path = file_exists('/usr/bin/pg_dump93') ? '/usr/bin/pg_dump93' : 'pg_dump';
			$backup = "PGPASSWORD=\"{$pass}\" {$path} -h {$host} -U {$user} -p {$port}";
			$gzip = exec('which gzip 2>&1');
			if (is_executable ($gzip) && $compressionLevel !== 0)
			{
				$file .= '.gz';
				$backup .= " {$dbName} | gzip -c" . $compressionLevel . ' > ' . $file;
			}
			else
			{
				$backup .= " -f {$file} {$dbName}";
				$compress = true;
			}
		}
		else
		{
			if ($this->Type === Data::Postgres)
			{
				$backup = "SET PGPASSWORD={$pass}&& pg_dump -h {$host} -U {$user} -p {$port} -d {$dbName}";
			}
			elseif ($this->Type === Data::MSSQL)
			{
				sqlsrv_configure('WarningsReturnAsErrors', 0);
				$query = <<<SQL
					BACKUP DATABASE "{$dbName}" TO DISK='{$file}';
SQL;
				$this->ExecSQL($query);
				sqlsrv_configure('WarningsReturnAsErrors', 1);
			}

			$gzip = exec('where gzip 2>&1');
			if (is_executable ($gzip) && $compressionLevel !== 0)
			{
				$file .= '.gz';
				$backup .= ' | gzip -c' . $compressionLevel . ' > ' . $file;
			}
			else
			{
				if ($this->Type === Data::Postgres)
				{
					$backup .= " -f {$file}";
					$compress = true;
				}
				elseif ($this->Type === Data::MSSQL)
				{
					$compress = true;
				}
			}
		}

		if ($backup != '')
		{
			exec($backup);
		}

		if (file_exists($file) && $compress && $compressionLevel !== 0)
		{
			$path = pathinfo($file);
			if ($path['extension'] != 'gz')
			{
				$file = File::GzCompress($file, $compressionLevel, true);
			}
		}

		return file_exists($file) ? $file : false;
	}
	function DBDumpMultiple($tarFile, array $connections, $compressionLevel = 5)
	{
		if ($this->Type !== Data::Postgres)
		{
			BloodyMurder('Multiple DB Dumps are only supported for Postgres data connections');
		}

		$info = pathinfo($tarFile);
		$tarFile = "{$info['dirname']}/{$info['filename']}" . '.tar';

		$fileName  = $this->DatabaseName . '_' . date("Ymd") . '_' . '.bak';
		$primaryDump = $this->DBDump($info['dirname'] . DIRECTORY_SEPARATOR . $fileName, 0);

		if ($primaryDump === false)
		{
			BloodyMurder("DB Dump failed for {$this->DatabaseName}");
		}

		$tar = new PharData($tarFile);
		$tar->addFile($primaryDump, $fileName);
		$files = array($primaryDump);

		foreach ($connections as $target)
		{
			if (!($target instanceof DataConnection))
			{
				BloodyMurder('Connections must be DataConnection objects');
			}
			else
			{
				if ($target->Type !== Data::Postgres)
				{
					BloodyMurder('Multiple DB Dumps are only supported for Postgres data connections');
				}
			}

			$fileName = $target->DatabaseName . '_' . date("Ymd") . '_' . '.bak';
			$targetDump = $target->DBDump($info['dirname'] . DIRECTORY_SEPARATOR . $fileName, 0);

			if ($targetDump === false)
			{
				BloodyMurder("DB Dump failed for {$target->DatabaseName}");
			}

			$tar->addFile($targetDump, $fileName);
			$files[] = $targetDump;
		}

		unset($tar); // Removes open file handles held by the PharData object
		foreach ($files as $file)
		{
			unlink($file);
		}

		if ($compressionLevel !== 0)
		{
			$tarFile = File::GzCompress($tarFile, $compressionLevel, true);
		}
		return file_exists($tarFile) ? $tarFile : false;
	}

	/**
	 * Takes in a file and restores the current DB from that file.
	 *
	 * @param string $file Must be a text file dump.
	 */
	function DBRestore($file)
	{
		// Set time to 10 minutes.
		ini_set('max_execution_time', '600');
		ini_set('memory_limit', '1G');

		if ($this->Type !== Data::Postgres)
		{
			BloodyMurder('Restore currently not supported for non Postgres Databases');
		}

		$user = $this->Username;
		$host = $this->Host;
		$dbName = $this->DatabaseName;
		$port = $this->Port;
		$pass = $this->Password;

		if ($this->PasswordEncrypted)
		{
			$encryptionKey = Security::GetEncryptionKeyFromPath();
			$pass = Security::Decrypt($pass, $encryptionKey);
		}
		$tempName = 'restore_db_' . date("YmdHis");

		if (System::IsWindows())
		{
			$createCommand = "SET PGPASSWORD={$pass}&& createdb -h {$host} -U {$user} -p {$port} {$tempName}";
			$restoreCommand = "SET PGPASSWORD={$pass}&& psql -h {$host} -U {$user} -p {$port} -d {$tempName} -f {$file}";
		}
		else
		{
			$pass = $this->EscapePassword($pass);
			$createCommand = "PGPASSWORD=\"{$pass}\" createdb -h {$host} -U {$user} -p {$port} {$tempName}";
			$restoreCommand = "PGPASSWORD=\"{$pass}\" psql -h {$host} -U {$user} -p {$port} -d {$tempName} -f {$file}";
		}

		// Create new DB and Restore file into it.
		System::Execute($createCommand);
		System::Execute($restoreCommand);

		$this->TerminateConnections(true);

		$baseConnection = new DataConnection(
			$this->Type,
			'postgres',
			$this->Username,
			$pass,
			$this->Host,
			$this->Port,
			$this->PasswordEncrypted
		);

		$query = <<<SQL
			DROP DATABASE "{$dbName}";
SQL;
		$baseConnection->ExecSQL($query);
		$baseConnection->RenameDBTo($tempName, $dbName);
	}
	/*
	 * Terminates active db connections.
	 * @param boolean $closeSelf
	 */
	public function TerminateConnections($closeSelf = false)
	{
		if ($this->Type !== Data::Postgres)
		{
			BloodyMurder('Terminate Connections currently not supported for non Postgres Databases');
		}

		$query = <<<SQL
			SELECT datname
			FROM pg_database
			WHERE datname = $1;
SQL;
		$exists = $this->ExecSQL(Data::Assoc, $query, $this->DatabaseName);

		if ($exists[0] !== null)
		{
			$query = <<<SQL
				SELECT pg_terminate_backend(pg_stat_activity.pid)
				FROM pg_stat_activity
				WHERE pg_stat_activity.datname = $1
					AND pid != pg_backend_pid();
SQL;
			$this->ExecSQL($query, $this->DatabaseName);
		}

		if ($closeSelf)
		{
			$this->Close();
		}
	}
	function __wakeup()
	{
		$this->AfterConnectCalled = false;
		$this->ActiveConnection = &Data::$Links->{$this->Name}->ActiveConnection;
	}
	function SendTo(DataConnection $target, $backupPath)
	{
		if (System::IsWindows())
		{
			BloodyMurder('SendTo currently not supported for windows operating system');
		}
		if ($this->Type !== Data::Postgres || $target->Type !== Data::Postgres)
		{
			BloodyMurder('SendTo only supported for Postgres data connections');
		}

		$encryptionKey = false;
		if($this->PasswordEncrypted)
		{
			$encryptionKey = Security::GetEncryptionKeyFromPath();
			$password = Security::Decrypt($this->Password, $encryptionKey);
		}
		else
			$password = $this->Password;

		$password = $this->EscapePassword($password);

		if($target->PasswordEncrypted)
		{
			$encryptionKey = $encryptionKey || Security::GetEncryptionKeyFromPath();
			$targetPassword = Security::Decrypt($target->Password, $encryptionKey);
		}
		else
			$targetPassword = $target->Password;

		$targetPassword = $this->EscapePassword($targetPassword);

		$filename = $target->DatabaseName . '_' . date('Ymdhis');
		$file = $backupPath . '/' . $filename;

		if ($target->Connect() && !$target->DBDump($file))
		{
			throw new Exception('Could not take backup of target database');
		}

		$target->Close();

		$targetPsql = "PGPASSWORD=\"{$targetPassword}\" psql -h {$target->Host} -U {$target->Username} -p {$target->Port}";

		$command = $targetPsql . " -c \"DROP DATABASE IF EXISTS {$target->DatabaseName}_sendtocopy;\"";
		System::Execute($command);

		$command = $targetPsql . " -c \"CREATE DATABASE {$target->DatabaseName}_sendtocopy;\"";
		System::Execute($command);

		$dump = "PGPASSWORD=\"{$password}\" pg_dump -U {$this->Username} -p {$this->Port} {$this->DatabaseName}";
		$dumpTo = "{$targetPsql} {$target->DatabaseName}_sendtocopy";
		$sendTo = "{$dump} | {$dumpTo}";
		System::Execute($sendTo);

		$command = $targetPsql . " -c \"DROP DATABASE IF EXISTS {$target->DatabaseName};\"";
		System::Execute($command);

		$command = $targetPsql . " -c \"ALTER DATABASE {$target->DatabaseName}_sendtocopy RENAME TO {$target->DatabaseName};\"";
		System::Execute($command);

		return true;
	}
	/**
	 * @param DataConnection $target
	 * @param $serverName
	 * @param array $userMappings array additional users to map to this foreign server connections
	 * example:
	 * 		array(
	 * 			array('user' => 'user1', 'password' => 'pass1'),
	 * 			array('user' => 'user2', 'password' => 'pass2')
	 * 		)
	 * NOTE: User associated with this DataConnection is added by default
	 * @param string $host The host of the target DB, but from the point of the view of the source database
	 */
	function CreateServer(DataConnection $target, $serverName, $userMappings = array(), $host = 'localhost')
	{
		if ($this->Type !== Data::Postgres || $target->Type !== Data::Postgres)
		{
			BloodyMurder('Create Server is only supported for Postgres data connections');
		}

		if ($target->PasswordEncrypted)
		{
			$encryptionKey = Security::GetEncryptionKeyFromPath();
			$password = Security::Decrypt($target->Password, $encryptionKey);
		}
		else
		{
			$password = $target->Password;
		}
		// Validate format of additional users array
		foreach ($userMappings as $user)
		{
			if ($user['user'] === null || $user['password'] === null)
			{
				BloodyMurder('Invalid format passed for additional users array');
			}
		}

		$query = <<<SQL
			CREATE EXTENSION IF NOT EXISTS postgres_fdw;
			
			DROP SERVER IF EXISTS {$serverName} CASCADE;
			
			CREATE SERVER {$serverName}
			FOREIGN DATA WRAPPER postgres_fdw
			OPTIONS (host $1, port $2, dbname $3);
SQL;

		if ($host === null)
		{
			$host = $target->Host;
		}
		$this->ExecSQL($query, $host, (string)$target->Port, $target->DatabaseName);

		// Add connection user to mappings array
		array_push($userMappings, array('user' => $target->Username, 'password' => $password));

		foreach ($userMappings as $creds)
		{
			$this->CreateUserMapping($serverName, $creds['user'], $creds['password']);
		}
	}
	function CreateUserMapping($serverName, $user, $pass)
	{
		$query = <<<SQL
			CREATE USER MAPPING FOR {$user}
			SERVER {$serverName}
			OPTIONS (user '{$user}', password '{$pass}');
SQL;

		$this->ExecSQL($query);
	}
	static function EncryptDBPassword($password)
	{
		$encryptionKey = Security::GetEncryptionKeyFromPath();
		return Security::Encrypt($password, $encryptionKey);
	}
	static function ODBCByDSN($dsn, $type, $username = null, $password = null)
	{
		$connection = new DataConnection(
			Data::ODBC,
			null,
			$username,
			$password,
			$dsn,
			null,
			false,
			array('odbc_type' => $type)
		);

		return $connection;
	}
	public function GetODBCType()
	{
		return $this->ODBCType;
	}
	public function ColumnExists($table, $column)
	{
		if ($this->Type !== Data::Postgres)
		{
			BloodyMurder('ColumnExists only currently supported in Postgres');
		}

		$query = <<<SQL
			SELECT column_name 
			FROM information_schema.columns 
			WHERE table_name = $1
				AND column_name = $2;
SQL;
		$results = $this->ExecSQL(Data::Assoc, $query, $table, $column);
		return isset($results[0]['column_name']);
	}
	public function TableExists($tableName)
	{
		$tableNameKey = $this->Type === Data::MSSQL
			? 'TABLE_NAME'
			: 'table_name';

		if ($this->Type === Data::MSSQL)
		{
			$query = <<<SQL
				SELECT * 
				FROM INFORMATION_SCHEMA.TABLES 
				WHERE TABLE_SCHEMA = 'dbo' 
				AND TABLE_NAME = $1
SQL;
		}
		elseif ($this->Type === Data::Postgres)
		{
			$query = <<<SQL
				SELECT table_name
				FROM information_schema.tables
				WHERE table_name = $1
SQL;
		}
		else
		{
			BloodyMurder('TableExists only currently supported in Postgres and MSSQL');
		}

		$results = $this->ExecSQL(Data::Assoc, $query, $tableName);

		return isset($results[0][$tableNameKey]);
	}
	public function DatabaseExists($dbName)
	{
		if ($this->Type === Data::Postgres)
		{
			$query = <<<SQL
				SELECT datname
				FROM pg_catalog.pg_database
				WHERE datname = $1;
SQL;
			$results = $this->ExecSQL(Data::Assoc, $query, $dbName);
			return isset($results[0]);
		}
		elseif ($this->Type === Data::MSSQL)
		{
			$query = <<<SQL
				SELECT "name"
				FROM sys.databases
				WHERE "name" = $1;
SQL;
			$results = $this->ExecSQL(Data::Assoc, $query, $dbName);
			return isset($results[0]);
		}
		else
		{
			BloodyMurder('DatabaseExists is not supported for this connection type');
		}
	}
	public function UserMappingExists($foreignServer, $user)
	{
		$query = <<<SQL
			SELECT *
			FROM information_schema.user_mappings
			WHERE foreign_server_name = '{$foreignServer}'
				AND authorization_identifier = '{$user}';
SQL;

		$result = $this->ExecSQL($query);

		return $result->Count > 0;
	}
	/**
	 * Creates SQL statement from array of data
	 * @param array $data
	 * @return string
	 */
	public function CreateDataArraySQL(array $data)
	{
		if ($this->Type !== Data::Postgres)
		{
			BloodyMurder('CreateArraySQL only supports Postgres data connections');
		}

		if (empty($data))
		{
			$query = <<<SQL
				SELECT NULL AS id WHERE FALSE
SQL;

		}
		else
		{
			$json = json_encode($data);
			$definition = static::CreateDataArraySQLDefinition(array_keys($data[0]));
			$query = <<<SQL
				SELECT x.* 
				FROM jsonb_to_recordset(\$\${$json}\$\$)
				AS x({$definition})
SQL;
		}

		return $query;
	}
	/**
	 * Creates SQL command from array of data
	 * @param $data
	 * @return DataCommand
	 */
	public function CreateDataArrayCommand(array $data)
	{
		$query = $this->CreateDataArraySQL($data);
		return $this->CreateCommand(Data::SQL, Data::Assoc, $query);
	}
	/**
	 * Creates a column definition for array of columns
	 * NOTE: Uses flexible TEXT datatype for all fields
	 * @param array $columns
	 * @return string
	 */
	protected static function CreateDataArraySQLDefinition(array $columns)
	{
		$cols = array();
		foreach ($columns as $col)
		{
			$cols[] = "{$col} TEXT";
		}
		return implode(', ', $cols);
	}

	/**
	 * Renames Database
	 * @param string $dbName
	 * @param string $renameDb
	 */
	public function RenameDBTo($dbName, $renameDb)
	{
		if ($this->Type === Data::MySQL)
		{
			$dbName = mysql_real_escape_string($dbName);
			$renameDb = mysql_real_escape_string($renameDb);

			$query = <<<SQL
				ALTER DATABASE "{$dbName}" MODIFY NAME = "{$renameDb}";
SQL;
			$this->ExecSQL(Data::Assoc, $query);
		}
		elseif ($this->Type === Data::Postgres)
		{
			$dbName = pg_escape_string($dbName);
			$renameDb = pg_escape_string($renameDb);

			$query = <<<SQL
				ALTER DATABASE "{$dbName}" RENAME TO "{$renameDb}";
SQL;
			$this->ExecSQL(Data::Assoc, $query);
		}
		else
		{
			BloodyMurder('Database Type Currently Not Supported');
		}
	}

	/**
	 * Checks whether a passed in candidate is possi
	 * @param $candidate
	 * @return boolean
	 */
	protected function IsActiveConnection($candidate)
	{
		if ($this->Type === Data::Postgres && PHP_VERSION_ID >= 80100)
		{
			return ($candidate instanceof PgSql\Connection);
		}

		return is_resource($candidate);
	}

	/**
	 * @ignore
	 */
	static function CloseAll($forceCommit = false)
	{
		if (isset($_SESSION['_NDataLinks']))
		{
			foreach ($_SESSION['_NDataLinks'] as $connection)
			{
				if ($forceCommit)
				{
					$connection->ForceCommit();
				}

				$connection->Close();
			}
		}
	}

	function __serialize()
	{
		$this->ActiveConnection = null;
		return (array)$this;
	}
}
?>
