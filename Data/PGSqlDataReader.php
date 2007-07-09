<?php

class PGSqlDataReader
{
	//public $Command;
	public $Items;
	public $ResultType;
	
	function PGSqlDataReader($resources, $resultType=PGSQL_BOTH)
	{
		$this->ResultType = $resultType;
//		$this->Items = pg_fetch_all($resources);
//		print_r($this->Items);
		//print_r($resources);
		//print(" testing ");
		if($resultType == PGSQL_BOTH || $resultType == PGSQL_NUM)
		{
			$this->Items = array();
			$numRows = pg_numrows($resources);
			for ($i=0; $i < $numRows; ++$i)
				$this->Items[] = pg_fetch_array($resources, $i, $this->ResultType);
		}
		else
			$this->Items = pg_fetch_all($resources);
//			$this->Items = array_merge($this->Items, array_values($this->Items));
//		elseif($resultType == PGSQL_NUM)
//			$this->Items = array_values($this->Items);
//		print_r($this->Items);
//		
//		$numRows = pg_numrows($resources);
//		for ($i=0; $i < $numRows; $i++)
//			$this->Items[] = pg_fetch_array($resources, $i, $this->ResultType);
//		print_r($this->Items);	
			
//		return $this->Items
//		for($i=0; $i < $numRows; ++$i)
//		//$this->Command = $whatPGSqlCommand;
//		if ($whatPGSqlCommand != null)
//		{
//			$this->Command = $whatPGSqlCommand;
//			$mytempExecCommand = $whatPGSqlCommand->Execute();
//			$numRows = pg_numrows($mytempExecCommand);
//		
//			for ($i=0; $i < $numRows; $i++)
//				$this->Items[] = pg_fetch_array($mytempExecCommand, $i, $this->ResultType);
//		}
	}
	
//	function ExecuteCommand($whatPGSqlCommand = null, $whatResultType = PGSQL_BOTH)
//	{
//		$this->ResultType = $whatResultType;
//		$mytempExecCommand = null;
//		
//		if ($whatPGSqlCommand == null) 
//			$mytempExecCommand = $this->Command->Execute();
//		else
//		{
//			$mytempExecCommand = $whatPGSqlCommand->Execute();
//			$this->Command = $mytempExecCommand;
//		}
//		$numRows = pg_numrows($mytempExecCommand);
//		for ($i=0; $i < $numRows; $i++)
//			$this->Items[] = pg_fetch_array($mytempExecCommand, $i, $this->ResultType);
//		
//		return $this->Items[0];
//	}
	
}

?>