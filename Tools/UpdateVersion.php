<?php

require_once('../Statics/System.php');
require_once('../NOLOHConfig.php');

chdir('..');

$revCount = System::Execute('git rev-list HEAD --count');
$revCount += 1;

$fileContents = file_get_contents('NOLOH.php');
if ($fileContents === false)
{
	die('NOLOH.php is missing or corrupted.');
}

$nolohVersionFunctionLocation = strpos($fileContents, 'GetNOLOHVersion');
$nolohVersionValueStartLocation = strpos($fileContents, "'", $nolohVersionFunctionLocation) + 1;
$nolohVersionValueEndLocation = strpos($fileContents, "'", $nolohVersionValueStartLocation) - 1;
$nolohVersionValue = substr(
	$fileContents,
	$nolohVersionValueStartLocation,
	($nolohVersionValueEndLocation - $nolohVersionValueStartLocation) + 1
);
$fileContents = str_replace($nolohVersionValue, NOLOHConfig::NOLOHBaseVersion . '.' . $revCount, $fileContents);
file_put_contents('NOLOH.php', $fileContents);