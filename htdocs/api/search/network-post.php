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

	// Database stuff
	$cm->enableDatabase($dal, $do2db);

	$search = new \search\NetworkSearch($search_origin, $search_location);
	$search_manager = new \search\SearchManager($cm, $dal, $do2db, $search);

	// Query for networks
	$results = $search_manager->getResults();

	// prepare results for json
	$json_response['main_network'] = (array) $results;

	// Query for related networks
	
	// Close database
	$cm->closeConnection();

	
	// Preparing data for return to json

	$json_response['error'] = 'Reworked database';
	echo json_encode($json_response);
	exit();
?>
