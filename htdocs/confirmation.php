<?php
	include 'environment.php';
	$cm = new Environment();

	$mobile_detect = new \misc\MobileDetect();
	
	session_name($cm->session_name);
	session_start();

	// user id
	$uid = $_GET['uid'];
	$act_code = $_GET['act_code'];
	$act_success = False;

	$cm->enableDatabase($dal, $do2db);

	if ($uid !== NULL) {
	  $user = \dobj\User::createFromId($uid, $dal, $do2db);
	}

	if (($user !== NULL) && ($user->act_code === $_GET['act_code'])) {

		$act_success = $user->activate($dal, $do2db, $act_code);
		$_SESSION['uid'] = $user->id; 
	}

	$cm->closeConnection();

	$page_loader = new \misc\PageLoader($cm, $mobile_detect);
	echo $page_loader->generate('templates' . $cm->ds .'confirmation.html', array(
		'vars' => $cm->getVars(),
		'logged_in' => $logged_in,
		'success' => $act_success,
		'get' => $_GET
	));
?>
