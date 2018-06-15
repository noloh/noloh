<?php
/**
 * DataReader class
 *
 * A DataReader object is used to store data retrieved from a database. A DataReader object
 * can also be treated as if it were an array in many circumstances. DataReaders are usually
 * used in conjuction with Data::$Links and rarely need to be instantiated on their own.
 * 
 * <pre>
 *     $data = Data::$Links->Database1->ExecView('v_get_all_users');
 *     //In the above line Data::$Links returns a DataReader object containing
 *     //the resulting data from our query
 *     //We can now access the information container in our $data DataReader as though
 *     //it were an array.
 *     foreach($data as $user)
 *         foreach($user as $field => $value)
 *             System::Log("The value of $field  is $value);
 *             
 *     //Furthermore, if we just wanted a specific column of a specific row we can access
 *     //it in the following way:
 *     $name = $user[10]['username'];
 *     //Assuming the $data result has a username column and an 11th row, our $name
 *     //variable would now contain the corresponding username.
 * </pre>
 *
 * 
 * @package Data
 */
class DataReader extends Object implements ArrayAccess, Countable, Iterator
{
	/**
	 * An array containing the results of your DataCommand
	 * @var array 
	 */
	protected $Data;
	protected $Type;
	/**
	 * Determines how your data columns are indexed
	 * @var Data::Assoc|Data::Numeric|Data::Both 
	 */
	public $ResultType;
	private $ColumnTypes;
	protected $CallBack;
	protected $Resource;
	
