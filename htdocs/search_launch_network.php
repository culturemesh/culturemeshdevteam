<?php
ini_set("display_errors", true);

include_once("data/dal_network.php");
include_once("data/dal_network-dt.php");
include_once("data/dal_locations.php");
include_once("data/dal_languages.php");

// start db connection
$con = getDBConnection();

$query = array($_POST['type'], mysqli_real_escape_string($con, $_POST['city_cur']), mysqli_real_escape_string($con, $_POST['region_cur']), mysqli_real_escape_string($con, $_POST['country_cur']), mysqli_real_escape_string($con, $_POST['q_1']), mysqli_real_escape_string($con, $_POST['q_2']), mysqli_real_escape_string($con, $_POST['q_3']));

$loc_data = null;
if ($query[1] == null && $query[2] == null)
  { $loc_data = Locations::getCOByName($query[3], $con); }
else if ($query[1] == null)
  { $loc_data = Locations::getRCByName($query[2], $query[3], $con); }
else
  { $loc_data = Locations::getCCByName($query[1], $query[2], $query[3], $con); }

//var_dump($query);
// get current stuff
//var_dump($loc_data);
$query_data = null;
$network = new NetworkDT();
$network->network_class = $query[0];

switch ($query[0])
{
case "co":
	$query_data = Locations::getCOByName($query[6], $con);
	$network->id_country_origin = $query_data[4];
	$network->country_origin = $query_data[5];
	break;
case "cc":
	$query_data = Locations::getCCByName($query[4], $query[5], $query[6], $con);
	$network->id_city_origin = $query_data[0];
	$network->city_origin = $query_data[1];
	$network->id_region_origin = $query_data[2];
	$network->region_origin = $query_data[3];
	$network->id_country_origin = $query_data[4];
	$network->country_origin = $query_data[5];
	break;
case "rc":
	$query_data = Locations::getRCByName($query[5], $query[6], $con);
	$network->id_region_origin = $query_data[2];
	$network->region_origin = $query_data[3];
	$network->id_country_origin = $query_data[4];
	$network->country_origin = $query_data[5];
	break;
case "_l":
	$query_data = Languages::getLanguageByName($query[4], $con);
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
$id = Network::launchNetwork($network, $con);

mysqli_close($con);

if (!$id)
  { header("Location: search_results.php?error=Could+not+launch+network"); }
else
  { header("Location: network.php?id={$id}"); }
?>
