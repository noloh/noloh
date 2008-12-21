<?php
$_NPath = dirname(__FILE__) . '/';

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
function GetNOLOHVersion() {return '1.7.210';}

?>