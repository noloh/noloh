<?php
/**
 * @package Data
 */
class PGSqlConnection
{
	public $UserName;
	public $DatabaseName;
	public $Password;
	public $Host;
	public $Port;
	public $ActiveConnection;
	
	function PGSqlConnection($userName="", $databaseName="", $host="localhost", $port="5432", $password="")
	{
		$this->UserName = $userName;
		$this->DatabaseName = $databaseName;
		$this->Host = $host;
		$this->Port = $port;
		$this->Password = $password;
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