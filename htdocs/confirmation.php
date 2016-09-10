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

	$user = NULL;
	if ($uid !== NULL) {
	  $user = \dobj\User::createFromId($uid, $dal, $do2db);
	}

	if (($user !== NULL) && ($user->act_code === $_GET['act_code'])) {

		$act_success = $user->activate($dal, $do2db, $act_code);
		$_SESSION['uid'] = $user->id; 
	}

	$cm->closeConnection();

	// mustache components
	$m_comp = new \misc\MustacheComponent();

	$searchbar_template = file_get_contents('templates' . $cm->ds . 'searchbar.html');
	$sb_alt_font = $m_comp->render($searchbar_template, array('alt-font' => True, 'alt-color' => True, 'network' => True, 'vars'=>$cm->getVars()));

	$page_loader = new \misc\PageLoader($cm, $mobile_detect);
	echo $page_loader->generate('templates' . $cm->ds .'confirmation.html', array(
		'vars' => $cm->getVars(),
		'logged_in' => $logged_in,
		'searchbars' => array(
			'alt-font' => $sb_alt_font
		),
		'site_user' => $user,
		'success' => $act_success,
		'get' => $_GET
	));
?>
