<?php

	include("environment.php");
	$cm = new Environment();

	$mobile_detect = new \misc\MobileDetect();

	session_name($cm->session_name);
	session_start();

	$cm->enableDatabase($dal, $do2db);

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

	$sb_home = $m_comp->render($searchbar_template, array('sb-home' => True));
	$sb_alt_font = $m_comp->render($searchbar_template, array('alt-font' => True));

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
		'top_networks' => $top_network_html,
		'main_image' => $main_image,
		'site_user' => $site_user,
		'searchbars' => array(
			'sb-home' => $sb_home,
			'alt-font' => $sb_alt_font
		)
	));

	/*
		/////////////////////////////////////////////////////////////////////
		// MAKING SURE PICTURES VARY N SUCH
		
	//"images/cmfrontpage_image1.jpg",
	// too much white
		$bg_links = array( 
			"images/cmfrontpage_image1.jpg",
			"images/cmfrontpage_image2.jpg", 
			"images/cmfrontpage_image3.jpg",
			"images/cmfrontpage_image4.jpg",
			//"images/cmfrontpage_image5.jpg",
			"images/cmfrontpage_image6.jpg",
			"images/cmfrontpage_image7.jpg",
			//"images/cmfrontpage_image8.jpg",
			//"images/cmfrontpage_image9.jpg",
			//"images/cmfrontpage_image10.jpg",
			"images/cmfrontpage_image11.jpg",
			"images/cmfrontpage_image12.jpg",
			//"images/cmfrontpage_image13.jpg",
			//"images/cmfrontpage_image14.jpg",
			//"images/cmfrontpage_image15.jpg",
			"images/cmfrontpage_image16.jpg",
			"images/cmfrontpage_image17.jpg",
			//"images/cmfrontpage_image18.jpg",
			"images/cmfrontpage_image19.jpg",
			"images/cmfrontpage_image20.jpg",
			"images/cmfrontpage_image21.jpg",
			"images/cmfrontpage_image22.jpg",
			//"images/cmfrontpage_image23.jpg",
			"images/cmfrontpage_image24.jpg",
			"images/cmfrontpage_image25.jpg",
			"images/cmfrontpage_image26.jpg",
			"images/cmfrontpage_image27.jpg",
			"images/cmfrontpage_image28.jpg"
			);
		
		$i = rand(0,18);
		
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
		?>
		
		<style type='text/css'>
		#stage-area
		{
			background:url(<?php echo $bg_links[$i]; ?>);
		}
		</style>

		<?php
			include "headinclude.php";
		?>

		<script src="<?php echo \Environment::host_root(); ?>/js/searchbar.js"></script>

			<?php
				include "header.php";
			?>
			<?php if(isset($_GET['signout'])) : ?>
				<script>
					$("#signout_panel").show();
					$("#signout_panel").fadeOut(5000);
				</script>
			<?php endif; ?>

					<form id="search-form" class='stage' method="GET" action="//<?php echo \Environment::host_root(); ?>/search/" autocomplete="off">

						<?php
						$networks = Network::getTopFourNetworks();
						
						for ($i = 0; $i < count($networks); $i++)
							HTMLBuilder::displayPopNetwork($networks[$i]);
						?>
	<head>
		
		<title>CultureMesh - Connecting the World's Diasporas </title>
		<meta name="keywords" content="" />
		<meta name="description" content="Welcome to CultureMesh - Connecting the world's diasporas!" />
		


	</head>
	 */
?>
