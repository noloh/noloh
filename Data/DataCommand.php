<?php
/**
 * DataCommand class
 *
 * A DataCommand object stores and executes SQL queries that can be executed against a database. 
 * In most cases DataCommand's are generated automatically through Data::$Links.
 * 
 * The following is an example of manually creating and executing a DataCommand:
 * <pre>
 *	   $connection = new DataConnection(Data::Postgres, 'new_york_people');
 *     $command = new DataCommand($connection, 'SELECT * FROM people');
 *     $people = $command->Execute();
 * </pre>
 * 
 * A more likely implementation of DataCommand that's transparent to the develper:
 * We assume that the People Data::$Link was alredy defined previously in our program.
 * <pre>
 * 	   $people = Data::$Links->People->ExecSQL('SELECT * FROM people');
 * </pre>
 * 
 * Please see the article on Data::$Links for more information.
 * 
 * @package Data
 */
class DataCommand extends Object
{
	private $Connection;
	private $SqlStatement;
	private $Callback;
	/**
	 * The format of the data column indices returned by the function.
	 * @var Data::Assoc|Data::Numeric|Data::Both
	 */ 
	public $ResultType;
	/**
	 * Constructor of the DataCommand class
	 * 
	 * @param DataConnection $connection A DataConnection object that has your database connection information.
	 * @param string $sql The SQL statement you wish to execute.
	 * @param Data::Assoc|Data::Numeric|Data::Both $resultType Optional: The format of the data column indices returned by the function.
	 */
	function DataCommand($connection = null, $sql = '', $resultType = Data::Both)
	{
		$this->Connection = $connection;
		$this->SqlStatement = $sql;
		if($resultType)
			$this->ResultType = $resultType;
	}
	/**
	 * @ignore
	 */
	function GetCallback()				{return $this->Callback;}
	/**
	 * Gets the DataConnection used when executing the command's statement.
	 * @return string 
	 */
	function GetConnection()			{return $this->Connection;}
	/**
	 * Sets the DataConnection used when executing the command's statement.
	 * @param DataConnection $connection
	 */
	function SetConnection($connection)	{$this->Connection = $connection;}
	/**
	 * Returns the SQL statement that this command will execute.
	 * @return string 
	 */
	function GetSQL()					{return $this->SqlStatement;}
	/**
	 * Sets the SQL statement that this command will execute.
	 * @param string $sql The SQL statement that this command will execute.
	 */
	function SetSQL($sql)				{$this->SqlStatement = $sql;}
	/**
	 * Returns the SQL statement that this command will execute.
	 * @deprecated use GetSQL() instead.
	 * @return string 
	 */
	function GetSqlStatement()			{return $this->SqlStatement;}
	/**
	 * Sets the SQL statement that this command will execute.
	 * @deprecated use SetSQL() instead.
	 * @param string $sql 
	 */
	function SetSqlStatement($sql)		{$this->SqlStatement = $sql;}
	/**
	 *	Executes your SQL statement aginst the database whose connection information
	 *  is stored in this class's Connection property.
	 *  
	 *  If command executes successfully it returns a DataReader object 
	 *	containing the resulting data of the query. Otherwise the function will return false.
	 *	@param Data::Assoc|Data::Numeric|Data::Both $resultType The format of the data column indices returned by the function.
	 *	@return mixed 
	 */
	function Execute($resultType = null)
	{
		if($this->Connection != null && $this->SqlStatement != null)
		{
			System::BeginBenchmarking();
			$type = $this->Connection->GetType();
			$connection = $this->Connection->Connect();
			if($type == Data::Postgres)
				$resource = @pg_query($connection, $this->SqlStatement);
			elseif($type == Data::MySQL)
				$resource = mysql_query($this->SqlStatement, $connection);
			elseif($type == Data::MSSQL)
				if (function_exists('sqlsrv_query'))
					$resource = sqlsrv_query($connection, $this->SqlStatement);
				else
					$resource = mssql_query($this->SqlStatement, $connection);
				
			if(!$resource)
			{
				$this->Connection->ErrorOut($connection, $this->SqlStatement);
				return false;
			}
			$resultType = $resultType?$resultType:$this->ResultType;
			
			if ($type === Data::Postgres)
			{
				$reader = new DataReaderIterator($resource, $resultType, $this->Callback);
			}
			else
			{
				$reader = new DataReader($type, $resource, $resultType, $this->Callback);
			}

			Application::$RequestDetails['total_database_time'] += System::Benchmark();
			return $reader;
		}
		return false;
	}
	/**
	 * Replaces the parameter to your database function specified by the index
	 * 
	 * <pre>
	 * $command = Data::$Links->MyDb->CreateCommand('sp_get_people', 'Johnny', '10065');
	 * $results1 = $command->Execute();
	 * //Now we can replace the zipcode param
	 * $command->ReplaceParam(-1, '11219');
	 * $results2 = $command->Execute();
	 * </pre>
	 * @param integer $index The number of the parameter, negative numbers denote distance from end.
	 * @param mixed $value
	 */
	function ReplaceParam($index, $value)
	{
		$sql = $this->SqlStatement;
		if ($this->Connection && preg_match('/^.*?\((.+?)\);?\s*$/i', $sql, $matches)) 
		{
			//preg_match_all('/\'[^\']*\'|[^,]+/i', $matches[1], $result, PREG_PATTERN_ORDER);
			// Phill: I think there was a \' problem in the above RegEx. I have changed to what I think is the solution. Didn't really test.
			//preg_match_all('/\'(?:[^\'\\]|\\.)*?\'|[^,]+/i', $matches[1], $result, PREG_PATTERN_ORDER);
			/* Your solution doesn't work, and broke my code, I don't know why you think there's a \' problem. This takes in sql that has to
			 have quotes escaped in a particular way. So I'm reinstating the above code*/
			preg_match_all('/\'[^\']*\'|[^,]+/i', $matches[1], $result, PREG_PATTERN_ORDER);
			$params = $result[0];
			$count = count($params);
			$replacement = '';
			for($i=0; $i < $count; ++$i)
			{
				if($i == $index || ($index < 0 && ($i - $count) == $index))
					$param = $this->Connection->ConvertValueToSQL($value);
				else
					$param = $params[$i];
				$replacement .= $param . ',';	
			}
			$replacement = rtrim($replacement, ',');
			
			$query = str_replace($matches[1], $replacement, $sql);
			
			$this->SetSqlStatement($query);
		}
	}
	/**
	 * @ignore
	 */
    function Callback($object, $functionName, $paramsAsDotDotDot = null)
    {
    	$offset = 0;
    	$args = func_get_args();
    	if($args[0] instanceof DataConstraint)
    		$offset = 1;
    		
		if(is_string($args[$offset]))
		{
			$key = 'class';
			$object = $args[$offset];
		}
		else
		{
			$key = 'id';
			$object = $args[$offset]->Id;
		}
		$callBack = array($key => $object, 'function' =>$args[$offset + 1], 'params' => array_slice($args, $offset + 2));
		if($offset == 1)
			$callBack['constraint'] = $args[0];
		$this->Callback = $callBack;
    }
}
?>