<?php
/**
 * DataReader class
 *
 * This class needs a description...
 * 
 * @package Data
 */
class DataReader extends Object implements ArrayAccess, Countable, Iterator
{
	public $Data;
	public $ResultType;
	
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
		
			$numRows = mysql_num_rows($resource);
			for ($i=0; $i < $numRows; ++$i)
				$this->Data[] =  mysql_fetch_array($resource, $resultType);
		}
		if(!$this->Data)
			$this->Data = array();
	}
	function Count()
	{
		return count($this->Data);
	}
	/**
	 * @ignore
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