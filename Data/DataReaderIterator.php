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
class DataReaderIterator extends Object implements ArrayAccess, Countable, Iterator
{
	/**
	 * Determines how your data columns are indexed
	 * @var Data::Assoc|Data::Numeric|Data::Both 
	 */
	public $ResultType;
	private $Resource;
	private $CallBack;
	private $Count;
	private $Index = 0;
	
	/**
	 * Constructor
	 * Be sure to call this from the constructor of any class that extends DataReader.
	 * @param resource $resource A resource representing the data returned from the database.
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Determines how your data columns are indexed.
	 * @param ServerEvent $callBack
	 * @param Boolean $convertType Whether to convert returned data into their native PHP equivalents, instead of strings.
	 */
	function DataReaderIterator($resource, $resultType = Data::Assoc, $callBack=null, $convertType=false)
	{
		$this->ResultType = $resultType;
		$this->Resource = $resource;
		$this->CallBack = $callBack;

		$this->Count = pg_numrows($this->Resource);
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
	public function GetData()
	{
		if ($this->CallBack)
		{
			$object = isset($this->CallBack['id'])?GetComponentById($this->CallBack['id']): $this->CallBack['class'];
			$callArray = array($object, $this->CallBack['function']);
		}
		
		if ($this->ResultType == Data::Both)
		{
			$this->ResultType = PGSQL_BOTH;
		}
		elseif ($this->ResultType == Data::Assoc)
		{
			$this->ResultType = PGSQL_ASSOC;
		}
		else
		{
			$this->ResultType = PGSQL_NUM;
		}

		if ($this->ResultType == PGSQL_BOTH || $this->ResultType == PGSQL_NUM || $this->CallBack)
		{
			$rows = array();
			for ($i = 0; $i < $this->Count; ++$i)
			{
				$data = pg_fetch_array($this->Resource, $i, $this->ResultType);
				if (isset($this->CallBack['constraint']))
				{
					$data = self::HandleConstraint($data, $this->CallBack['constraint']);
				}

				$rows[] = $data;

				if ($this->CallBack)
				{
					$args = array_merge(array($data), $this->CallBack['params']);
					call_user_func_array($callArray, $args);
				}
			}
		}
		else
		{
			$rows = pg_fetch_all($this->Resource);
		}

		if (empty($rows))
		{
			$rows = array();
		}
		
		return $rows;
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
		return $this->Count;
	}
	/**
	 * The number of rows in your dataset.
	 * 
	 * @return integer
	 */
	function GetCount()
	{
		return $this->Count;
	}
	/**
	 * @ignore
	 */
	public function rewind() 
	{
		return $this->Index = 0;
	}
	/**
	 * @ignore
	 */
	public function current() 
	{
		return $this->offsetGet($this->Index);
	}
	/**
	 * @ignore
	 */
	public function key() 
	{
		return $this->Index;
	}
	/**
	 * @ignore
	 */
	public function next() 
	{
		return ++$this->Index;
	}
	/**
	 * @ignore
	 */
	public function valid() 
	{
		return $this->offsetExists($this->Index);
	}
	function offsetExists($key)
	{
		return is_numeric($key) && $key >= 0 && $key < $this->Count;
	}
	function offsetGet($index)
	{
		if ($this->offsetExists($index))
		{
			return pg_fetch_assoc($this->Resource, $index);
		}
		else
		{
			return null;
		}
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
	}
}
?>