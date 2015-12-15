<?php

class DataReaderIterator extends DataReader
{
	protected $Count;
	protected $Index = 0;
	
	/**
	 * Constructor
	 * Be sure to call this from the constructor of any class that extends DataReader.
	 * @param resource $resource A resource representing the data returned from the database.
	 * @param mixed Data::Assoc|Data::Numeric|Data::Both $resultType Determines how your data columns are indexed.
	 * @param ServerEvent $callBack
	 * @param Boolean $convertType Whether to convert returned data into their native PHP equivalents, instead of strings.
	 */
	function DataReaderIterator($resource, $resultType = Data::Assoc, $callBack = null, $convertType = false)
	{
		$this->Type = Data::Postgres;
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
		if ($this->Data !== null)
		{
			return parent::GetData();
		}
		return $this->ReadData();
	}
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
		$this->Index = 0;
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
		++$this->Index;
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
		if ($this->Data !== null)
		{
			return parent::offsetGet($index);
		}
		elseif ($this->offsetExists($index))
		{
			if (isset($this->CallBack['constraint']))
			{
				$object = isset($this->CallBack['id']) ? GetComponentById($this->CallBack['id']) : $this->CallBack['class'];
				$callArray = array($object, $this->CallBack['function']);
				
				$data = pg_fetch_array($this->Resource, $index, $this->ResultType);
				if (isset($this->CallBack['constraint']))
				{
					$data = self::HandleConstraint($data, $this->CallBack['constraint']);
				}

				$args = array_merge(array($data), $this->CallBack['params']);
				call_user_func_array($callArray, $args);
				
				return $data;
			}
			elseif ($this->Resource)
			{
				return pg_fetch_assoc($this->Resource, $index);
			}
			else
			{
				throw new SqlException('Resource not available');
			}
		}
		else
		{
			return null;
		}
	}
	function __sleep()
	{
		if ($this->Data === null)
		{
			$this->Data = $this->ReadData();
		}
		return array_keys((array)$this);
	}
}
?>