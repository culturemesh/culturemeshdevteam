<?php
	require '../../environment.php';
	$cm = new \Environment();
	//$cm->displayErrors();

	// JSON RESPONSE
	$json_response = array(
		'error' => NULL,
		'main_network' => NULL,
		'possible_networks' => NULL,
		'active_networks' => NULL
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
	$origin_searchable->id = (int) $search_origin['id'];
	$origin_searchable->name = $search_origin['name'];

	$location_searchable = new $search_location['searchable_class'];
	$location_searchable->id = (int) $search_location['id'];
	$location_searchable->name = $search_location['name'];

	// Database stuff
	$cm->enableDatabase($dal, $do2db);

	$search_manager = new \search\SearchManager($cm, $dal, $do2db, NULL);

	// Gets a better version of the searchables
	if ($search_origin['searchable_class'] == $search_location['searchable_class']) {

		$ids = array( (int) $search_origin['id'], (int) $search_location['id']);
		$combined_search = new \search\SearchableGroupIdSearch( $ids, $search_origin['searchable_class']);

		$search_manager->setSearch($combined_search);
		$results = $search_manager->getResults();

		if ($results != False) {
			foreach($results as $r) {
				if ($r->id == $search_origin['id'])
				  $origin_searchable = $r;
				else
				  $location_searchable = $r;
			}
		}
	}
	else {

		$origin_search = new \search\SearchableIdSearch($search_origin['id'], $search_origin['searchable_class']);
		$location_search = new \search\SearchableIdSearch($search_location['id'], $search_location['searchable_class']);

		$search_manager->setSearch( $origin_search );
		$origin_searchable = $search_manager->getResults();

		$search_manager->setSearch( $location_search );
		$location_searchable = $search_manager->getResults();
	}

	$network_search = new \search\NetworkSearch($origin_searchable, $location_searchable);
	$search_manager->setSearch($network_search);

	// Query for networks
	$results = $search_manager->getResults();

	// Prepare results for json
	if ($results !== False) {

		// get JSON if possible
		//
		$json_response['main_network'] = $results->getJSON();
	}
	else {
		$main_network = new \dobj\Network();
		$main_network->origin_searchable = $origin_searchable;
		$main_network->location_searchable = $location_searchable;

		$json_response['main_network'] = $main_network->getJSON();
	}

	// Query for related networks
	$related_search = new \search\RelatedNetworkSearch($origin_searchable, $location_searchable);
	$search_manager->setSearch($related_search);

	$related_network_results = $search_manager->getResults();

	// Close database
	$cm->closeConnection();

	// Preparing data for return to json
	$related_networks_json = array();

	foreach( $related_network_results as $related_network ) {
		array_push($related_networks_json, $related_network->getJSON());
	}

	$json_response['related_networks'] = $related_networks_json;
	$json_response['error'] = 0;

	// RETURN THE STUFF
	echo json_encode($json_response);
	exit();
?>
