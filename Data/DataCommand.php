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
	public $ResultType;
	/**
	 * Constructor of the DataCommand class
	 * 
	 * @param DataConnection $connection A DataConnection object that has your database connection information.
	 * @param string $sql The SQL statement you wish to execute.
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Optional: The format of the data column indices returned by the function.
	 */
	function DataCommand($connection = null, $sql = '', $resultType = Data::Both)
	{
		$this->Connection = $connection;
		$this->SqlStatement = $sql;
		if($resultType)
			$this->ResultType = $resultType;
	}
	function GetCallback()				{return $this->Callback;}
	function GetConnection()			{return $this->Connection;}
	function SetConnection($connection)	{$this->Connection = $connection;}
	/**
	 * @return string Returns the SQL statement that this command will execute.
	 */
	function GetSQL()					{return $this->SqlStatement;}
	/**
	 * Sets the SQL staetment that this command will execute.
	 * @param string @sql The SQL statement that this command will execute.
	 */
	function SetSQL($sql)				{$this->SqlStatement = $sql;}
	/**
	 * @deprecated use GetSQL() instead.
	 * 
	 * @return string Returns the SQL statement that this command will execute.
	 */
	function GetSqlStatement()			{return $this->SqlStatement;}
	/**
	 * @deprecated use SetSQL() instead.
	 * @param string @sql Sets The SQL statement that this command will execute.
	 */
	function SetSqlStatement($sql)		{$this->SqlStatement = $sql;}
	/**
	 *	Executes your SQL statement aginst the database whose connection information
	 *  is stored in this class's Connection property.
	 *
	 *	@return mixed If command executes successfully it returns a DataReader object 
	 *	containing the resulting data of the query. Otherwise the function will return false.
	 */
	function Execute($resultType = null)
	{
		if($this->Connection != null && $this->SqlStatement != null)
		{
			$type = $this->Connection->GetType();
			if($type == Data::Postgres)
				$resource = pg_query($this->Connection->Connect(), $this->SqlStatement);
			elseif($type == Data::MySQL)
			{
				$resource = $this->Connection->Connect();
				$resource = mysql_query($this->SqlStatement, $resource);
			}
			elseif($type == Data::MSSQL)
				$resource = mssql_query($this->SqlStatement, $this->Connection->Connect());
				
			if(!$resource)
			{
				if($type == Data::Postgres)
					BloodyMurder(pg_last_error());
				elseif($type == Data::MySQL)
					BloodyMurder(mysql_error());
				elseif($type == Data::MSSQL)
					BloodyMurder(mssql_get_last_message);
				return false;
			}
			$resultType = $resultType?$resultType:$this->ResultType;
			return new DataReader($type, $resource, $resultType, $this->Callback);
		}
		return false;
	}
	function ReplaceParam($index, $value)
	{
		$sql = $this->SqlStatement;
		if ($this->Connection && preg_match('/^.*?\((.+?)\);\s*$/i', $sql, $matches)) 
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