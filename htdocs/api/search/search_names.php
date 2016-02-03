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

	$name_search = new \search\SearchableByName($input_value, $search_class);

	$cm->enableDatabase($dal, $do2db);
	$search_manager = new \search\SearchManager($cm, $dal, $do2db, $name_search);
	$search_results = $search_manager->getResults();

	$json_results = array();

	foreach($search_results as $searchable) {
		array_push($json_results, $searchable->getJSON());
	}

	/*
	$dummy_array = array(

		array('name' => 'Something', 'id' => 1234, 'obj_class' => '\dobj\City', 'type' => 'xx'),
		array('name' => 'Iowa', 'id' => 1234, 'obj_class' => '\dobj\Region', 'type' => 'xx'),
		array('name' => 'Belgium', 'id' => 1234, 'obj_class' => '\dobj\Country', 'type' => 'xx'),
		array('name' => 'Florida', 'id' => 1234, 'obj_class' => '\dobj\Region', 'type' => 'xx'),
		array('name' => 'Atlanta', 'id' => 1234, 'obj_class' => '\dobj\City', 'type' => 'xx'),
		array('name' => 'Georgia', 'id' => 1234, 'obj_class' => '\dobj\Country', 'type' => 'xx')
	);

	$result_items = array();

	foreach ($dummy_array as $dummy) {

		// push 
		if (strpos($dummy['name'], $user_value) !== False) {
			array_push($result_items, $dummy);
		}
	}
	 */

	$cm->closeConnection();

	// for now, let's just throw those results here
	$json_response['results'] = $json_results;
	echo json_encode($json_response);
?>
