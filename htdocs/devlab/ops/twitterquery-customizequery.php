<?php

	$json_response = array(
		'error' => 0,
	);
	
	include '../../environment.php';
	$cm = new \Environment();

	$nid = (int) $_POST['nid'];
	$query = $_POST['query-custom'];

	$dal = new dal\DAL($cm->getConnection());
	$dal->loadFiles();
	$do2db = new dal\Do2Db();

	$network = dobj\Network::createFromId($nid, $dal, $do2db);
	$network->query_custom = $query;

	try {

		$network->writeCustomQuery($dal, $do2db, $query);
	}
	catch (\Exception $e) { 

		$json_response['error'] = 'Failure: ' . $e;
	}

	echo json_encode($json_response);

?>
