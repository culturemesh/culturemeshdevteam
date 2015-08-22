<?php

include 'environment.php';
$cm = new Environment();

session_name($cm->session_name);
session_start();

unset($_SESSION['uid']);

// possible pages that we could be logging out from
$pages = array('index', 'network', 'search_results', 
	'careers', 'about', 'press', 'profile');

$prev_url = $_SERVER['HTTP_REFERER'];

$redirect = new \nav\HTTPRedirect($cm, $prev_url, $pages);
$redirect->removeQueryParameters(array('lerror', 'rerror', 'jeerror', 'eid', 'ueerror'));

$redirect->execute();
?>
