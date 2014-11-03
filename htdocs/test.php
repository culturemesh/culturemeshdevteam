<?php
ini_set('display_errors', true);
/*
	include_once("data/dal_location.php");
	include_once("data/dal_query_handler.php");
	include_once("data/dal_network.php");
 */
?>
<html>
	<head>
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

echo __DIR__;
?>
	</body>
</html>
