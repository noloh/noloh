<?php
/**
 * @package Data
 */
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
		if($this->Connection != null && $this->SqlStatement != null)
			$tempCommand = pg_query($this->Connection->Connect(), $this->SqlStatement);
		if(!$tempCommand)
		{
			BloodyMurder(pg_last_error());
			return false;
		}
		return new PGSqlDataReader($tempCommand, $resultType);
	}
}
?>