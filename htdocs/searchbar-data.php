<?php
ini_set('display_errors', 1);

include_once("zz341/fxn.php");
include_once("data/dal_network.php");
include_once("data/dal_language.php");
include_once("data/dal_location.php");

// If we're receiving this from a 
// searchbar, then we just want to return the file
// with all the nice json data
if (isset($_POST['data']))
{
	$file = fopen("data/searchbar-data.json", "r");

	if ($file == false )
		echo ( "Error in opening file" );

	$filesize = filesize( "data/searchbar-data.json" );
	$filetext = fread( $file, $filesize);
	echo $filetext;
}
else
{
	//echo "Attempting to write a json file with ALLTHEDATA";
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
		array_push($countries, array(
			'id' => $row['id'],
			'name' => $row['name']));
	}

	while($row = mysqli_fetch_array($raw_regions))
	{
		array_push($regions, array(
			'id' => $row['id'],
			'name' => $row['name'],
			'country_id' =>	$row['country_id'],
			'country_name' => $row['country_name']));
	}

	while($row = mysqli_fetch_array($raw_cities))
	{
		array_push($cities, array(
			'id' => $row['id'],
			'name' => $row['name'],
			'region_id' => $row['region_id'],
			'region_name' => $row['region_name'],
			'country_id' =>	$row['country_id'],
			'country_name' => $row['country_name']));
	}

	$allthedata = array(
		"languages" => $languages,
		"countries" => $countries,
		"regions" => $regions,
		"cities" => $cities);

	//$json_file = fopen($_SERVER['DOCUMENT_ROOT']."/culturemesh/culturemeshdevteam/htdocs/data/searchbar-data.json", "w");
	//jfwrite($json_file, json_encode($allthedata));
	//fclose($json_file);
	echo json_encode($allthedata);
}
//echo json_encode($allthedata);
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
