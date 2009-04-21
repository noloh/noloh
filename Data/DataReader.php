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
	public $Data;
	public $ResultType;
	
	/**
	 * @param mixed Data::Postgres|Data::MySQL|Data::MSSQL|Data::ODBC $type The type of the database.
	 * @param resource $resource A resource representing the data returned from the database.
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Determines how your data columns are indexed .
	 * @param $callBack
	 */
	function DataReader($type, $resource, $resultType=Data::Assoc, $callBack=null)
	{
		$this->ResultType = $type;
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
				if($callBack)
				{
					if(isset($callBack['id']))
							$object = GetComponentById($callBack['id']);
						else
							$object = $callBack['class'];
					$callArray = array($object, $callBack['function']);
				}
				for ($i=0; $i < $numRows; ++$i)
				{
					if(isset($callBack['constraint']))
					{
						$intersection = array();
						$data = pg_fetch_array($resource, $i, $resultType);
						$columns = $callBack['constraint']->GetColumns();
						$count = count($columns);
						for($columnIndex=0; $columnIndex < $count; ++$columnIndex)
						{
							//$local = $keys[$i];
							if(isset($data[$columns[$columnIndex]]))
								$intersection[$columns[$columnIndex]] = $data[$columns[$columnIndex]];
						}
						$this->Data[] = $intersection;
//						$this->Data[] = array_intersect_key(pg_fetch_array($resource, $i, $resultType), array_flip($callBack['constraint']->GetColumns()));
					
					}
					else
						$this->Data[] = pg_fetch_array($resource, $i, $resultType);
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
		elseif($type == Data::MySQL)
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
				$this->Data[] = $row;
		}
		elseif($type == Data::MSSQL)
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
					$data[$count][] = $row;
			}while (mssql_next_result($resource));
			mssql_free_result($resource);
			
			if($count > 0)
				$this->Data = $data;
			else
				$this->Data = $data[0];		
//			$numRows = mssql_num_rows($resource);
//			for ($i=0; $i < $numRows; ++$i)
//				$this->Data[] =  mssql_fetch_array($resource, $resultType);
		}
		if(!$this->Data)
			$this->Data = array();
	}
	function Count()
	{
		return count($this->Data);
	}
	/**
	 * The number of rows in your dataset.
	 * 
	 * @return integer;
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
		reset($this->Data);
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