<?php
	require '../../environment.php';
	$cm = new \Environment();

	// JSON RESPONSE
	$json_response = array(
		'error' => NULL
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

	/*
	$json_response['error'] = 'Transmitted post';
	echo json_encode($json_response);
	 */

	// Database stuff
	$cm->enableDatabase($dal, $do2db);

	// Close database
	$cm->closeConnection();

	$json_response['error'] = 'Reworked database';
	echo json_encode($json_response);
	exit();
?>
