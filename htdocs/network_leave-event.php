<?php

include ('http_redirect.php');

// set up redirect
$pages = array('network');
$prev_url = $_SERVER['HTTP_REFERER'];

$redirect = new HTTPRedirect($prev_url, $pages);

// check for posted values
if (isset($_POST['uid']) && isset($_POST['eid'])) {

	include_once('data/dal_query_handler.php');
	include_once('data/dal_event_registration.php');

	$uid = $_POST['uid'];
	$eid = $_POST['eid'];

	// no con needed, only one operation
	$result = EventRegistration::deleteEventRegistration($uid, $eid);

	// remove unwanted parameters, will cause weird behavior i'm sure
	$params = array('jeerror', 'elink', 'eid');
	$redirect->removeQueryParameters($params);

	if ($result) {

		$redirect->addQueryParameter('eid', $eid);
		$redirect->addQueryParameter('leerror', 'You have left this event!');	
		$redirect->execute();
	}
	else {
		$redirect->addQueryParameter('leerror', 'Server error, try again later');	
		$redirect->execute();
	}

}
else {
	// cannot proceed, not all values
	$redirect->addQueryParameter('leerror', 'Not all values are present');
	$redirect->execute();
}

?>
