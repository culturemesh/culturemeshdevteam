<?php

require 'vendor/autoload.php';
include_once 'zz341/fxn.php';
include 'environment.php';

include_once 'error_pool.php';

// data files
include_once 'data/dal_query_handler.php';
include_once 'data/dal_meta.php';
include_once 'data/dal_text_data.php';
include_once 'data/dal_location.php';
include_once 'data/dal_language.php';

include_once 'data/loc_item.php';

ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

include_once 'lib/misc/Util.php';

$con = QueryHandler::getDBConnection();

$m = new Mustache_Engine;

$mod_cols = array('name');
$json_post = array(
	'operation' => 'update',
	'table' => 'languages',
	'data' => array(
		'original_name' => 'Yucatan',
		'name' => 'Hardise',
		'id' => 546,
		'region_id' => 1417,
		'region_name' => 'Salk',
		'country_id' => 143,
		'country_name' => 'Anivalaina'
	)
);

$data = $json_post['data'];


	// UPDATE KEYS
	//
	// 	IF 
	// 	(2) country
	//
	// 	UPDATE (name)
	//if ($working_on_keys || $json_post['operation'] === 'create') {

	$working_on_keys = NULL;
	$updating_name = NULL;
	$updating_parent = NULL;
	$updating_children = NULL;
	$creating_searchable = NULL;

	$modifying_region = NULL;
	$modifying_country = NULL;

	if (in_array('name', $mod_cols)) {
		$updating_name = True;

		if (in_array($json_post['table'], array('regions', 'countries'))) {
		  $updating_children = True;
		}
	}
	if (in_array('region_id', $mod_cols)) {
		$updating_parent = True;
		$modifying_region = True;
	}
	if (in_array('country_id', $mod_cols)) {
		$updating_parent = True;
		$modifying_country = True;
	}
	if ($json_post['operation'] === 'create') {
		$creating_searchable = True;
	}

	if ($updating_name || $updating_parent || $creating_searchable) {

		if ($updating_name || $creating_searchable) {

			// DELETE KEYS ASSOCIATED WITH NAME
			//
			if ($updating_name) {

				if ($json_post['table'] === 'languages') {
					// operation
					var_dump(Language::deleteSearchKeys($data['id'], $con));
				}
				else {
					// operation
					var_dump(Location::deleteSearchKeys($data['id'], $json_post['table'], $con));
				}
			}

			/// Create keys for metaphone
			//
			$keys = \misc\Util::DoubleMetaphone($data['name']);

			// turn to keyed, array, also remove empty elements
			//
			$keys = array_filter( array_values($keys) );

			//
			// CREATE A NEW SET OF KEYS
			//
			//
			/// ALLTHE STUFF: array('city_id', 'city_name', 'region_id', 'region_name', 'country_id', 'country_name', 'language_id', 'language_name', 'class_searchable');
			//
			$col_names = array();
			$row_items = NULL;

			if ($json_post['table'] === 'cities') {
				$col_names = array('`key`', 'city_id', 'city_name', 'region_id', 'region_name', 'country_id', 'country_name');

				if ($data['region_id'] === "") {
					$region_id = "NULL";
					$region_name = "NULL";
				}
				else {
					$region_id = $data['region_id'];
					$region_name = "'" . $data['region_name'] . "'";
				}

				$row_items = array($data['id'], "'". $data['name'] . "'", $region_id, $region_name, $data['country_id'], "'" . $data['country_name'] . "'", '\'city\'');
			}

			if ($json_post['table'] === 'regions') {
				$col_names = array('`key`', 'region_id', 'region_name', 'country_id', 'country_name');
				$row_items = array($data['id'], "'". $data['name'] . "'", $data['country_id'], "'" . $data['country_name'] . "'", '\'region\'');
			}

			if ($json_post['table'] === 'countries') {
				$col_names = array('`key`', 'country_id', 'country_name');
				$row_items = array($data['id'], "'". $data['name'] . "'", '\'country\'');
			}

			if ($json_post['table'] === 'languages') {
				$col_names = array('`key`', 'language_id', 'language_name');
				$row_items = array($data['id'], "'". $data['name'] . "'", '\'language\'');
			}

			// add common column
			array_push($col_names, 'class_searchable');

			$rows = array();

			foreach ($keys as $key) {

				// put the element onto the beginning of the array
				array_unshift($row_items, "'" . $key . "'");

				$template_data = array(
					'row_values' => implode(',', $row_items)
				);

				$template = file_get_contents('templates/sql-insertrow-simple.sql');
				$row = $m->render($template, $template_data);

				array_push($rows, $row);

				// take the element back off the array
				array_shift($row_items);
			}

			$template_data = array(
				'table_name' => 'search_keys',
				'col_names' => implode(',', $col_names),
				'insert_rows' => implode(',', $rows)
			);

			$template = file_get_contents('templates/sql-insert-simple.sql');
			$stmt = $m->render($template, $template_data);
			var_dump($stmt);
	 		var_dump(QueryHandler::executeQuery($stmt, $con));

		}

		if ($updating_parent) {

			$update_args = NULL;

			$value_statements = array();
			$where_statements = array();

			// UPDATE KEY PARENTS
			if ($json_post['table'] === 'cities') {

				if ($modifying_region) {
					// possibly updating regions and countries
					if ($data['region_id'] === "") {
						array_push($value_statements, 'region_id=NULL');
						array_push($value_statements, 'region_name=NULL');
					}
					else {
						array_push($value_statements, 'region_id=' . $data['region_id']);
						array_push($value_statements, 'region_name=\'' . $data['region_name'] . '\'');
					}
				}

				if ($modifying_country) {
					array_push($value_statements, 'country_id=' . $data['country_id']);
					array_push($value_statements, 'country_name=\'' . $data['country_name'] . '\'');
				}

				// add where statement
				array_push($where_statements, 'city_id=' . $data['id']);
			}

			if ($json_post['table'] === 'regions') {

				// sorta redundant here, but why not?
				if ($modifying_country) {
					// updating countries
					array_push($value_statements, 'country_id=' . $data['country_id']);
					array_push($value_statements, 'country_name=\'' . $data['country_name'] . '\'');
				}

				// add where statement
				array_push($where_statements, 'region_id=' . $data['id']);
			}


			$template_data = array(
				'table_name' => 'search_keys',
				'value_statements' => implode(',', $value_statements),
				'where_statements' => implode(' AND ', $where_statements)
			);

			$template = file_get_contents('templates/sql-update-simple.sql');
			$stmt = $m->render($template, $template_data);
			var_dump($stmt);
	 		var_dump(QueryHandler::executeQuery($stmt, $con));

			//Location::updateSearchKeyParents($update_args, $json_post['table'], $con);
		}

		//
		// UPDATE CHILDREN
		//
		//
		if ($updating_children) {

			$value_statements = array();
			$where_statements = array();
			
			if ($json_post['table'] === 'regions') {

				array_push($value_statements, 'region_name=\'' . $data['name'] . '\'');
				array_push($where_statements, 'region_id=' . $data['id'] );
			}

			if ($json_post['table'] === 'countries') {
				array_push($value_statements, 'country_name=\'' . $data['name'] . '\'');
				array_push($where_statements, 'country_id=' . $data['id']);
			}

			$template_data = array(
				'table_name' => 'search_keys',
				'value_statements' => implode(',', $value_statements),
				'where_statements' => implode(' AND ', $where_statements)
			);

			$template = file_get_contents('templates/sql-update-simple.sql');
			$stmt = $m->render($template, $template_data);
			var_dump($stmt);
			var_dump(QueryHandler::executeQuery($stmt, $con));
		}
	}

	mysqli_close($con);


