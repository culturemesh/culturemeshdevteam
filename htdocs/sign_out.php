<?php

session_name("myDiaspora");
session_start();

unset($_SESSION['uid']);

include 'environment.php';
$cm = new Environment();

// possible pages that we could be logging out from
$pages = array('index', 'network', 'search_results', 
	'careers', 'about', 'press', 'profile');

$prev_url = $_SERVER['HTTP_REFERER'];

$redirect = new \nav\HTTPRedirect($cm, $prev_url, $pages);
$redirect->removeQueryParameters(array('lerror', 'rerror', 'jeerror', 'eid', 'ueerror'));

$redirect->execute();
?>
