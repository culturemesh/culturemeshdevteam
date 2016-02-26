<?php
	include "../../environment.php";
	$cm = new \Environment();

	$json_post = json_decode($HTTP_RAW_POST_DATA, true);
	$json_response = array(
		'results' => NULL,
		'error' => NULL
	);

	$input_value = $json_post['input_value'];
	$search_class = $json_post['search_class']; // optional
	$search_table = $json_post['search_table']; // ditto

	$name_search = new \search\SearchableByName($input_value, $search_class, $search_table);

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
