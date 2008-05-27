<?php
/**
 * @package Data
 */
/**
 * DataConnection class
 *
 * This class needs a description...
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
	
	function DataConnection($type = Data::Postgres, $databaseName='',  $username='', $host='localhost', $port='5432', $password='')
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
		$tmpConnectString = "dbname = $this->DatabaseName user=$this->Username host = $this->Host port = $this->Port password = $this->Password";
		if($this->Type == Data::Postgres)
			$this->ActiveConnection = pg_connect($tmpConnectString);
		elseif($this->Type == Data::MySQL)
			$this->ActiveConnection = mysql_connect($tmpConnectString);
		return $this->ActiveConnection;
	}
	function Close()
	{
		if($this->Type == Data::Postgres)
			$status = pg_close($this->ActiveConnection);
		elseif($this->Type == Data::MySQL)
			$status = mysql_close($this->ActiveConnection);
		return $status;
	}
	function SetType($type)	{$this->Type = $type;}
	function GetType()		{return $this->Type;}
	
	//Database Query Helper Functions
	private function GenerateSqlString($spName, $paramsArray)
	{
		if($spName == null)
			return null;
		$query = 'SELECT * FROM '. $spName . '(';
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
			for($i = 0; $i < $numArgs; ++$i)
			{
				$tmpArg = self::ConvertTypeToMySQL($paramsArray[$i]);
				$query .= $tmpArg . ",";		
			}
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
	private static function ConvertTypeToMySQL($value, $quote="'")
	{
		if(is_string($value))
		{
			$value = mysql_real_escape_string($value);
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
		$dbCmd->Connection->Close();
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
		$dbCmd->Connection->Close();
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
		$dbCmd->Connection->Close();
		
		return $tmpReturn;
	}
	function CreateCommand($spName, $paramsDotDotDot)
	{
		$args = func_get_args();
		$query = self::GenerateSqlString($spName, array_slice($args, 2));
		$dbCmd = new PGSqlCommand($this, $query);
		return $dbCmd;
	}
}
?>