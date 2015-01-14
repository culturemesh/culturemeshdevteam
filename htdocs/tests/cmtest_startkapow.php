<?php

include 'Environment.php';
$cm = new Environment();

if (!$cm) {
	throw new Exception('Cannot find environment files');
	exit;
}
?>
