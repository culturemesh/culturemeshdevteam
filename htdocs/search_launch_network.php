<?php
ini_set("display_errors", true);

include_once("data/dal_network.php");
include_once("data/dal_network-dt.php");
include_once("data/dal_location.php");
include_once("data/dal_language.php");
include_once("http_redirect.php");

session_name("myDiaspora");
session_start();

// start db connection
$con = getDBConnection();

$query = array($_POST['type'], mysqli_real_escape_string($con, $_POST['city_cur']), mysqli_real_escape_string($con, $_POST['region_cur']), mysqli_real_escape_string($con, $_POST['country_cur']), mysqli_real_escape_string($con, $_POST['q_1']), mysqli_real_escape_string($con, $_POST['q_2']), mysqli_real_escape_string($con, $_POST['q_3']));

// process query for db entry
//
// > turn empty strings into NULL
// > add quotes
for ($i = 1; $i < count($query); $i++) {
	if ($query[$i] == '')
	  $query[$i]  = 'NULL';
}

$loc_data = null;
if ($query[1] == 'NULL' && $query[2] == 'NULL')
  { $loc_data = Location::getCOByNameR($query[3], $con); }
else if ($query[1] == 'NULL')
  { $loc_data = Location::getRCByNameR($query[2], $query[3], $con); }
else
  { $loc_data = Location::getCCByNameR($query[1], $query[2], $query[3], $con); }

//var_dump($query);
//echo '<br><br>';
// get current stuff
//var_dump($loc_data);
$query_data = null;
$network = new NetworkDT();
$network->network_class = $query[0];

// get ids and names of the query variable
switch ($query[0])
{
case "co":
	$query_data = Location::getCOByNameR($query[6], $con);
	$network->id_country_origin = $query_data[4];
	$network->country_origin = $query_data[5];
	break;
case "cc":
	$query_data = Location::getCCByNameR($query[4], $query[5], $query[6], $con);
	$network->id_city_origin = $query_data[0];
	$network->city_origin = $query_data[1];
	$network->id_region_origin = $query_data[2];
	$network->region_origin = $query_data[3];
	$network->id_country_origin = $query_data[4];
	$network->country_origin = $query_data[5];
	break;
case "rc":
	$query_data = Location::getRCByNameR($query[5], $query[6], $con);
	$network->id_region_origin = $query_data[2];
	$network->region_origin = $query_data[3];
	$network->id_country_origin = $query_data[4];
	$network->country_origin = $query_data[5];
	break;
case "_l":
	$query_data = Language::getLanguageByName($query[4], $con);
	$network->id_language_origin = $query_data[0];
	$network->language_origin = $query_data[1];
	break;
}

// load network
$network->id_city_cur = $loc_data[0];
$network->city_cur = $loc_data[1];
$network->id_region_cur = $loc_data[2];
$network->region_cur = $loc_data[3];
$network->id_country_cur = $loc_data[4];
$network->country_cur = $loc_data[5];

//var_dump($network);

// If network doesn't exist, create it
$test_query = Network::networkToQuery($network);
$result = Network::getNetworksAllClasses($test_query, $con);
$id = NULL;
//var_dump($network);
if(mysqli_num_rows($result) == 0)
{ 
	$id = Network::launchNetwork($network, $con); 

	// add user to launched network
	if (isset($_SESSION['uid'])) {
		$netreg = new NetworkRegistrationDT();

		$netreg->id_user = $_SESSION['uid'];
		$netreg->id_network = $id;

		NetworkRegistration::createNetRegistration($netreg);
	}
}



// result is for que?
$rows = QueryHandler::getRows($result);

// close connection
mysqli_close($con); 

// redirect
// 	success: network page
// 	failure: search results
$pages = array('search_results');

$prev_url = $_SERVER['HTTP_REFERER'];

$redirect = new HTTPRedirect($prev_url, $pages);

if (!$id)
{ 
	if ($id==NULL) {
		// header("Location: search_results.php?error=Network+exists");
		$redirect->addQueryParameter('error', 'Network exists');
       	}
	else {
		// header("Location: search_results.php?error=Could+not+launch+network"); 
		$redirect->addQueryParameter('error', 'Could not launch network');
	}
}
else {
       	//header("Location: network.php?id={$id}");
	$redirect->setPath('network.php');
	$redirect->addQueryParameter('id', $id);
}

// execute redirect
$redirect->execute();
?>
