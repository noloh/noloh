<?php

/**
 * Class DataSequence
 *
 * A sequence is used to generate numbers in order.
 * They exist outside of transactions, so they correctly prevent duplicate values.
 */
class DataSequence extends Base
{
	protected $Name;
	protected $Connection;

	function __construct($name, DataConnection $connection)
	{
		parent::__construct();

		if (!preg_match('/^[a-zA-Z0-9_]+$/', $name))
		{
			BloodyMurder('Bad sequence name: ' . $name);
		}

		if (in_array($connection->Type, array(Data::Postgres, Data::MSSQL)))
		{
			$this->Name = $name;
			$this->Connection = $connection;
		}
		else
		{
			BloodyMurder('Connection type not supported');
		}

	}

	/**
	 * Get current value of sequence
	 * @return mixed
	 */
	function Current()
	{
		if ($this->Connection->Type === Data::Postgres)
		{
			$query = <<<SQL
				SELECT last_value FROM {$this->Name}
SQL;
			return $this->Connection->ExecSQL(Data::Assoc, $query, $this->Name)
				->Data[0]['last_value'];
		}
		else
		{
			$query = <<<SQL
				SELECT current_value FROM sys.sequences WHERE name = "{$this->Name}"
SQL;
			return $this->Connection->ExecSQL(Data::Assoc, $query, $this->Name)
				->Data[0]['current_value'];
		}
	}

	/**
	 * Get next value of sequence
	 * @return mixed
	 */
	function Next()
	{
		if ($this->Connection->Type === Data::Postgres)
		{
			$query = <<<SQL
				SELECT nextval("{$this->Name}") AS val
SQL;
		}
		else
		{
			$query = <<<SQL
				NEXT VALUE FOR "{$this->Name}" AS val
SQL;
		}
		return $this->Connection->ExecSQL(Data::Assoc, $query, $this->Name)
			->Data[0]['val'];
	}

	/**
	 * Set current value of sequence
	 * @param $val The value assigned
	 * @param boolean $isCalled If true then the next Next() call gives $val + incrementBy. If false, Next() returns $val the first time. A default of true is consistent with Postgres behavior.
	 */
	function Set($val, $isCalled = true)
	{
		$val = (int)$val;

		if ($this->Connection->Type === Data::Postgres)
		{
			$isCalled = $isCalled ? 'true' : 'false';

			$query = <<<SQL
				SELECT setval("{$this->Name}", {$val}, {$isCalled}) AS val
SQL;
			$this->Connection->ExecSQL(Data::Assoc, $query, $this->Name)
				->Data[0]['val'];
		}
		else
		{
			$val = $isCalled ? ($val - 1) : $val;
			$query = <<<SQL
				ALTER SEQUENCE "{$this->Name}"
				RESTART WITH {$val};
SQL;
			$this->Connection->ExecSQL(Data::Assoc, $query, $this->Name);
		}
	}

	/**
	 * Creates a sequence
	 * @param bool $ifNotExists If it exists and this is false, an error is produced
	 * @param int $incrementBy The value by which each Next() increments/decrements. Note it can be negative.
	 * @param int $minVal The smallest value the sequence can take
	 * @param null $maxVal The largest value the sequence can take
	 * @param int $startWith Where the sequence will start
	 * @param bool $cycle When the smallest/largest value is reached, if true, it will start over
	 * @param bool $isCalled If true then the next Next() call gives $val + $incrementBy. If false, Next() returns $val the first time. A default of false is consistent with Postgres behavior.
	 */
	function Create($ifNotExists = false, $incrementBy = 1, $minVal = 1, $maxVal = null, $startWith = 1, $cycle = false, $isCalled = false)
	{
		$ifNotExistsString = $ifNotExists ? 'IF NOT EXISTS' : '';
		$cycleString = $cycle ? 'CYCLE' : 'NO CYCLE';
		$minValString = $minVal === null ? '' : "MINVALUE {$minVal}";
		$maxValString = $maxVal === null ? '' : "MAXVALUE {$maxVal}";

		if ($this->Connection->Type === Data::Postgres)
		{
			$query = <<<SQL
				CREATE SEQUENCE {$ifNotExistsString} "{$this->Name}"
				INCREMENT BY {$incrementBy}
				{$minValString}
				{$maxValString}
				START WITH {$startWith}
				{$cycleString}
SQL;
		}
		else
		{
			$query = <<<SQL
				CREATE SEQUENCE "{$this->Name}"
				START WITH {$startWith}
				INCREMENT BY {$incrementBy}
				{$minValString}
				{$maxValString}
				{$cycleString}
SQL;
		}
		$this->Connection->ExecSQL(Data::Assoc, $query);

		if ($isCalled)
		{
			$this->Next();
		}
	}

	/**
	 * Destroys the sequence
	 * @param bool $ifExists If it doesn't exist and this is false, an error is produced
	 */
	function Drop($ifExists = false)
	{
		$ifExistString = $ifExists ? 'IF EXISTS' : '';

		$query = <<<SQL
			DROP SEQUENCE {$ifExistString} "{$this->Name}"
SQL;
		$this->Connection->ExecSQL(Data::Assoc, $query);
	}

}