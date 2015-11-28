<?php
	require '../../environment.php';
	$cm = new \Environment();

	// JSON RESPONSE
	$json_response = array(
		'error' => NULL,
		'main_network' => NULL
	);

	// GET POST DATA
	$post_data = json_decode(file_get_contents('php://input'), true);

	$search_origin = $post_data['origin'];
	$search_location = $post_data['location'];

	if (!isset($search_origin) || !isset($search_location)) {
		$json_response['error'] = 'No variables were set';
		echo json_encode($json_response);
		exit();
	}

	// Set up searchables from data
	$origin_searchable = new $search_origin['searchable_class'];
	$origin_searchable->id = $search_origin['id'];
	$origin_searchable->name = $search_origin['name'];

	$location_searchable = new $post_data['origin']['searchable_class'];
	$location_searchable->id = $search_location['id'];
	$location_searchable->name = $search_location['name'];

	// Database stuff
	$cm->enableDatabase($dal, $do2db);

	$network_search = new \search\NetworkSearch($search_origin, $search_location);
	$search_manager = new \search\SearchManager($cm, $dal, $do2db, $network_search);

	// Query for networks
	$results = $search_manager->getResults();

	// Prepare results for json
	$json_response['main_network'] = (array) $results;

	// Query for related networks
	$related_search = new \search\RelatedNetworkSearch($origin_searchable, $location_searchable);
	$search_manager->setSearch($related_search);

	$json_response['related_networks'] = (array) $search_manager->getResults();
	
	// Close database
	$cm->closeConnection();
	
	// Preparing data for return to json

	$json_response['error'] = 'Reworked database';
	echo json_encode($json_response);
	exit();
?>
