<?php

if (version_compare(PHP_VERSION, '7.2.0') < 0)
{
	class_alias('Base', 'Object');
}

?>