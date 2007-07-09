<?php

class PGSqlConnection
{
	public $UserName;
	public $DatabaseName;
	public $Password;
	public $Host;
	public $Port;
	public $ActiveConnection;
	
	function PGSqlConnection($whatUserName="", $whatDatabaseName="", $whatHost="localhost", $whatPort="5432", $whatPassword="")
	{
		$this->UserName = $whatUserName;
		$this->DatabaseName = $whatDatabaseName;
		$this->Host = $whatHost;
		$this->Port = $whatPort;
		$this->Password = $whatPassword;
	}
	
	function Connect()
	{
		$tempConnectString = "dbname = $this->DatabaseName user=$this->UserName host = $this->Host port = $this->Port password = $this->Password";
		$this->ActiveConnection = pg_connect($tempConnectString);
		return $this->ActiveConnection;
	}
	
	function Close()
	{
		$status = pg_close($this->ActiveConnection);
		return $status;
	}
}

?>