	/**
	 * Constructor
	 * Be sure to call this from the constructor of any class that extends DataReader.
	 * @param mixed Data::Postgres|Data::MySQL|Data::MSSQL|Data::ODBC $type The type of the database.
	 * @param resource $resource A resource representing the data returned from the database.
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Determines how your data columns are indexed.
	 * @param ServerEvent $callBack
	 * @param Boolean $convertType Whether to convert returned data into their native PHP equivalents, instead of strings.
	 */
	function DataReader($type, $resource, $resultType=Data::Assoc, $callBack=null, $convertType=false)
	{
		$this->Type = $type;
		$this->ResultType = $resultType;
		$this->Resource = $resource;
		$this->CallBack = $callBack;
		
		$this->Data = $this->ReadData();
	}
	public function GetData()
	{
		return $this->Data;
	}
	protected function ReadData()
	{
		$type = $this->Type;
		$resultType = $this->ResultType;
		$resource = $this->Resource;
		$callBack = $this->CallBack;
		
		if ($callBack)
		{
			$object = isset($callBack['id']) ? GetComponentById($callBack['id']) : $callBack['class'];
			$callArray = array($object, $callBack['function']);
		}
		
		if ($type == Data::Postgres)
		{
			if ($resultType == Data::Both)
			{
				$resultType = PGSQL_BOTH;
			}
			elseif ($resultType == Data::Assoc)
			{
				$resultType = PGSQL_ASSOC;
			}
			else
			{
				$resultType = PGSQL_NUM;
			}

			if ($resultType == PGSQL_BOTH || $resultType == PGSQL_NUM || $callBack)
			{
				$rows = array();
				$numRows = pg_numrows($resource);
				for ($i = 0; $i < $numRows; ++$i)
				{
					$data = pg_fetch_array($resource, $i, $resultType);
					//$data = self::ConvertType($type, $resource);
					if (isset($callBack['constraint']))
					{
						$data = self::HandleConstraint($data, $callBack['constraint']);
					}

					$rows[] = $data;

					if ($callBack)
					{
						$args = array_merge(array($rows[$i]), $callBack['params']);
						call_user_func_array($callArray, $args);
					}
				}
			}
			elseif ($resource)
			{
				$rows = pg_fetch_all($resource);
			}
			else
			{
				throw new SqlException('Resource not available');
			}
		}
		elseif ($type == Data::MySQL && is_resource($resource))
		{
			if ($resultType == Data::Both)
			{
				$resultType = MYSQL_BOTH;
			}
			elseif ($resultType == Data::Assoc)
			{
				$resultType = MYSQL_ASSOC;
			}
			else
			{
				$resultType = MYSQL_NUM;
			}

			//			$numRows = mysql_num_rows($resource);
			//			for ($i=0; $i < $numRows; ++$i)
			//				$rows[] =  mysql_fetch_array($resource, $resultType);
			while ($row = mysql_fetch_array($resource, $resultType))
			{
				if (isset($callBack['constraint']))
				{
					$row = self::HandleConstraint($row, $callBack['constraint']);
				}

				$rows[] = $row;
				if ($callBack)
				{
					$args = array_merge(array($row), $callBack['params']);
					call_user_func_array($callArray, $args);
				}
			}
		}
		elseif ($type == Data::MSSQL && $resource !== true)
		{
			if (function_exists('sqlsrv_fetch_array'))
			{
				if ($resultType == Data::Both)
				{
					$resultType = SQLSRV_FETCH_BOTH;
				}
				elseif ($resultType == Data::Assoc)
				{
					$resultType = SQLSRV_FETCH_ASSOC;
				}
				else
				{
					$resultType = SQLSRV_FETCH_NUMERIC;
				}
				
				$fetch = 'sqlsrv_fetch_array';
				$next = 'sqlsrv_next_result';
				$free = 'sqlsrv_free_stmt';
			}
			else
			{
				if ($resultType == Data::Both)
				{
					$resultType = MSSQL_BOTH;
				}
				elseif ($resultType == Data::Assoc)
				{
					$resultType = MSSQL_ASSOC;
				}
				else
				{
					$resultType = MSSQL_NUM;
				}
				
				$fetch = 'mssql_fetch_array';
				$next = 'mssql_next_result';
				$free = 'mssql_free_result';
			}

			$count = -1;
			$data = array();
			do
			{
				++$count;
				$data[$count] = array();
				while ($row = $fetch($resource, $resultType))
				{
					if (isset($callBack['constraint']))
					{
						$row = self::HandleConstraint($row, $callBack['constraint']);
					}
					$data[$count][] = $row;
					if ($callBack)
					{
						$args = array_merge(array($row), $callBack['params']);
						call_user_func_array($callArray, $args);
					}
				}
			} while ($next($resource));
			$free($resource);

			$rows = ($count > 0) ? $data : $data[0];
		}
		elseif (Data::ODBC)
		{
			while($row = odbc_fetch_array($resource))
			{
				if (isset($callBack['constraint']))
				{
					$row = self::HandleConstraint($row, $callBack['constraint']);
				}

				$rows[] = $row;
				if ($callBack)
				{
					$args = array_merge(array($row), $callBack['params']);
					call_user_func_array($callArray, $args);
				}
			}
		}
		
		if (!$rows)
		{
			$rows = array();
		}
		
		return $rows;
	}
	protected function DetermineColumnTypes($dbType, $resource)
	{
		$this->ColumnTypes = array();
		if($dbType == Data::Postgres)
		{
			$numCols = pg_num_fields($resource);
			for($i=0; $i < $numCols; ++$i)
				$this->ColumnTypes[] = pg_field_type($resource, $i);
		}
		elseif($dbType == Data::MySQL)
		{
			$numCols = mysql_num_fields($resource);
			for($i=0; $i < $numCols; ++$i)
				$this->ColumnTypes[] = mysql_field_type($resource, $i);
		}
		elseif($dbType == Data::MSSQL)
		{
			if (function_exists('sqlsrv_num_fields'))
			{
				$numCols = sqlsrv_num_fields($resource);
				for($i=0; $i < $numCols; ++$i)
					$this->ColumnTypes[] = sqlsrv_get_field($resource, $i);
			}
			else
			{
				$numCols = mssql_num_fields($resource);
				for($i=0; $i < $numCols; ++$i)
					$this->ColumnTypes[] = mssql_field_type($resource, $i);
			}
		}
		elseif ($dbType === Data::ODBC)
		{
			$numCols = odbc_num_fields($resource);
			for ($i = 1; $i <= $numCols; ++$i)
			{
				$this->ColumnTypes[] = odbc_field_type($resource, $i);
			}
		}
	}
	private function ConvertType($dbType, $data)
	{
		
	}
	protected static function HandleConstraint($data, $constraint)
	{
		//$intersection = array();
		$columns = $constraint->GetColumns();
		/*$count = count($columns);
		for($columnIndex=0; $columnIndex < $count; ++$columnIndex)
		{
			//$local = $keys[$i];
			if(isset($data[$columns[$columnIndex]]))
				$intersection[$columns[$columnIndex]] = $data[$columns[$columnIndex]];
		}*/
//		System::Log($columns, $data);
//		$intersection = array_intersect_key($data, array_flip($columns));
		return array_intersect_key($data, array_flip($columns));	
	}
/*	/**
	* 
	* 
	
	function GetResource()	{return $this->Resource;}*/
	/**
	 * @ignore
	 */
	function Count()
	{
		return count($this->Data);
	}
	/**
	 * The number of rows in your dataset.
	 * 
	 * @return integer
	 */
	function GetCount()
	{
		return count($this->Data);
	}
	/**
	 * @ignore
	 */
	public function rewind() 
	{
		return reset($this->Data);
	}
	/**
	 * @ignore
	 */
	public function current() 
	{
		return current($this->Data);
	}
	/**
	 * @ignore
	 */
	public function key() 
	{
		return key($this->Data);
	}
	/**
	 * @ignore
	 */
	public function next() 
	{
		return next($this->Data);
	}
	/**
	 * @ignore
	 */
	public function valid() 
	{
		return $this->current() !== false;
	}
	/**
	 * @ignore
	 */
	function offsetExists($key)
	{
		return(array_key_exists($key, $this->Data));
	}
	/**
	 * @ignore
	 */
	function offsetGet($index)
	{
		return $this->Data[$index];
	}
	/**
	 * @ignore
	 */
	function offsetSet($index, $val)
	{	
		BloodyMurder('offsetSet is not allowed for DataReader');
	}
	/**
	 * @ignore
	 */
	function offsetUnset($index)
	{
		BloodyMurder('offsetUnset is not allowed for DataReader');
	}

	public function __isset($name)
	{
		if ($name === 'Data')
		{
			return $this->Count > 0;
		}
		else
		{
			return false;
		}
	}
}
?>