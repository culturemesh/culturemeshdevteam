<?php
	ini_set("display_errors", true);
	include_once("data/dal_location.php");
	include_once("data/dal_query_handler.php");
	include_once("data/dal_network.php");
?>
<html>
	<head>
	</head>
	<body>
<?php
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
?>
	</body>
</html>
