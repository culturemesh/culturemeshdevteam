<?php
ini_set("display_errors", true);

include_once("data/dal_network.php");
include_once("data/dal_network-dt.php");
include_once("data/dal_locations.php");
include_once("data/dal_languages.php");

$query = array($_POST['type'], $_POST['city_cur'], $_POST['country_cur'],
		$_POST['q_1'], $_POST['q_2']);

// start db connection
$con = getDBConnection();

// get current stuff
$loc_data = Locations::getCCByName($query[1], $query[2], $con);
$query_data = null;
$network = new NetworkDT();
$network->network_class = $query[0];

switch ($query[0])
{
case "co":
	$query_data = Locations::getCOByName($query[3], $con);
	$network->id_country_origin = $query_data[0];
	$network->country_origin = $query_data[1];
	break;
case "cc":
	$query_data = Locations::getCCByName($query[3], $query[4], $con);
	$network->id_city_origin = $query_data[0];
	$network->city_origin = $query_data[1];
	$network->id_country_origin = $query_data[2];
	$network->country_origin = $query_data[3];
	break;
case "rc":
	$query_data = Locations::getRCByName($query[3], $query[4], $con);
	$network->id_region_origin = $query_data[0];
	$network->region_origin = $query_data[1];
	$network->id_country_origin = $query_data[2];
	$network->country_origin = $query_data[3];
	break;
case "_l":
	$query_data = Languages::getLanguageByName($query[3], $con);
	$network->id_language_origin = $query_data[0];
	$network->language_origin = $query_data[1];
	break;
}

// load network
$network->id_city_cur = $loc_data[0];
$network->city_cur = $loc_data[1];
$network->id_country_cur = $loc_data[2];
$network->country_cur = $loc_data[3];

//var_dump($network);
$id = Network::launchNetwork($network, $con);

var_dump( $id);
mysqli_close($con);

header("Location: network.php?id={$id}");
?>
