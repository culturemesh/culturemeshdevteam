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

	$mobile_detect = new \misc\MobileDetect();

	session_name($cm->session_name);
	session_start();

	$cm->enableDatabase($dal, $do2db);

	// USER STUFF
	$site_user = NULL;
	$logged_in = false;
	$member = false;

	if (isset($_SESSION['uid'])) {

		$logged_in = true;

		// check if user is registered
		// if so, get user info
		$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);

		// see if user is registered
		// in network
		$guest = false;
	}

	$page_loader = new \misc\PageLoader($cm, $mobile_detect);
	echo $page_loader->generate('templates' . $cm->ds .'careers.html', array(
		'vars' => $cm->getVars(),
		'site_user' => $site_user,
		'logged_in' => $logged_in
	));
?>
