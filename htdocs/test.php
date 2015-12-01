<?php
ini_set('display_errors', true);
include('environment.php');
$cm = new Environment();
$cm->displayErrors();
$dal = NULL;
$do2db = NULL;
$cm->enableDatabase($dal, $do2db);

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

$cm->closeConnection();

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

