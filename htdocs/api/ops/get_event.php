<?php

$json_response = array(
	'error' => NULL,
	'event' => NULL
);

$json_post = json_decode($HTTP_RAW_POST_DATA, true);

include '../../environment.php';
$cm = new Environment();

// start session
session_name($cm->session_name);
session_start();

$eid = (int) $json_post['id'];

$param_object = new \dobj\Blank();
$param_object->id = $eid;

$cm->enableDatabase($dal, $do2db);

$result = $do2db->execute($dal, $param_object, 'getEventById');

// check if user is logged in
// check registration
$site_user = NULL;
$logged_in = false;
$member = false;

if (isset($_SESSION['uid'])) {

	$logged_in = true;

	// check if user is registered
	// if so, get user info
	$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);
}

$cm->closeConnection();

if (get_class($result) === "PDOStatement") {
	$json_response['event'] = NULL;
	$json_response['error'] = True;
	$json_response['error_message'] = "No event found with that id";
}
else {
	// check 
	$result->checkMembership($site_user);
	$json_response['event'] = $result->getJSON();;
}
echo json_encode($json_response);

?>