/*
ini_set('display_errors', true);
include('environment.php');
$cm = new Environment();
$cm->displayErrors();
$dal = NULL;
$do2db = NULL;
$cm->enableDatabase($dal, $do2db);

$json_response = array(
	'results' => NULL,
	'error' => NULL
);

$input_value = 'San Jose, Phillipines';
$search_class = 'location'; // optional

$name_search = new \search\SearchableByName($input_value, $search_class);

$cm->enableDatabase($dal, $do2db);
$search_manager = new \search\SearchManager($cm, $dal, $do2db, $name_search);
$search_results = $search_manager->getResults();

$json_results = array();

foreach($search_results as $searchable) {
	array_push($json_results, $searchable->getJSON());
}

$cm->closeConnection();

var_dump($json_results);
 */

/*
$search_origin = array(
	'searchable_class' => 'dobj\Country',
	'id' => 45008,
	'name' => 'China'
);

$search_location = array(
	'searchable_class' => 'dobj\City',
	'id' => 323791,
	'name' => 'East Lansing'
);

$alt_location = array(
	'searchable_class' => 'dobj\City',
	'id' => 310991,
	'name' => 'Tallahassee'
);

// Set up searchables from data
$origin_searchable = new $search_origin['searchable_class'];
$origin_searchable->id = $search_origin['id'];
$origin_searchable->name = $search_origin['name'];

$location_searchable = new $search_location['searchable_class'];
$location_searchable->id = $search_location['id'];
$location_searchable->name = $search_location['name'];

$alt_searchable = new $alt_location['searchable_class'];
$alt_searchable->id = $alt_location['id'];
$alt_searchable->name = $alt_location['name'];

// Database stuff
$cm->enableDatabase($dal, $do2db);

$network_search = new \search\NetworkSearch($origin_searchable, $location_searchable);
$search_manager = new \search\SearchManager($cm, $dal, $do2db, $network_search);
$results = $search_manager->getResults();

$related_search = new \search\RelatedNetworkSearch($origin_searchable, $location_searchable);
$search_manager->setSearch($related_search);

$related_networks = $search_manager->getResults();
var_dump($related_networks);

 */

