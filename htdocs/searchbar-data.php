<?php
include 'environment.php';
$cm = new \Environment();

// could become important later if we incorporate user data
session_name($cm->session_name);
session_start();

// If we're receiving this from a 
// searchbar, then we just want to return the file
// with all the nice json data
/*
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
}
 */
ini_set('memory_limit', '512M');

// check cache for current data
$cache = new \misc\Cache($cm);
$data_key = 'searchable_data_assoc_';

$tables = array('languages', 'cities', 'regions', 'countries');
$tables_found = True;

foreach($tables as $table) {

	if (!$cache->exists($data_key . $table)) {
		$tables_found = False;
		break;
	}
}

if ($tables_found) {
	$data_arrays = array();

	foreach($tables as $table) {

		$data_arrays[$table] = $cache->fetch($data_key . $table);
	}

	echo json_encode($data_arrays);
}
else {

	$dal = new \dal\DAL($cm->getConnection());
	$dal->loadFiles();
	$do2db = new \dal\Do2Db();

	$languages = dobj\Language::getAll($dal, $do2db);
	$countries = dobj\Country::getAll($dal, $do2db);
	$regions = dobj\Region::getAll($dal, $do2db);
	$cities = dobj\City::getAll($dal, $do2db);

	$allthedata = array(
		"languages" => $languages->toArray(),
		"countries" => $countries->toArray(),
		"regions" => $regions->toArray(),
		"cities" => $cities->toArray());

	// add to cache
	//
	// must split up because of memory limit
	//
	foreach ($tables as $table) {

		$cache->add($data_key . $table, $allthedata[$table]);
	}

	echo json_encode($allthedata);
}
?>
