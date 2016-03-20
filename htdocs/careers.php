<?php
/*
    include_once 'zz341/fxn.php';
    include 'environment.php';
    $cm = new Environment();

    $ppre = "careers_pg";
    include 'reg_page_tpl.php';
 */
	include("environment.php");
	$cm = new Environment();

	session_name($cm->session_name);
	session_start();

	if (isset($_SESSION['uid']))
		$logged_in = true;
	else
		$logged_in = false;

	$page_loader = new \misc\PageLoader($cm);
	echo $page_loader->generate('templates' . $cm->ds .'careers.html', array(
		'vars' => $cm->getVars(),
		'logged_in' => $logged_in
	));
?>
