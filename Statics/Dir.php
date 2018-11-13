<?php

final class Dir
{
	private function Dir() {}
	/**
	 * Generates a hash of a directory.
	 *
	 * @param $directory
	 * @return bool|string
	 * @throws Exception
	 */
	static function HashDirectory($directory)
	{
		try
		{
			// Source: Jon LaBelle from https://jonlabelle.com/snippets/view/php/generate-md5-hash-for-directory

			if (!is_dir($directory))
			{
				return false;
			}

			$files = array();
			$dir = dir($directory);

			while (false !== ($file = $dir->read()))
			{
				if ($file != '.' && $file != '..')
				{
					if (is_dir($directory . '/' . $file))
					{
						$files[] = static::HashDirectory($directory . '/' . $file);
					}
					else
					{
						$files[] = md5_file($directory . '/' . $file);
					}
				}
			}

			$dir->close();

			return md5(implode('', $files));
		}
		catch (Exception $e)
		{
			throw new Exception("Cannot hash directory {$directory}");
		}
	}
	/**
	 * Given a From and To Directory, the To directory will sync with the From.
	 *
	 * @param $fromDirectory
	 * @param $toDirectory
	 * @throws Exception
	 */
	static function MirrorDirectory($fromDirectory, $toDirectory)
	{
		try
		{
			// Check to see if the From and To directories are siblings
			$from = rtrim(realpath(dirname($fromDirectory)), '/\\');
			$to = rtrim(realpath(dirname($toDirectory)), '/\\');

			if ($from !== $to)
			{
				throw new Exception("From and To directories are not siblings");
			}

			// Add check for OS and then copy directory, and set permissions if Linux
			if (System::IsWindows())
			{
				// TODO: Double check xcopy. Also, add a MIR comment.
				/* Robocopy has multiple success codes; hence the array passed in. */
				System::Execute("robocopy {$fromDirectory} {$toDirectory} /MIR", array(0, 1, 2, 4));
			}
			else
			{
				// Sync. -a: is recursive, also, preserve date, ownership, permissions, groups, etc.; --delete: remove deleted files
				System::Execute("rsync -a --delete {$fromDirectory} {$toDirectory}");

				static::SetOwner($toDirectory);
			}
		}
		catch (Exception $e)
		{
			throw new Exception("Cannot copy {$fromDirectory} to {$toDirectory}. $e->getMessage()");
		}
	}
	/**
	 * Removes a directory
	 *
	 * @param $directory
	 * @throws Exception
	 */
	static function RemoveDirectory($directory)
	{
		if (is_dir($directory))
		{
			try
			{
				if (System::IsWindows())
				{
					// /s: remove recursively; /q: don't prompt
					System::Execute("rmdir {$directory} /s /q");
				}
				else
				{
					// -r: remove recursively; -f: don't prompt
					System::Execute("rm -rf {$directory}");
				}
			}
			catch (Exception $e)
			{
				throw new Exception("Cannot remove directory {$directory}. $e->getMessage()");
			}
		}
	}
	/**
	 * Sets the owner of a directory. Defaults to 'flowgroup'.
	 *
	 * @param $directory
	 * @param string $owner
	 * @throws Exception
	 */
	static function SetOwner($directory, $owner = 'flowgroup')
	{
		try
		{
			if (!System::IsWindows())
			{
				System::Execute("chown {$owner} {$directory} -R");
			}
		}
		catch (Exception $e)
		{
			throw new Exception("Unable to set permissions for directory {$directory}. $e->getMessage()");
		}
	}
	/**
 	 * Returns an array of all files present at all depths in the directory provided.
 	 * The full file path will be returned.
	 *
	 * @param $path
	 * @return array
 	 */
	public static function RecursiveScanDir($path)
	{
		$fullPath = realpath($path);

		// Check that path exists and that it is a directory
		if (file_exists($fullPath) === false)
		{
			BloodyMurder('Recursive scan failed: Directory not found');
		}
		elseif (is_file($fullPath))
		{
			BloodyMurder('Recursive scan failed: Path is not a directory');
		}

		$files = array();

		$dirContents = scandir($fullPath);

		foreach ($dirContents as $content)
		{
			if (is_file($fullPath . DIRECTORY_SEPARATOR . $content))
			{
				$files[] = $fullPath . DIRECTORY_SEPARATOR . $content;
			}
			elseif ($content !== '.' && $content !== '..')
			{
				$files = array_merge($files, self::RecursiveScanDir($fullPath . DIRECTORY_SEPARATOR . $content));
			}
		}

		return $files;
	}
}