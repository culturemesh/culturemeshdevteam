<?php
	include 'environment.php';
	$cm = new \Environment();

	session_name($cm->session_name);
	session_start();

	$json_response = array(
		'error' => NULL,
		'html' => NULL,
		'continue' => 'n',
		'postSwitchValues' => array(
			'nmp_more_tweets' => NULL,
			'nmp_initial' => NULL,
			'nmp_tweet_until_date' => NULL,
			'nmp_last_updated' => NULL,
			'nmp_cur_location_scope' => NULL,
			'nmp_max_location_scope' => NULL,
			'nmp_cur_origin_scope' => NULL,
			'nmp_max_origin_scope' => NULL),
		'debug' => array(
			'location_ratio' => NULL,
			'origin_ratio' => NULL,
			'tweet_count' => NULL,
			'query_info' => NULL)
		);

	if (!isset($_POST['nmp_more_tweets']) || !isset($_POST['nmp_cur_location_scope']) ||
		!isset($_POST['nmp_max_location_scope']) || !isset($_POST['nmp_cur_origin_scope']) ||
		!isset($_POST['nmp_max_origin_scope']) || !isset($_POST['nid']) ||
		!isset($_POST['nmp_tweet_until_date']) || !isset($_POST['nmp_last_updated']) || 
		!isset($_POST['nmp_initial'])) {

			$json_response['error'] = 'Required variables were not set';
			echo json_encode($json_response);
			exit();
		}


	$json_response['postSwitchValues']['nmp_max_location_scope'] = (int) $_POST['nmp_max_location_scope'];
	$json_response['postSwitchValues']['nmp_max_origin_scope'] = (int) $_POST['nmp_max_origin_scope'];
	$json_response['postSwitchValues']['nmp_cur_location_scope'] = (int) $_POST['nmp_cur_location_scope'];
	$json_response['postSwitchValues']['nmp_cur_origin_scope'] = (int) $_POST['nmp_cur_origin_scope'];

	// important varyables
	$last_updated = $_POST['nmp_last_updated'];

	// setting up date stuff
	$this_moment = new \DateTime();
	$date_string = "";

	if ($_POST['nmp_tweet_until_date'] !== "0") {
		$date_string = $_POST['nmp_tweet_until_date'];
	}

	$date_dt = new DateTime($date_string);
	//$until_date = $date_dt->format('Y-m-d');
	$cur_until_date = $date_dt->format('Y-m-d');

	$component = NULL;

	// Check and see if it's the first time,
	//
	// Set the initial component to origin
	//
	if ($_POST['nmp_initial'] == 1) {
		$json_response['postSwitchValues']['nmp_initial'] = 0;
		$component = 'origin';
	}
	else {
		if ($date_string == "") {

			if ($last_updated == 'origin') {

				$component = 'location';

				if ($json_response['postSwitchValues']['nmp_cur_location_scope'] > 
				    $json_response['postSwitchValues']['nmp_max_location_scope'] ) {
					$component = 'origin';
				}
			}
			else {
				$component = 'origin';

				if ($json_response['postSwitchValues']['nmp_cur_origin_scope'] > 
				    $json_response['postSwitchValues']['nmp_max_origin_scope'] ) {
					$component = 'location';
				}
			}
		}
		// if we're continuing through a query's timeline...
		else {
			$component = $last_updated;
		}
	}

	$network = new \dobj\Network();
	$network->id = (int) $_POST['nid'];

	$dal = new \dal\DAL($cm->getConnection());
	$dal->loadFiles();
	$do2db = new \dal\Do2Db();
	$site_user = \dobj\User::createFromId((int) $_SESSION['uid'], $dal, $do2db);

	// get network for real
	$network = \dobj\Network::createFromId($network->id, $dal, $do2db);

	// modify network query info with POST data
	if ($component == "origin") {
		$network->query_origin_scope = $json_response['postSwitchValues']['nmp_cur_origin_scope'];
	}
	else if ($component == "location") {
		$network->query_location_scope = $json_response['postSwitchValues']['nmp_cur_location_scope'];
	}

	$tweet_manager = new \api\TweetManager($cm, $network,
			$dal, $do2db);

	$tweets = $tweet_manager->requestTweets('network_addtl', NULL, 
			array('until_date' => $cur_until_date, 
				'component' => $component));

	$json_response['debug']['tweet_count'] = count($tweets);

	$query_info = $tweet_manager->getQueryInfo();
	$json_response['until_date'] = $query_info['until_date'];
	$json_response['debug']['query_info'] = $query_info;

	$cm->closeConnection();

	/////// make components //////////
	$m_comp = new \misc\MustacheComponent();
	$tweets->setMustache($m_comp);

	try
	{
		$p_html = $tweets->getHTML('network', array(
			'cm' => $cm,
			'network' => $network,
			'site_user' => $site_user,
			'mustache' => $m_comp
			)
		);

		$json_response['error'] = 'Success';
	}
	catch (\Exception $e)
	{
		$p_html = NULL;
		$json_response['error'] = 'Failure: ' . $e;
	}

		$json_response['html'] = $p_html;

		/////////////////////////////////////////////////////////////////////////////////
		// UPDATING UNTIL DATE
		//
		// check tweet date to see if it's < 10 days
		//
		// @new - $exhausted_query, false if we've progressed too far through timeline
		//
		///////////////////////////////////////////////////////////////////////////
		$earliest_tweet_date = new \DateTime($query_info['until_date']);

		// check for equality of until dates
		// if unequal, subtract a day from until date
		//
		if ($cur_until_date == $earliest_tweet_date->format('Y-m-d')) {
			$earliest_tweet_date->sub(new \DateInterval('P4D'));
		}

		// now check to see if we've past the 10 days mark
		$new_difference = $this_moment->diff($earliest_tweet_date);
		$exhausted_query = (int) $new_difference->format('%a') >= 8 || count($tweets) < 15;

		$json_response['debug']['date_difference'] = $new_difference->format('%a');

		/////////////////////////////////////////////////////////////////////////////////////


		/////////////////////////////////////////////////////////////////////////////////////
		// UPDATING THE QUERY
		//
		// if query is not exhausted, don't updated 
		// scope values
		//
		// @change last updated if haven't exhausted query
		//
		/////////////////////////////////////////////////
		if ( !$exhausted_query ) {

			$date = $earliest_tweet_date->format('Y-m-d');
			$json_response['until_date'] = $date;
			$json_response['postSwitchValues']['nmp_tweet_until_date'] = $date;

		}
		// else, update, change until dates to null
		else {
			$json_response['until_date'] = "0";
			$json_response['postSwitchValues']['nmp_tweet_until_date'] = "0";

			if ($component == "origin") {
				$json_response['postSwitchValues']['nmp_cur_origin_scope'] += 1;
			}
			else if ($component == "location") {
				$json_response['postSwitchValues']['nmp_cur_location_scope'] += 1;
			}
		}

		$json_response['continue'] = 'y';
		$json_response['postSwitchValues']['nmp_more_tweets'] = 1;

		// change last_updated
		$json_response['postSwitchValues']['nmp_last_updated'] = $component;

		// Stops the entire mechanism when we've exhausted queries for all locations
		// and origins
		//
		if (($json_response['postSwitchValues']['nmp_cur_origin_scope'] > 
		    $json_response['postSwitchValues']['nmp_max_origin_scope']) &&
		    ($json_response['postSwitchValues']['nmp_cur_location_scope'] >
		    $json_response['postSwitchValues']['nmp_max_location_scope']) &&
		    $exhausted_query) {

			$json_response['continue'] = 'n';
			$json_response['postSwitchValues']['nmp_more_tweets'] = 0;
		}

	// return stuff
	echo json_encode($json_response);
?>
