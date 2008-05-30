<?php
/**
 * @package Data
 */
/**
 * File class
 *
 * A File is an Object representing a file on the server. It contains various methods that have to do with reading, writing, or even
 * sending files to a client.
 */
class File extends Object
{
	const Read = 'r';
	const ReadWrite = 'r+';
	const Write = 'w';
	const Append = 'a+';
	
	private $Filename;
	private $File;
	private $Type;
	private $Size;
	
	private $PointerToFile;
	private $TempFilename = null;
	private $AutoSave = true;
	
	static function Send($fileName, $contentType='application/octet-stream', $alias=null)
	{
		AddScript('_NRequestFile("' . $_SERVER['PHP_SELF'] . '?NOLOHFileRequest=' . $fileName . '")');
		$_SESSION['_NFileSend'][$fileName] = array($contentType, $alias);
		//$webPage = GetComponentById('N1');
		//$webPage->Controls->Add($iframe = new IFrame($_SERVER['PHP_SELF'].'?NOLOHFileRequest='.$fileName));
	}
	
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
	
	function Write($write='')
	{
		fwrite($this->PointerToFile, $write);
	}
	
	Function Read()
	{
		$str = '';
		while(!feof($this->PointerToFile))
			$str .= fgets($this->PointerToFile);
			
		return $str;
	}
	
	function Close()
	{
		fclose($this->PointerToFile);
	}
	
	function SaveAs($folder = '', $filename = null)
	{
		$tempString = '';
//		$tempString = "/";
		if(!empty($folder))
		{
			$tempString .= $folder;
			if(substr($folder, -1, 1) != '/')
				$tempString .= '/';
		}
		$tempString .= is_null($filename)?$this->Filename:$filename;
		//$tempString = $this->GetFullPath() . $tempString;
		//Alert($tempString);
		if(!is_null($this->TempFilename))
			copy($this->TempFilename, $tempString);
		elseif(!$this->AutoSave)
			copy($this->PointerToFile, $tempString);
		else
			copy($this->Filename, $tempString);
	}
	function __toString()
	{
		return $this->Filename;
	}
	
	function GetFilename()
	{
		return $this->Filename;
	}
	
	function GetFile()
	{
		return $this->File;
	}
	
	function GetType()
	{
		return $this->Type;
	}
	
	function GetSize()
	{
		return $this->Size;
	}
	
	function GetFullPath()
	{
		$relativePath = explode('/', $_SERVER['PHP_SELF']);
		array_pop($relativePath);
		$relativePath = implode('/', $relativePath);
		return($_SERVER['DOCUMENT_ROOT'] . $relativePath);
	}
}
?>