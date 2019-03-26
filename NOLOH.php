<?php

if (!defined('PHP_VERSION_ID'))
{
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

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
function GetNOLOHVersion() {return '1.8.513';}   // Run "git rev-list HEAD --count", then +1.
?>