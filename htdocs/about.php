<?php

	include("environment.php");
	$cm = new Environment();

	session_name($cm->session_name);
	session_start();

	// Basic test for mobile device
	$mobile_detect = new \misc\MobileDetect();

	$cm->enableDatabase($dal, $do2db);

	//
	// USER STUFF
	//
	$site_user = NULL;
	$logged_in = false;
	
	if (isset($_SESSION['uid'])) {
		$logged_in = true;

		// check if user is registered
		// if so, get user info
		$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);
	}

	// GET TEAM MEMBERS
	$team_members = $do2db->execute($dal, NULL, 'getTeamMembers');
	$cm->closeConnection();

	//
	////////////// RENDER TEAM MEMBERS ////////////
	//

	// mustache components
	$m_comp = new \misc\MustacheComponent();

	$searchbar_template = file_get_contents('templates' . $cm->ds . 'searchbar.html');
	$sb_alt_font = $m_comp->render($searchbar_template, array('alt-font' => True, 'alt-color' => True, 'network' => True, 'vars'=>$cm->getVars()));

	// Width calculations
	//
	$team_members_count = count($team_members);

	$tmp = file_get_contents($cm->template_dir . $cm->ds . 'about_team-member_ul.html');
	$team_html = $team_members->getHTML('about', array(
		'cm' => $cm,
		'mustache' => $m_comp,
		'list_template' => $tmp,
		'list_vars' => array('row_width' => $row_width)
		)
	);

	//
	///////////// HANDLE EMAILS
	//
	$success = False;
	$failure = False; // requires two because of templating

        if(isset($_POST['contact_name']) && ($_POST['contact_body'])
		&& isset($_POST['contact_email'])){

		$contact_us = new \api\ContactUsEmail($cm, $m_comp, 'ken@culturemesh.com', array(
			'name' => $_POST['contact_name'],
			'email' => $_POST['contact_email'],
			'message' => $_POST['contact_body']
		));

		$success = $contact_us->send();
		if (!$success)
		  $failure = True;

        }

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

	//
	// LOAD THE PAGE
	//

	$page_loader = new \misc\PageLoader($cm, $mobile_detect);
	echo $page_loader->generate('templates' . $cm->ds .'about.html', array(
		'vars' => $cm->getVars(),
		'site_user' => $site_user,
		'logged_in' => $logged_in,
		'site_user' => $site_user,
		'team_html' => $team_html,
		'searchbars' => array(
			'alt-font' => $sb_alt_font
		),
		'success' => $success,
		'failure' => $failure
	));
?>
