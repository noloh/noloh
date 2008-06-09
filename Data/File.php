<?php
/**
 * File class
 *
 * A File is an Object representing a file on the server. It contains various methods that have to do with reading, writing, or even
 * sending files to a client.
 * 
 * @package Data
 */
class File extends Object
{
	/**
	 * Open a file for reading only
	 */
	const Read = 'r';
	/**
	 * Open a file for reading and writing, overwriting anything previously in the file when writing
	 */
	const ReadWrite = 'r+';
	/**
	 * Open a file for writing only, overwriting anything previously in the file
	 */
	const Write = 'w';
	/**
	 * Open a file for appending to the file
	 */
	const Append = 'a+';
	
	private $Filename;
	private $File;
	private $Type;
	private $Size;
	private $PointerToFile;
	private $TempFilename;
	private $AutoSave = true;
	/**
	 * Send a file to the client
	 * @param string $fileName The path of the file to be sent
	 * @param string $contentType The type of the file, typically used when opening is preferred to saving
	 * @param string $alias The way the client would see the filename
	 */
	static function Send($fileName, $contentType='application/octet-stream', $alias=null)
	{
		AddScript('_NRequestFile("' . $_SERVER['PHP_SELF'] . '?NOLOHFileRequest=' . $fileName . '")');
		$_SESSION['_NFileSend'][$fileName] = array($contentType, $alias);
		//$webPage = GetComponentById('N1');
		//$webPage->Controls->Add($iframe = new IFrame($_SERVER['PHP_SELF'].'?NOLOHFileRequest='.$fileName));
	}
	/**
	 * @ignore
	 */
	static function SendRequestedFile($fileName)
	{
		if(isset($_SESSION['_NFileSend'][$fileName]))
		{
			$fileInfo = $_SESSION['_NFileSend'][$fileName];
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    header('Content-Description: File Transfer');
		    header('Content-Type: ' . $fileInfo[0]);
		    header('Content-Length: ' . filesize($fileName));
	    	header('Content-Disposition: attachment; filename=' . basename($fileInfo[1]?$fileInfo[1]:$fileName));
			readfile($fileName);
			unset($_SESSION['_NFileSend'][$fileName]);
		}
		else 
			BloodyMurder('You do not have permission to access that file!');
	}
	/**
	 * Constructor
	 * @param string|array $file A path to the file or an array of file information in the sense of PHP's file() function
	 * @return File
	 */
	function File($file = null)
	{
		if(is_array($file))
		{
			$this->File = $file;
			$this->Filename = $this->File['name'];
			$this->Type = $this->File['type'];
			$this->Size = $this->File['size'];
			$this->TempFilename = $this->File['tmp_name'];
		}
		elseif(is_file($file))
		{
			$this->File = $file;
			$this->Filename = $file;
			$this->Size = filesize($file);
			$this->Type = filetype($file);
		}
	}
	/**
	 * Open a file for reading, writing, or both. Always close your file when you are finished
	 * @param mixed $mode A File class constant signifying what the file is opened for 
	 * @param bool $autoSave
	 */
	function Open($mode = File::ReadWrite, $autoSave = true)
	{
		if(!$autoSave)
		{
			$this->AutoSave = false;
			$this->PointerToFile = tmpfile();
			fputs($this->PointerToFile, fread(fopen($this->File, 'r'), filesize($this->Filename)));
		}
		$this->PointerToFile = fopen(realpath($this->Filename), $mode);
	}
	/**
	 * Write a string to a file
	 * @param string $write The string to be written
	 */
	function Write($write='')
	{
		fwrite($this->PointerToFile, $write);
	}
	/**
	 * Read a file's content
	 * @return string
	 */
	Function Read()
	{
		$str = '';
		while(!feof($this->PointerToFile))
			$str .= fgets($this->PointerToFile);
		return $str;
	}
	/**
	 * Closes a previously opened file
	 */
	function Close()
	{
		fclose($this->PointerToFile);
	}
	/**
	 * Save a file to another location or name
	 * @param string $folder The path of the folder where the file will be saved
	 * @param string $fileName The new name of the file should you want the file to be renamed
	 */
	function SaveAs($folder = '', $fileName = null)
	{
		$tempString = '';
//		$tempString = "/";
		if(!empty($folder))
		{
			$tempString .= $folder;
			if(substr($folder, -1, 1) != '/')
				$tempString .= '/';
		}
		$tempString .= is_null($fileName)?$this->Filename:$fileName;
		//$tempString = $this->GetFullPath() . $tempString;
		//Alert($tempString);
		if(!is_null($this->TempFilename))
			copy($this->TempFilename, $tempString);
		elseif(!$this->AutoSave)
			copy($this->PointerToFile, $tempString);
		else
			copy($this->Filename, $tempString);
	}
	/**
	 * Returns the name of the file
	 * @return string
	 */
	function GetFilename()
	{
		return $this->Filename;
	}
	/**
	 * @ignore
	 */
	function GetFile()
	{
		return $this->File;
	}
	/**
	 * Returns the extension of the file
	 * @return string
	 */
	function GetType()
	{
		return $this->Type;
	}
	/**
	 * Returns the size of the file in bytes
	 * @return integer
	 */
	function GetSize()
	{
		return $this->Size;
	}
	/**
	 * Returns the full path of the file
	 * @return string
	 */
	function GetFullPath()
	{
		$relativePath = explode('/', $_SERVER['PHP_SELF']);
		array_pop($relativePath);
		$relativePath = implode('/', $relativePath);
		return($_SERVER['DOCUMENT_ROOT'] . $relativePath);
	}
	/**
	 * @ignore
	 */
	function __toString()
	{
		return $this->Filename;
	}
}
?>