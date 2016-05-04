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

	$cm->enableDatabase($dal, $do2db);

	$logged_in = false;
	$site_user = NULL;

	if (isset($_SESSION['uid'])) {

		$logged_in = true;

		// check if user is registered
		// if so, get user info
		$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);
	}

	$cm->closeConnection();

	$page_loader = new \misc\PageLoader($cm);
	echo $page_loader->generate('templates' . $cm->ds .'careers.html', array(
		'vars' => $cm->getVars(),
		'logged_in' => $logged_in,
		'site_user' => $site_user
	));
?>
