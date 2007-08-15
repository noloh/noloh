<?php
/**
 * @package Collections
 */
class File
{
	const Read = "r";
	const ReadWrite = "r+";
	const Write = "w";
	const Append = "a+";
	
	private $Filename;
	private $File;
	private $Type;
	private $Size;
	
	private $PointerToFile;
	private $TempFilename = null;
	private $AutoSave = true;
	
	static function Send($fileName)
	{
		AddScript('_NRequestFile("' . $_SERVER['PHP_SELF'] . '?NOLOHFileRequest=' . $fileName . '")');
		$_SESSION['NOLOHFileSend'][$fileName] = true;
		//$webPage = GetComponentById('N1');
		//$webPage->Controls->Add($iframe = new IFrame($_SERVER['PHP_SELF'].'?NOLOHFileRequest='.$fileName));
	}
	
	static function SendRequestedFile($fileName)
	{
		if(isset($_SESSION['NOLOHFileSend'][$fileName]))
		{
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Length: ' . filesize($fileName));
	    	header('Content-Disposition: attachment; filename=' . basename($fileName));
			readfile($fileName);
			unset($_SESSION['NOLOHFileSend'][$fileName]);
		}
		else 
			print("You do not have permission to access that file!");
	}
	
	function File($whatFile = null)
	{
		if(is_array($whatFile))
		{
			$this->File = $whatFile;
			$this->Filename = $this->File['name'];
			$this->Type = $this->File['type'];
			$this->Size = $this->File['size'];
			$this->TempFilename = $this->File['tmp_name'];
		}
		else if(is_file($whatFile))
		{
			$this->File = $whatFile;
			$this->Filename = $whatFile;
			$this->Size = filesize($whatFile);
			$this->Type = filetype($whatFile);
		}
	}
	
	function Open($whatMode = self::ReadWrite, $autoSave = true)
	{
		if($autoSave == false)
		{
			$this->AutoSave = false;
			$this->PointerToFile = tmpfile();
			fputs($this->PointerToFile, fread(fopen($this->File, "r"), filesize($this->Filename)));
		}
		$this->PointerToFile = fopen(realpath($this->Filename), $whatMode);
	}
	
	function Write($whatWrite="")
	{
		fwrite($this->PointerToFile, $whatWrite);
	}
	
	Function Read()
	{
		$str = "";
		while(!feof($this->PointerToFile))
			$str .= fgets($this->PointerToFile);
			
		return $str;
	}
	
	function Close()
	{
		fclose($this->PointerToFile);
	}
	
	function SaveAs($folder = "", $filename = null)
	{
		$tempString = "";
//		$tempString = "/";
		if(!empty($folder))
		{
			$tempString .= $folder;
			if(substr($folder, -1, 1) != "/")
				$tempString .= "/";
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
		$relativePath = explode("/", $_SERVER['PHP_SELF']);
		array_pop($relativePath);
		$relativePath = implode("/", $relativePath);
		return($_SERVER['DOCUMENT_ROOT'] . $relativePath);
	}
}
?>