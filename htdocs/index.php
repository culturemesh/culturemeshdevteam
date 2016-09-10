<?php

	include("environment.php");
	$cm = new Environment();

	$mobile_detect = new \misc\MobileDetect();

	session_name($cm->session_name);
	session_start();

	$cm->enableDatabase($dal, $do2db);

	// USER STUFF
	$site_user = NULL;
	$logged_in = false;

	if (isset($_SESSION['uid'])) {

		$logged_in = true;

		// check if user is registered
		// if so, get user info
		$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);
	}

	// GET TOP FOUR NETWORKS
	$top_networks = $do2db->execute($dal, NULL, 'getTopFourNetworks');

	// db shit
	$cm->closeConnection();

	//
	////////////// BACKGROUND IMAGE ////////////
	//
	$image_list_filename = \Environment::$site_root . $cm->ds . 'data' . $cm->ds . 'homepage-images.json';
	$image_list = json_decode( file_get_contents($image_list_filename), True);

	$image_count = count($image_list);
	$i = rand(0, $image_count - 1);
	
	if (isset($_SESSION['cur_bg']))
	{
		if ($_SESSION['cur_bg'] == $i)
		{
			$i+=1;
			if ($i > 1)
			{
				$i = 0;
				$_SESSION['cur_bg'] = $i;
			}
			else
				$_SESSION['cur_bg'] = $i;
		}
		else
			$_SESSION['cur_bg'] = $i;
	}
	else
		$_SESSION['cur_bg'] = $i;

	$main_image = $image_list[$i];

	//
	////////////// RENDER TOP FOUR NETWORKS ////////////
	//

	// mustache components
	$m_comp = new \misc\MustacheComponent();

	$searchbar_template = file_get_contents('templates' . $cm->ds . 'searchbar.html');

	$sb_home = $m_comp->render($searchbar_template, array('sb-home' => True, 
		'vars'=>$cm->getVars()
		));
	$sb_alt_font = $m_comp->render($searchbar_template, array('alt-font' => True, 'alt-color' => True, 'network' => True, 'vars'=>$cm->getVars()));

	$top_network_html = NULL;

	if ($top_networks) {
		//$tmp = file_get_contents($cm->template_dir . $cm->ds . 'home_popular-network.html');
		$top_network_html = $top_networks->getHTML('top-network', array(
			'cm' => $cm,
			'mustache' => $m_comp,
			)
		);
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
		$member = $site_user->checkNetworkRegistration($nid);//$network->checkRegistration($site_user->id, $dal, $do2db);
		$guest = false;
	}

	$page_loader = new \misc\PageLoader($cm, $mobile_detect);
	echo $page_loader->generate('templates' . $cm->ds .'home.html', array(
		'vars' => $cm->getVars(),
		'logged_in' => $logged_in,
		'site_user' => $site_user,
		'top_networks' => $top_network_html,
		'main_image' => $main_image,
		'site_user' => $site_user,
		'searchbars' => array(
			'sb-home' => $sb_home,
			'alt-font' => $sb_alt_font
		)
	));
?>
