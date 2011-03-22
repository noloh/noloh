<?php

$_NCWD = getcwd();
$_NPath = dirname(__FILE__);
$_NPath .= strpos($_NPath, '/') === false ? "\\" : '/';
require_once($_NPath . 'Includes.php');

/**
 * Computes the NOLOH path based on this file's directory
 * @return string
 */
function ComputeNOLOHPath()	{return dirname(__FILE__);}
/**
 * Gets the current version of NOLOH
 * @return string
 */
function GetNOLOHVersion() {return '1.7.668';}

?>