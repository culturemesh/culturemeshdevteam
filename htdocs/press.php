<?php

	include("environment.php");
	$cm = new Environment();

	$mobile_detect = new \misc\MobileDetect();

	session_name($cm->session_name);
	session_start();

	$cm->enableDatabase($dal, $do2db);

	$logged_in = false;
	$site_user = NULL;

	// USER STUFF
	if (isset($_SESSION['uid'])) {

		$logged_in = true;

		// check if user is registered
		// if so, get user info
		$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);
	}

	// get press
	$press = $do2db->execute($dal, NULL, 'getPress');

	$cm->closeConnection();

	$m_comp = new \misc\MustacheComponent();

	// searchbar
	$searchbar_template = file_get_contents('templates' . $cm->ds . 'searchbar.html');
	$sb_alt_font = $m_comp->render($searchbar_template, array('alt-font' => True, 'alt-color' => True, 'network' => True, 'vars'=>$cm->getVars()));

	$press_html = $press->getHTML('press', array(
		'cm' => $cm,
		'mustache' => $m_comp));

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

	echo $page_loader->generate('templates' . $cm->ds .'press.html', array(
		'vars' => $cm->getVars(),
		'site_user' => $site_user,
		'logged_in' => $logged_in,
		'searchbars' => array(
			'alt-font' => $sb_alt_font
		),
		'press' => $press_html
	));
?>
