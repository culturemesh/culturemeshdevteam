<?php

include '../../environment.php';
$cm = new Environment();

////////////////////////
// start session
session_name($cm->session_name);
session_start();

///////////////////////
// set up redirect
// possible pages that we could be logging in from
//
$pages = array('index', 'network', 'search_results', 
	'careers', 'about', 'press');

$prev_url = $_SERVER['HTTP_REFERER'];

$redirect = new \nav\HTTPRedirect($cm, $prev_url, $pages);
$redirect->removeQueryParameters(array('lerror', 'rerror', 'jeerror', 'eid', 'ueerror'));

/////////////////////////////
// get post variables
//
$origin_id = (int) $_POST['origin_id'];
$origin_class = $_POST['origin_class'];
$location_id = (int) $_POST['location_id'];
$location_class = $_POST['location_class'];

$origin_searchable = NULL;
$location_searchable = NULL;

$dal = NULL;
$do2db = NULL;
$cm->enableDatabase($dal, $do2db);

//////////////////////////////
// GET SITE USER
//
if (isset($_SESSION['uid'])) {

	$logged_in = true;

	// check if user is registered
	// if so, get user info
	$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);
}

$search_manager = new \search\SearchManager($cm, $dal, $do2db, NULL);

// GET ORIGIN AND LOCATION SEARCHABLES
if ($location_class == $origin_class) {

	$ids = array($origin_id, $location_id);
	$combined_search = new \search\SearchableGroupIdSearch( $ids, $origin_class );

	$search_manager->setSearch($combined_search);
	$results = $search_manager->getResults();

	foreach($results as $r) {
		if ($r->id == $origin_id)
		  $origin_searchable = $r;
		else
		  $location_searchable = $r;
	}
}
else {

	$origin_search = new \search\SearchableIdSearch($origin_id, $origin_class);
	$location_search = new \search\SearchableIdSearch($location_id, $location_class);

	$search_manager->setSearch( $origin_search );
	$origin_searchable = $search_manager->getResults();

	$search_manager->setSearch( $location_search );
	$location_searchable = $search_manager->getResults();
}

// double check existence of network
$network_search = new \search\NetworkSearch($origin_searchable, $location_searchable);
$search_manager->setSearch($network_search);
$network = $search_manager->getResults();

$id = NULL;

// If we're able, do this stuff
if ($network === False) {

	// launch network
	$op = new \ops\LaunchNetwork($origin_searchable, $location_searchable, $site_user);
	$op->run($dal, $do2db);
	$id = $op->getNetworkId();

	$launched = True;
}
else {
	$launched = False;
	$id = $network->id;
}

$cm->closeConnection();

// redirect to new NETWORKKKKK
//
$redirect->setControl('network', $id);
$redirect->execute();
?>
