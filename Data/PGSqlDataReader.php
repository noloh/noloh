<?php
/**
 * @package Data
 */
class PGSqlDataReader
{
	public $Items;
	public $ResultType;
	
	function PGSqlDataReader($resources, $resultType=PGSQL_BOTH)
	{
		$this->ResultType = $resultType;
		if($resultType == PGSQL_BOTH || $resultType == PGSQL_NUM)
		{
			$this->Items = array();
			$numRows = pg_numrows($resources);
			for ($i=0; $i < $numRows; ++$i)
				$this->Items[] = pg_fetch_array($resources, $i, $this->ResultType);
		}
		else
			$this->Items = pg_fetch_all($resources);
	}
}
?>