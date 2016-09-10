<?php

	include "../../environment.php";
	$cm = new \Environment();

	$json_post = json_decode($HTTP_RAW_POST_DATA, true);
	$json_response = array(
		'results' => NULL,
		'error' => NULL
	);

	$input = array();
	$input['latitude'] = (float) $json_post['latitude']; // optional
	$input['longitude'] = (float) $json_post['longitude']; // ditto

	$name_search = new \search\SearchableGeolocationSearch($input);

	$cm->enableDatabase($dal, $do2db);
	$search_manager = new \search\SearchManager($cm, $dal, $do2db, $name_search);
	$search_results = $search_manager->getResults();

	$json_results = array();

	if ($search_results === False) {
		$json_results = False;
		$json_response['error'] = "No response was found";
	}
	else {
		foreach($search_results as $searchable) {
			array_push($json_results, $searchable->getJSON());
		}
	}

	$cm->closeConnection();

	// for now, let's just throw those results here
	$json_response['results'] = $json_results;
	echo json_encode($json_response);
?>
