<?php

class AdvisoryLock extends Base
{
	private $FilePointer;
	private $FileName;

	function __construct($key)
	{
		$this->FileName = static::LockFileName($key);
		$this->FilePointer = static::GetLockPointer($key);
	}

	/*
	 * This destructor will remove the need to handle clean up manually. When the object is no longer referenced,
	 * or the process is terminating this will handle clean up automatically.
	 */
	function __destruct()
	{
		if ($this->TryLock(false))
		{
			unlink($this->FileName);
		}

		fclose($this->FilePointer);
	}

	/**
	 * Generates the full lock filename from the given key
	 *
	 * @param $key
	 * @return string
	 */
	private static function LockFileName($key)
	{
		return sys_get_temp_dir() . "/{$key}.lock";
	}

	/**
	 * Returns a file handle pointing to the lock file
	 *
	 * @param $key unique key to identify this lock file
	 * @return bool|resource
	 */
	private static function GetLockPointer($key)
	{
		$lockHandle = fopen(static::LockFileName($key), 'c');

		return $lockHandle;
	}

	/**
	 * Attempts to acquire the advisory lock. By default this will cause the process to wait until the lock is available.
	 * If $block is set to false the function will return false and the process will continue
	 * @param bool $block Set to false to have the process continue without acquiring the lock
	 * @return bool
	 */
	public function TryLock($block = true)
	{
		$flags = $block ? LOCK_EX : LOCK_EX | LOCK_NB;

		return flock($this->FilePointer, $flags);
	}

	/**
	 * Releases the held advisory lock.
	 * This function should be called by locks which succeed their TryLock() check
	 * once the programmer wishes to release the lock
	 */
	public function Unlock()
	{
		flock($this->FilePointer, LOCK_UN);
	}
}