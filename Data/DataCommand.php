<?php
/**
 * DataCommand class
 *
 * This class needs a description...
 * 
 * @package Data
 */
class DataCommand extends Object
{
	private $Connection;
	private $SqlStatement;
	
	function DataCommand($connection = null, $sql = '')
	{
		$this->Connection = $connection;
		$this->SqlStatement = $sql;
	}
	function GetConnection()			{return $this->Connection;}
	function SetConnection($connection)	{$this->Connection = $connection;}
	function GetSqlStatement()			{return $this->SqlStatement;}
	function SetSqlStatement($sql)		{$this->SqlStatement = $sql;}
	function Execute($resultType = Data::Both)
	{
		if($this->Connection != null && $this->SqlStatement != null)
		{
			$type = $this->Connection->GetType();
			if($type == Data::Postgres)
				$tmpResource = pg_query($this->Connection->Connect(), $this->SqlStatement);
			elseif($type == Data::MySQL)
			{
//				if(is_resource($this->Connection))
//					$tmpResource = $this->Connection;
//				else
				$tmpResource = $this->Connection->Connect();
				$tmpResource = mysql_query($this->SqlStatement, $tmpResource);
			}
				
			if(!$tmpResource)
			{
				if($type == Data::Postgres)
					BloodyMurder(pg_last_error());
				elseif($type == Data::MySQL)
					BloodyMurder(mysql_error());
				return false;
			}
			return new DataReader($type, $tmpResource, $resultType);
		}
		return false;
	}
}
?>