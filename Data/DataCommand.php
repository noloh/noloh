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
	private $Callback;
	public $ResultType;
	
	function DataCommand($connection = null, $sql = '', $resultType = Data::Both)
	{
		$this->Connection = $connection;
		$this->SqlStatement = $sql;
		if($resultType)
			$this->ResultType = $resultType;
	}
	function GetCallback()				{return $this->Callback;}
	function GetConnection()			{return $this->Connection;}
	function SetConnection($connection)	{$this->Connection = $connection;}
	function GetSqlStatement()			{return $this->SqlStatement;}
	function SetSqlStatement($sql)		{$this->SqlStatement = $sql;}
	
	function Execute($resultType = null)
	{
		if($this->Connection != null && $this->SqlStatement != null)
		{
			$type = $this->Connection->GetType();
			if($type == Data::Postgres)
				$resource = pg_query($this->Connection->Connect(), $this->SqlStatement);
			elseif($type == Data::MySQL)
			{
//				if(is_resource($this->Connection))
//					$tmpResource = $this->Connection;
//				else
				$resource = $this->Connection->Connect();
				$resource = mysql_query($this->SqlStatement, $resource);
			}
				
			if(!$resource)
			{
				if($type == Data::Postgres)
					BloodyMurder(pg_last_error());
				elseif($type == Data::MySQL)
					BloodyMurder(mysql_error());
				return false;
			}
			$resultType = $resultType?$resultType:$this->ResultType;
			return new DataReader($type, $resource, $resultType, $this->Callback);
		}
		return false;
	}
	function ReplaceParam($index, $value)
	{
		$sql = $this->SqlStatement;

		if ($this->Connection && preg_match('/^.*?\((.+?)\);\s*$/i', $sql, $matches)) 
		{
			preg_match_all('/\'[^\']*\'|[^,]+/i', $matches[1], $result, PREG_PATTERN_ORDER);
			$params = $result[0];
			$count = count($params);
			$replacement = '';
			for($i=0; $i < $count; ++$i)
			{
				if($i == $index || ($index < 0 && ($i - $count) == $index))
					$param = $this->Connection->ConvertValueToSQL($value);
				else
					$param = $params[$i];
				$replacement .= $param . ',';	
			}
			$replacement = rtrim($replacement, ',');
			
			$query = str_replace($matches[1], $replacement, $sql);
			
			$this->SetSqlStatement($query);
		}
	}
    function Callback($object, $functionName, $paramsAsDotDotDot = null)
    {
    	$offset = 0;
    	$args = func_get_args();
    	if($args[0] instanceof DataConstraint)
    		$offset = 1;
    		
		if(is_string($args[$offset]))
		{
			$key = 'class';
			$object = $args[$offset];
		}
		else
		{
			$key = 'id';
			$object = $args[$offset]->Id;
		}
		$callBack = array($key => $object, 'function' =>$args[$offset + 1], 'params' => array_slice($args, $offset + 2));
		if($offset == 1)
			$callBack['constraint'] = $args[0];
		$this->Callback = $callBack;
    }
}
?>