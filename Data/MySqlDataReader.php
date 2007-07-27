<?php
/**
 * @package Data
 */
class MySqlDataReader
{
	public $Command;
	public $Items = array();
	public $ResultType;
	
	function MySqlDataReader($whatMySqlCommand = null, $whatResultType = MYSQL_BOTH)
	{
		$this->ResultType = $whatResultType;
		$this->Command = $whatMySqlCommand;
		if ($whatMySqlCommand != null)
		{
			$this->Command = $whatMySqlCommand;
			$mytempExecCommand = $whatMySqlCommand->Execute();
			$numRows = mysql_num_rows($mytempExecCommand);
		
			for ($i=0; $i < $numRows; $i++)
				$this->Items[] =  mysql_fetch_array($mytempExecCommand, $i, $this->ResultType);
		}
	}
	
	function ExecuteCommand($whatMySqlCommand = null, $whatResultType = MYSQL_BOTH)
	{
		$this->ResultType = $whatResultType;
		$mytempExecCommand = null;
		
		if($whatMySqlCommand == null) 
			$mytempExecCommand = $this->Command->Execute();
		else
		{
			$mytempExecCommand = $whatMySqlCommand->Execute();
			$this->Command = $mytempExecCommand;
		}
		$numRows = mysql_num_rows($mytempExecCommand);
		for ($i=0; $i < $numRows; $i++)
			$this->Items[] =  mysql_fetch_array($mytempExecCommand, $i, $this->ResultType);
		
		return $this->Items[0];
	}
	
}

?>