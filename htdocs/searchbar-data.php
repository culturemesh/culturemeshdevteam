<?php
ini_set('display_errors', 1);

include_once("zz341/fxn.php");
include_once("data/dal_network.php");
include_once("data/dal_language.php");
include_once("data/dal_location.php");

$con = getDBConnection();
$raw_languages = Language::getAllLanguages($con);
$raw_countries = Location::getAllCountries($con);
$raw_regions = Location::getAllRegions($con);
$raw_cities = Location::getAllCities($con);

mysqli_close($con);

$languages = array();
$countries = array();
$regions = array();
$cities = array();

while($row = mysqli_fetch_array($raw_languages))
{
	array_push($languages, $row['name']);	
}

while ($row = mysqli_fetch_array($raw_countries))
{
	array_push($countries, array($row['id'], $row['name']));
}

while($row = mysqli_fetch_array($raw_regions))
{
	array_push($regions, array($row['id'], $row['name'], $row['country_id'], $row['country_name']));
}

while($row = mysqli_fetch_array($raw_cities))
{
	array_push($cities, array($row['id'], $row['name'], $row['country_id'], $row['country_name']));
}

$allthedata = array(
	"languages" => $languages,
	"countries" => $countries,
	"regions" => $regions,
	"cities" => $cities);

echo json_encode($allthedata);
/*
if isset($_GET["initial"])
{
	return searchbarInitData()
}

function searchBarInitData()
{
	return json_encode($data);
}
 */
?>
