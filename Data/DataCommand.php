<?php
/**
 * @package Data
 */
class DataCommand extends Object
{
	public $Connection;
	public $SqlStatement;
	
	function DataCommand($connection = null, $sqlStament = '')
	{
		$this->Connection = $connection;
		$this->SqlStatement = $sqlStament;
	}
	function Execute($resultType = Data::Both)
	{
		if($this->Connection != null && $this->SqlStatement != null)
		{
			$type = $this->Connection->GetType();
		
			if($type == Data::Postgres)
				$tmpResource = pg_query($this->Connection->Connect(), $this->SqlStatement);
			elseif($type == Data::MySQL)
				$tmpResource = mysql_query($this->Connection->Connect(), $this->SqlStatement);
				
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