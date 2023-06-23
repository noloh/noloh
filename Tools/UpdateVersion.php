<?php

require_once('../NOLOH.php');

chdir('..');

$revCount = System::Execute('git rev-list HEAD --count') + 1;

$fileContents = file_get_contents('NOLOH.php');
if ($fileContents === false)
{
	die('NOLOH.php is missing or corrupted.');
}

$nolohVersionValue = GetNOLOHVersion();
$versionSections = explode('.', $nolohVersionValue);
$versionSections[2] = $revCount;
$newNolohVersionValue = implode('.', $versionSections);
$fileContents = str_replace($nolohVersionValue, $newNolohVersionValue, $fileContents);
file_put_contents('NOLOH.php', $fileContents);