/*
$nearby_location_search = new \search\NearbyLocationSearch($location_searchable);
$search_manager->setSearch($nearby_location_search);
$results = $search_manager->getResults();

$nearby_group_search = new \search\NearbyGroupLocationSearch(array($location_searchable, $alt_searchable));
$search_manager->setSearch($nearby_group_search);
$results = $search_manager->getResults();

// make fake ass networks
$networks = array();
$network = new \dobj\Network();
$network->origin_searchable = $location_searchable;
$network->location_searchable = $origin_searchable;
array_push($networks, $network);

$network = new \dobj\Network();
$network->origin_searchable = $origin_searchable;
$network->location_searchable = $location_searchable;
array_push($networks, $network);

// ACTUAL NETWORK
$actual_origin = new \dobj\City();
$actual_origin->id = 267101;
$actual_origin->name = 'Manila';

$actual_location = new \dobj\City();
$actual_location->id = 333381;
$actual_location->name = 'San Francisco';

$network = new \dobj\Network();
$network->origin_searchable = $actual_origin;
$network->location_searchable = $actual_location;
array_push($networks, $network);

$network_group_search = new \search\NetworkGroupSearch($networks);
$search_manager->setSearch($network_group_search);
$results = $search_manager->getResults();

var_dump($results);
*/

// Query for related networks
/*
$related_search = new \search\RelatedNetworkSearch($origin_searchable, $location_searchable);
$search_manager->setSearch($related_search);
$results= $search_manager->getResults();
*/



/*
$log = new \misc\Log($cm, 'test.log');
$log->logMessage('Hello World!');
$log->logArray(array('test' => 'one'));
$log->logArray(array('two', 'three'));
$log->logVar($cm);
 */

/*
	include_once("data/dal_location.php");
	include_once("data/dal_query_handler.php");
	include_once("data/dal_network.php");
 */
?>
<html>
	<head>
	<script src='js/dropzone.js'></script>
	</head>
	<body>
<?php
/*
	$con = QueryHandler::getDBConnection();
	$regions = Location::getRegionsByCountry("United States", $con);
	$regions = QueryHandler::getRows($regions);
	$cities = Location::getCitiesByCountry("United States", $con);
	$cities = QueryHandler::getRows($cities);
	$regions_nm = QueryHandler::getColumnFromDS($regions, 'name');
	$cities_nm = QueryHandler::getColumnFromDS($cities, 'name');
	$result = Network::getNetworksByRegions($regions_nm, $con);
	$result = QueryHandler::getRows($result);
	var_dump($result);
	echo "</br></br>";
	var_dump($cities_nm);
	$result = Network::getNetworksByCities($cities_nm, $con);
	$result = QueryHandler::getRows($result);
	var_dump($result);
	echo "</br></br>";
	mysqli_close($con);
*/

//	var_dump( filter_var("jumpergm@ail", FILTER_VALIDATE_EMAIL));
/*
$array = array(
	array('distance' => 4),
	array('distance' => 10),
	array('distance' => 17),
	array('distance' => 3),
	array('distance' => -15)
);

usort($array , function($a, $b) {
	if ($a['distance'] == $b['distance']) {
		return 0;
	}

	return ($a['distance'] < $b['distance']) ? -1 : 1;
});

print_r($array);
 */

/*
include 'autoload.php';
include 'vendor/autoload.php';

use Respect\Validation\Validator as v;

echo $_SERVER['DOCUMENT_ROOT'];
echo '</br>';
echo Foo::success();

$number = 123;
echo v::numeric()->validate($number); //true

$storage = new \Upload\Storage\FileSystem('/var/www/');
var_dump($storage);
 */

//echo __DIR__;
//throw new Exception('test');
echo 'something else' . '<br>';
echo $_SERVER['HTTP_HOST'];
?>
	
<!--
	<form action="/file-upload"
	      class="dropzone"
	      id="my-awesome-dropzone"></form>
	<div id='upload'>Click MEEE!</div>
	<div class="dropzone-previews"></div>
	<script>
var dropzone = new Dropzone("div#upload", {
	url : 'file/post',
	maxFilesize : 1,
	uploadMultiple : true,
	previewsContainer : 'div.dropzone-previews',
	acceptedFiles : 'image/*'
});
	</script>
-->
	</body>
</html>

