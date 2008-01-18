<?php
interface Groupable
{
	function SetSelected($bool);
	function GetSelected();
	function SetGroupName($groupName);
	function GetGroupName();
	
}
interface MultiGroupable
{
	function SetSelected($bool);
	function GetSelected();
	function SetGroupName($groupName);
	function GetGroupName();
}
?>