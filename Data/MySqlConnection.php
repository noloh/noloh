<?php
/**
 * @package Data
 */
class MySqlConnection
{
	public $UserName;
	public $DatabaseName;
	public $Password;
	public $Host;
	public $Port;
	public $ActiveConnection;
	
	function MySqlConnection($userName='', $databaseName='', $host='localhost', $port='5432', $password='')
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
		$this->ActiveConnection = mysql_connect($tempConnectString);
		return $this->ActiveConnection;
	}
	function Close()
	{
		$status = mysql_close($this->ActiveConnection);
		return $status;
	}
}
?>