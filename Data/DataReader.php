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
	public $Data;
	/**
	 * Determines how your data columns are indexed
	 * @var Data::Assoc|Data::Numeric|Data::Both 
	 */
	public $ResultType;
	
	/**
	 * Constructor
	 * Be sure to call this from the constructor of any class that extends DataReader.
	 * @param mixed Data::Postgres|Data::MySQL|Data::MSSQL|Data::ODBC $type The type of the database.
	 * @param resource $resource A resource representing the data returned from the database.
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Determines how your data columns are indexed .
	 * @param ServerEvent $callBack
	 */
	function DataReader($type, $resource, $resultType=Data::Assoc, $callBack=null)
	{
		$this->ResultType = $type;
		if($callBack)
		{
			$object = isset($callBack['id'])?GetComponentById($callBack['id']): $callBack['class'];
			$callArray = array($object, $callBack['function']);
		}
		if($type == Data::Postgres)
		{
			if($resultType == Data::Both)
				$resultType = PGSQL_BOTH;
			elseif($resultType == Data::Assoc)
				$resultType = PGSQL_ASSOC;
			else
				$resultType = PGSQL_NUM;	
				
			if($resultType == PGSQL_BOTH || $resultType == PGSQL_NUM || $callBack)
			{
				$this->Data = array();
				$numRows = pg_numrows($resource);
				for ($i=0; $i < $numRows; ++$i)
				{
					$data = pg_fetch_array($resource, $i, $resultType);
					if(isset($callBack['constraint']))
						$data = self::HandleConstraint($data, $callBack['constraint']);
					
					$this->Data[] = $data;
					
					if($callBack)
					{
						$args = array_merge(array($this->Data[$i]), $callBack['params']);
						call_user_func_array($callArray, $args);
					}
				}
			}
			else
				$this->Data = pg_fetch_all($resource);	
		}
		elseif($type == Data::MySQL && is_resource($resource))
		{
			if($resultType == Data::Both)
				$resultType = MYSQL_BOTH;
			elseif($resultType == Data::Assoc)
				$resultType = MYSQL_ASSOC;
			else
				$resultType = MYSQL_NUM;
		
//			$numRows = mysql_num_rows($resource);
//			for ($i=0; $i < $numRows; ++$i)
//				$this->Data[] =  mysql_fetch_array($resource, $resultType);
			while($row = mysql_fetch_array($resource, $resultType))
			{
				if(isset($callBack['constraint']))
					$row = self::HandleConstraint($row, $callBack['constraint']);
				
				$this->Data[] = $row;	
				if($callBack)
				{
					$args = array_merge(array($row), $callBack['params']);
					call_user_func_array($callArray, $args);
				}
			}
		}
		elseif($type == Data::MSSQL && $resource !== true)
		{
			if($resultType == Data::Both)
				$resultType = MSSQL_BOTH;
			elseif($resultType == Data::Assoc)
				$resultType = MSSQL_ASSOC;
			else
				$resultType = MSSQL_NUM;
		
			$count = -1;
			$data = array();
			do
			{
				++$count;
				$data[$count] = array();
				while($row = mssql_fetch_array($resource, $resultType))
				{
					if(isset($callBack['constraint']))
						$row = self::HandleConstraint($row, $callBack['constraint']);
					$data[$count][] = $row;
					if($callBack)
					{
						$args = array_merge(array($row), $callBack['params']);
						call_user_func_array($callArray, $args);
					}	
				}
			}while (mssql_next_result($resource));
			mssql_free_result($resource);
			
			$this->Data = ($count > 0)?$data:$data[0];
		}
		if(!$this->Data)
			$this->Data = array();
	}
	private static function HandleConstraint($data, $constraint)
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
		return null;	
	}
	/**
	 * @ignore
	 */
	function offsetUnset($index)
	{
		return null;
//		if(is_numeric($index))
//			array_splice($this->Data, $index, 1);
	}
}
?>