<?php
/**
 * DataConnection class
 *
 * This class needs a description...
 * 
 * @package Data
 */
class DataConnection extends Object
{
	public $Username;
	public $DatabaseName;
	public $Password;
	public $Host;
	public $Port;
	public $ActiveConnection;
	private $Type;
	
	function DataConnection($type = Data::Postgres, $databaseName='',  $username='', $password='', $host='localhost', $port='5432')
	{
		$this->Username = $username;
		$this->DatabaseName = $databaseName;
		$this->Host = $host;
		$this->Port = $port;
		$this->Password = $password;
		$this->Type = $type;
	}
	function Connect()
	{
		if($this->Type == Data::Postgres)
		{
			if(!is_resource($this->ActiveConnection) || pg_connection_status($this->ActiveConnection) === PGSQL_CONNECTION_BAD)
			{
				$tmpConnectString = 'dbname = ' . $this->DatabaseName . ' user='.$this->Username .' host = '.$this->Host. ' port = '. $this->Port .' password = ' .$this->Password;
				$this->ActiveConnection = pg_connect($tmpConnectString);
			}
		}
		elseif($this->Type == Data::MySQL)
		{
			$this->ActiveConnection = mysql_connect($this->Host, $this->Username, $this->Password);
			mysql_select_db($this->DatabaseName, $this->ActiveConnection);
		}
		return $this->ActiveConnection;
	}
	function Close()
	{
		if(is_resource($this->ActiveConnection))
			if($this->Type == Data::Postgres)
				return pg_close($this->ActiveConnection);
			elseif($this->Type == Data::MySQL)
				return mysql_close($this->ActiveConnection);
		return false;
	}
	function SetType($type)	{$this->Type = $type;}
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
	private static function ConvertTypeToPostgres($value, $quote="'")
	{
		if(is_string($value))
		{
			$value = pg_escape_string($value);
			$tmpArg = "$quote" . $value ."$quote";
		}
		elseif(is_int($value))
			$tmpArg = (int)$value;
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
	function ExecFunction($spName, $paramsDotDotDot = null)
	{
		$args = func_get_args();
		if($hasResultOption = is_int($spName))
		{
			$resultOption = $spName;
			$spName = $args[1];
		}
		$query = self::GenerateSqlString($spName, array_slice($args, $hasResultOption?2:1));
		$dbCmd = new DataCommand($this, $query);
		$tmpReturn = $dbCmd->Execute($hasResultOption?$resultOption:Data::Both);
//		$dbCmd->Connection->Close();
		return $tmpReturn;
	}
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
	function CreateCommand($spName, $paramsDotDotDot)
	{
		$args = func_get_args();
		$query = self::GenerateSqlString($spName, array_slice($args, 2));
		$dbCmd = new DataCommand($this, $query);
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