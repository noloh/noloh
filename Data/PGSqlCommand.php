<?php

class PGSqlCommand
{
	public $Connection;
	public $SqlStatement;
	
	function PGSqlCommand($connection = null, $sqlStament = "")
	{
		$this->Connection = $connection;
		$this->SqlStatement = $sqlStament;
	}
	function Execute($resultType = PGSQL_BOTH)
	{
//		if($connection != null && $sqlStatment != null)
//			//CHANGE to pg_query
//			$tempCommand = pg_exec($connection->Connect(), $sqlStatment);
//		else
		if($this->Connection != null && $this->SqlStatement != null)
			$tempCommand = pg_query($this->Connection->Connect(), $this->SqlStatement);
		if(!$tempCommand)
		{
			BloodyMurder("A problem occured executing the command");
			return false;
		}
		return new PGSqlDataReader($tempCommand, $resultType);
		//return $tmpRS->Items;
		//return $tmpRS;
		//print_r($tmpRS);
	}
}

?>