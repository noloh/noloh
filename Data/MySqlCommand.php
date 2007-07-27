<?php
/**
 * @package Data
 */
class MySqlCommand
{
	public $Connection;
	public $SqlStatement;
	
	function MySqlCommand($whatConnection = null, $whatSqlStatement = "")
	{
		$this->Connection = $whatConnection;
		$this->SqlStatement = $whatSqlStatement;
	}
	
	function Execute($whatConnection = null, $whatSqlStatement = "")
	{
		$tempCommand = false;
		if($whatConnection != null && $whatSqlStatement != null)
		{
			$whatConnection->Connect();
			$tempCommand = mysql_query($whatSqlStatement);
		}
		elseif($this->Connection != null && $this->SqlStatement != null)
		{
			$this->Connection->Connect();
			$tempCommand = mysql_query($this->SqlStatement);
		}
		
		if (!$tempCommand)
			BloodyMurder("A problem occured executing the command");
		return $tempCommand;
	}
}

?>