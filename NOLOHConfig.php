<?php
/**
* @ignore
*/
final class NOLOHConfig
{	
	private function NOLOHConfig(){}
	
	static function GetNOLOHPath()
	{
		return '/Projects/NOLOH/';
	}
	static function GetBaseDirectory()
	{
		return $_SERVER['DOCUMENT_ROOT'];
	}
}
?>
