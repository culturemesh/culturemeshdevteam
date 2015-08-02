<?php
ini_set("display_errors", 1);

if (file_exists('zz341/fxn.php'))
	include_once("zz341/fxn.php");
if (file_exists('dal_query_handler.php'))
	include_once("dal_query_handler.php");


class Location
{

	public static function getAllCities($con = null)
	{
		$query = <<<SQL
			SELECT * FROM cities
SQL;

		$result = QueryHandler::executeQuery($query,$con);

		$cities = array();

		while ($row = mysqli_fetch_array($result)) {
			
			$city = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'region_id' => $row['region_id'],
				'region_name' => $row['region_name'],
				'country_id' => $row['country_id'],
				'country_name' => $row['country_name'],
				'population' => $row['population'],
				'feature_code' => $row['feature_code'],
				'latitude' => $row['latitude'],
				'longitude' => $row['longitude']
			);

			array_push($cities, $city);
		}

		return $cities;
	}
	
	public static function getAllRegions($con = null)
	{
		$query = <<<SQL
			SELECT * FROM regions 
SQL;

		$result = QueryHandler::executeQuery($query,$con);

		$regions = array();

		while ($row = mysqli_fetch_array($result)) {
			
			$region = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'country_id' => $row['country_id'],
				'country_name' => $row['country_name'],
				'population' => $row['population'],
				'feature_code' => $row['feature_code'],
				'latitude' => $row['latitude'],
				'longitude' => $row['longitude']
			);

			array_push($regions, $region);
		}

		return $regions;
	}

	public static function getAllCountries($con = null)
	{
		$query = <<<SQL
			SELECT * FROM countries
SQL;

		$result = QueryHandler::executeQuery($query,$con);

		$countries = array();

		while ($row = mysqli_fetch_array($result)) {
			
			$country = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'population' => $row['population'],
				'latitude' => $row['latitude'],
				'longitude' => $row['longitude']
			);

			array_push($countries, $country);
		}

		return $countries;
	}

	public static function findNearbyCities($city_targ, $con=NULL) {
	
		$cities = self::getAllCities($con);

		$lat1 = $city_targ['latitude'];
		$lat2 = $city_targ['longitude'];

		foreach ($cities as $city) {

			$city['latitude'];
			$city['longitude'];
		}

	}

	public static function getCity($name, $con = null)
	{
		$must_close = false;

		if ($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$result = mysqli_query($con, "SELECT id, name FROM cities
				WHERE name LIKE '{$name}'");

		if ($must_close)
			mysqli_close($con);

		if (!$result)
			return $con->error;

		else
			return $result;
	}

	public static function getRegion($name, $con = null)
	{
		$must_close = false;

		if ($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$result = mysqli_query($con, "SELECT id, name FROM regions
				WHERE name LIKE '{$name}'");

		if ($must_close)
			mysqli_close($con);

		if (!$result)
			return $con->error;

		else
			return $result;
	}

	public static function getCountry($name, $con = null)
	{
		$must_close = false;

		if ($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$result = mysqli_query($con, "SELECT id, name FROM countries 
				WHERE name LIKE '{$name}'");

		if ($must_close)
			mysqli_close($con);

		if (!$result)
			return $con->error;

		else
			return $result;
	}

	// take a lowercasestring, see if it matches anything
	// in the database
	public static function getLocation($term, $con=NULL)
	{
		// account for different data types
		$name = NULL;
		if(isset($term['value']))
			$name = $term['value'];
		else
			$name = $term;

		$limit = 1;
//////////////////////////////////////////////////////////////////
		
		$query1 = <<<SQL
			SELECT *
			FROM cities
			WHERE name LIKE '$name'
			ORDER BY population DESC
			LIMIT 0,$limit
SQL;

		$query2 = <<<SQL
			SELECT *
			FROM regions 
			WHERE name LIKE '$name'
			LIMIT 0,$limit
SQL;

		$query3 = <<<SQL
			SELECT *
			FROM countries
			WHERE name LIKE '$name'
			LIMIT 0,$limit
SQL;


//////////////////////////////////////////////////////////////////
		// execute queries
		// for now, return results
		if ($term['type'] == 'city')
			return QueryHandler::executeQuery($query1, $con);
		if ($term['type'] == 'region')
			return QueryHandler::executeQuery($query2, $con);
		if ($term['type'] == 'country')
			return QueryHandler::executeQuery($query3, $con);
	}

	public static function getNearbyCities($name, $con=NULL) 
	{
//////////////////////////////////////////////////////////////////
		
		$query = <<<SQL

			SELECT * FROM nearby_cities nc, cities c
			WHERE nc.neighbor_name=c.name
			AND nc.neighbor_id=c.id
			AND nc.city_name='$name'
			ORDER BY nc.distance
SQL;

//////////////////////////////////////////////////////////////////
		$result = QueryHandler::executeQuery($query, $con);
		return $result;
	}

	public static function getNearbyRegions($name, $con=NULL)
	{
		// query
		$query = <<<SQL

			SELECT * FROM nearby_regions nr, regions r
			WHERE nr.neighbor_name=r.name
			AND nr.neighbor_id=r.id
			AND nr.region_name='$name'
			ORDER BY distance

SQL;
		$result = QueryHandler::executeQuery($query, $con);
		return $result;	
	}

	public static function getRegionsByCountry($name, $con=NULL)
	{
		// query
		$query = <<<SQL

			SELECT *
			FROM regions
			WHERE country_name = '$name'
			LIMIT 0,5
SQL;
	//-----------------------------------------
		$result = QueryHandler::executeQuery($query, $con);
		return $result;	
	}

	public static function getCitiesByCountry($name, $con=NULL)
	{
		$query = <<<SQL

			SELECT *
			FROM cities
			WHERE country_name = '$name'
			ORDER BY population DESC
			LIMIT 0,5
SQL;
	//------------------------------------------------
		$result = QueryHandler::executeQuery($query, $con);
		return $result;
	}

	public static function getCitiesByRegion($name, $con=NULL)
	{
		$query = <<<SQL

			SELECT *
			FROM cities
			WHERE region_name = '$name'
			LIMIT 0,5
SQL;
	//-------------------------------------------------
		$result = QueryHandler::executeQuery($query, $con);
		return $result;
	}

	public static function getCCByName($name, $region_name, $country_name, $con=NULL)
	{

		$query = <<<SQL
			SELECT id, name, region_id, region_name, country_id, country_name
			FROM cities
			WHERE name='$name'
			AND region_name='$region_name'
			AND country_name='$country_name'
SQL;
///////////////
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$city = array($row['id'], $row['name'], 
			$row['region_id'], $row['region_name'],
			$row['country_id'], $row['country_name']);

		return $city;
	}

	public static function getCCByNameF($name, $region_name, $country_name, $con=NULL)
	{

		$query = <<<SQL
			SELECT *
			FROM cities
			WHERE name='$name'
			AND region_name='$region_name'
			AND country_name='$country_name'
SQL;
///////////////
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$city = array(
			'id' => $row['id'],
			'name' => $row['name'], 
			'region_id' => $row['region_id'], 
			'region_name' => $row['region_name'],
			'country_id' => $row['country_id'],
			'country_name' => $row['country_name'],
			'latitude' => $row['latitude'],
			'longitude' => $row['longitude'],
			'population' => $row['population'],
			'tweet_terms' => $row['tweet_terms'],
			'region_tweet_terms' => $row['region_tweet_terms'],
			'country_tweet_terms' => $row['country_tweet_terms']);

		return $city;
	}

	public static function getCCByNameR($name, $region_name, $country_name, $con=NULL)
	{
		$region_name = self::checkForNull($region_name);
		$country_name = self::checkForNull($country_name);

		$query = <<<SQL
			SELECT *
			FROM cities
			WHERE name='$name'
			AND region_name $region_name
			AND country_name $country_name
SQL;
///////////////
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$city = array($row['id'], $row['name'], 
			$row['region_id'], $row['region_name'],
			$row['country_id'], $row['country_name']);

		return $city;
	}

	public static function getRCByName($name, $country_name, $con=NULL)
	{
		$query = <<<SQL
			SELECT id, name, country_id, country_name
			FROM regions 
			WHERE name='$name'
			AND country_name='$country_name'
SQL;
///////////////
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$region = array(NULL, NULL, $row['id'], $row['name'], 
			$row['country_id'], $row['country_name']);

		return $region;
	}

	public static function getRCByNameF($name, $country_name, $con=NULL)
	{
		$query = <<<SQL
			SELECT *
			FROM regions 
			WHERE name='$name'
			AND country_name='$country_name'
SQL;
///////////////
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$region = array(
			'id' => $row['id'],
			'name' => $row['name'], 
			'country_id' => $row['country_id'],
			'country_name' => $row['country_name'],
			'latitude' => $row['latitude'],
			'longitude' => $row['longitude'],
			'population' => $row['population'],
			'tweet_terms' => $row['tweet_terms'],
			'country_tweet_terms' => $row['country_tweet_terms']);

		return $region;
	}

	public static function getRCByNameR($name, $country_name, $con=NULL)
	{
		$country_name = self::checkForNull($country_name);

		$query = <<<SQL
			SELECT id, name, country_id, country_name
			FROM regions 
			WHERE name='$name'
			AND country_name $country_name
SQL;
///////////////
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$region = array(NULL, NULL, $row['id'], $row['name'], 
			$row['country_id'], $row['country_name']);

		return $region;
	}

	public static function getCOByName($name, $con=NULL)
	{
		$query = <<<SQL
			SELECT id, name
			FROM countries 
			WHERE name='$name'
SQL;
//------------>
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$country = array(NULL, NULL, NULL, NULL, $row['id'], $row['name']);

		return $country;
	}

	public static function getCOByNameF($name, $con=NULL)
	{
		$query = <<<SQL
			SELECT * 
			FROM countries 
			WHERE name='$name'
SQL;
//------------>
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$country = array(
			'id' => $row['id'],
			'name' => $row['name'], 
			'latitude' => $row['latitude'],
			'longitude' => $row['longitude'],
			'population' => $row['population'],
			'tweet_terms' => $row['tweet_terms']);

		return $country;
	}

	public static function getCOByNameR($name, $con=NULL)
	{
		$query = <<<SQL
			SELECT id, name
			FROM countries 
			WHERE name='$name'
SQL;
//------------>
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$country = array(NULL, NULL, NULL, NULL, $row['id'], $row['name']);

		return $country;
	}

	///////////////////////////////////////////////////////
	// 		INSERTS
	// 	//////////////////////

	public static function insertCity($city, $con=NULL) {
		$query = <<<SQL
			INSERT INTO cities
			(name, latitude, longitude,
			region_id, region_name, country_id,
			country_name, population, added)
			VALUES
			('$city->city_name',
				$city->latitude, $city->longitude,
				$city->region_id, '$city->region_name',
				$city->country_id, '$city->country_name',
				$city->population, 1)
SQL;
//////////////	
		return QueryHandler::executeQuery($query, $con);
	}


	// nearby location stuff
	private static function haversine($lat1, $lat2, $lon1, $lon2) {
		
		$dLat = ($lat2-$lat1);
		$dLon = ($lon2-$lon1);

		$a = ((sin($dLat/2) * sin($dLat/2)) + (sin($dLon/2)*sin($dLon/2) * cos($lat1)*cos($lat2)));
		$c = 2*atan2(sqrt($a), sqrt(1-$a)); 
		return $c;
	}

	/* WILL BE TRUNCATED */

	public static function getNearbyStuffSingle($location) {
		// stuff
		$con = QueryHandler::getDBConnection();
		$result = QueryHandler::executeQuery($location->askForAll(), $con);

		$candidates = array();

		while ($row = $result->fetch_assoc())
		{
			// init array
			$candidate = array();

			$candidate['id'] = $row['id'];
			$candidate['name'] = $row['name'];
			$candidate['latitude'] = $row['latitude'];
			$candidate['longitude'] = $row['longitude'];

			$lat1 = deg2rad($location->getLatitude());
			$lat2 = deg2rad($candidate['latitude']);
			$lon1 = deg2rad($location->getLongitude());
			$lon2 = deg2rad($candidate['longitude']);

			// get distance
			$raw_dist = self::haversine($lat1, $lat2, $lon1, $lon2);
			$dist = $raw_dist * $conversion;

			// figure out if we'll keep the candidate
			foreach($location->search_dists as $sd) {
				if ($dist < $sd) {
					$candidate['distance'] = $sd;
					array_push($candidates, $candidate);
					break;
				}
			}
		}

		// now it's time to rank all the candidates
		// USING JAVASCRIPT PHP
		uasort($candidates, function() {
			if ($a['distance'] == $b['distance']) {
				return 0;
			}

			return ($a['distance'] < $b['distance']) ? -1 : 1;
		});

		$insert_batch = array(
			'table_name' => 'nearby_'.$location->getTblName(),
			// before VALUES
			'col_names' => array(
				'cols' => array(
					$location->getClass().'_id',
					$location->getClass().'_name',
					'neighbor_id',
					'neighbor_name'
				),
				'tcol' => 'distance'
			),
			// after VALUES
			'row_items' => array(
				'rows' => array(), // array of row values
				'trow' => NULL // single row value
			)
		);

		/*
		$row_values = array(
			'values' => array(
				'vals' => array(),
				'tval' => NULL
			)
		);
		 */

		$insert_batch['table_name'] = 'nearby_'.$location->getTblName();

		for ($i = 0; $i < 5; $i++) {
			$row_values = array(
				'values' => array(
					'vals' => array(),
					'tval' => $candidate['distance']
				)
			);

			array_push($row_values['values']['vals'], $location['id']);
			array_push($row_values['values']['vals'], $location['name']);
			array_push($row_values['values']['vals'], $candidate['id']);
			array_push($row_values['values']['vals'], $candidate['name']);
		}

		$m = Mustache_Engine;

		$template = file_get_contents('templates/sql-insert.sql');
		$query = $m->render($template, $insert_batch);

		return QueryHandler::executeQuery($query, $con);
	}

	public static function getNearbyStuff($data, $con=NULL) {
		// so no matter what, we'll have an array
		// of locations
		if (!is_array($data)) {
			$data = array($data);
		}
	
		// needs to be declared here for
		// some reason
		$conversion = 3959; // radius of earth in miles

//		$con = QueryHandler::getDBConnection();

		$cities = Location::getAllCities($con);
		$regions = Location::getAllRegions($con);
		$countries = Location::getAllCountries($con);

		$rows = array();

		foreach ($data as $location) {

			$cur_list = NULL;

			$candidates = array();

			// figure out which list we're going to need
			switch($location->getClass()) {
			case 'city':
				$cur_list = $cities;
				break;
			case 'region':
				$cur_list = $regions;
				break;
			case 'country':
				$cur_list = $countries;
				break;
			}


			// compute the distance of each candidate
			foreach ($cur_list as $candidate)
			{
				// skip any duplicates
				if ($candidate['id'] == $location->getId())
					continue;

				$lat1 = deg2rad($location->getLatitude());
				$lat2 = deg2rad($candidate['latitude']);
				$lon1 = deg2rad($location->getLongitude());
				$lon2 = deg2rad($candidate['longitude']);

				// get distance
				$raw_dist = self::haversine($lat1, $lat2, $lon1, $lon2);
				$dist = $raw_dist * $conversion;

				/*
				if ($candidate['name'] == 'New York City') {
					echo $lat1 .' '. $lat2.' '.$lon1.' '.$lon2."\n";
					echo $candidate['name'];
					echo "\n";
					echo $raw_dist;
					echo "\n";
					echo $dist;
					echo "\n";
					break;
				}
				 */

				// figure out if we'll keep the candidate
				foreach($location->search_dists as $sd) {
					if ($dist < $sd) {
						$candidate['dist_level'] = $sd;
						$candidate['distance'] = $dist;
						array_push($candidates, $candidate);
						break;
					}
				}
			}

//			echo count($candidates);

			// now it's time to rank all the candidates
			// USING JAVASCRIPT PHP
			usort($candidates, function($a, $b) {
				if ($a['distance'] == $b['distance']) {
					return 0;
				}

				return ($a['distance'] < $b['distance']) ? -1 : 1;
			});

			for ($i = 0; $i < 5; $i++) {

				// stop if we're out of candidates
				if ($candidates[$i]['id'] == NULL)
					break;


				$row = array(
					'values' => array(
						'vals'=> array(
							$location->getId(),
							"'".$location->getName()."'",
							$candidates[$i]['id'],
							"'".$candidates[$i]['name']."'",
							$candidates[$i]['dist_level']
						),
						'tval'=> $candidates[$i]['distance']
					)
				);

				array_push($rows, $row);
			}

		}

		/////////////////////////
		// out of the loop, building query
		//

		// count # of rows
		$split = count($rows);

		$insert_batch = array(
			'table_name' => 'nearby_'.$location->getTblName(),
			// before VALUES
			'col_names' => array(
				'cols' => array(
					$location->getClass().'_id',
					$location->getClass().'_name',
					'neighbor_id',
					'neighbor_name',
					'dist_level'
				),
				'tcol' => 'distance'
			),
			// after VALUES
			'row_items' => array(
				'rows' => array_slice($rows, 0, $split-1), // array of row values
				'trow' =>  $rows[$split-1] // single row value
			)
		);

		/*
		$row_values = array(
			'values' => array(
				'vals' => array(),
				'tval' => NULL
			)
		);
		 */

		// Make us proud, mustache
		$m = new Mustache_Engine;
		$template = file_get_contents('../templates/sql-insert.sql');

		$query = $m->render($template, $insert_batch);

		// fantastic, now execute query
		return QueryHandler::executeQuery($query, $con);
	}

	public static function deleteNearbyStuff($id, $table, $class, $con=NULL) {

		// important terms
		$ntable = 'nearby_'.$table;
		$nclass = $class.'_id';

		$query = <<<SQL
			DELETE FROM $ntable
			WHERE $nclass = $id
SQL;

		return QueryHandler::executeQuery($query, $con);
	}

	public static function updateNetworkNames($id, $name, $class, $con=NULL) { 

		// important names
		$ncur = $class.'_cur'; // eg city_cur
		$norg = $class.'_origin'; // eg city_origin
		$icur = 'id_'.$class.'_cur'; // eg id_city_cur
		$iorg = 'id_'.$class.'_origin'; // eg id_city_origin

		$query = <<<SQL
			UPDATE networks
				SET $ncur = CASE
				WHEN $icur = $id THEN '$name'
				ELSE $ncur
				END,
				$norg = CASE
				WHEN $iorg = $id THEN '$name'
				ELSE $norg
				END
SQL;

		return QueryHandler::executeQuery($query, $con);

	}

	public static function updateNetworkParent($id, $class, $pid, $pname, $pclass, $con=NULL) {

		// important names
		$icur = 'id_'.$class.'_cur'; 
		$iorg = 'id_'.$class.'_origin';
		$pncur = $pclass.'_cur'; // eg city_cur
		$pnorg = $pclass.'_origin'; // eg city_origin
		$picur = 'id_'.$pclass.'_cur'; // eg id_city_cur
		$piorg = 'id_'.$pclass.'_origin'; // eg id_city_origin

		$query = <<<SQL
			UPDATE networks
				SET $pncur = CASE
				WHEN $icur = $id THEN '$pname'
				ELSE $pncur
				END,
				$pnorg = CASE
				WHEN $iorg = $id THEN '$pname'
				ELSE $pnorg
				END,
				$picur = CASE
				WHEN $icur = $id THEN $pid
				ELSE $picur
				END,
				$piorg = CASE
				WHEN $iorg = $id THEN $pid
				ELSE $piorg
				END
SQL;

		return QueryHandler::executeQuery($query, $con);
	}

	public static function updateChildrenNames($id, $name, $table, $class, $con=NULL) {
			// important names
		$icl = $class.'_id'; // eg id_city_cur
		$ncl = $class.'_name';

		$query = <<<SQL
			UPDATE $table 
			SET $ncl = '$name'
			WHERE $icl = $id
SQL;

		return QueryHandler::executeQuery($query, $con);
	}

	public static function updateRegionChildrenTweetNames($id, $terms, $con=NULL) {
		/*
			// important names
		$icl = $class.'_id'; // eg id_city_cur
		$ncl = $class.'_name';
		 */

		$term_string = $terms;

		// add quotes if the string is NOT NULL
		if ($terms != "NULL") {
			$term_string = '\'' . $term_string . '\'';
		}

		$query = <<<SQL
			UPDATE cities 
			SET region_tweet_terms = $term_string
			WHERE region_id = $id
SQL;

		return QueryHandler::executeQuery($query, $con);
	}

	public static function updateCountryChildrenTweetNames($id, $terms, $con=NULL) {
			// important names
		$icl = 'country_id'; 
		$ncl = 'country_tweet_terms';

		$term_string = $terms;

		// add quotes if the string is NOT NULL
		if ($terms != "NULL") {
			$term_string = '\'' . $term_string . '\'';
		}

		$query = <<<SQL
			UPDATE regions, cities
			SET regions.$ncl = $term_string,
			cities.$ncl = $term_string
			WHERE cities.country_id = regions.country_id
			AND regions.country_id = $id
SQL;

		return QueryHandler::executeQuery($query, $con);
	}

	private static function checkForNull($name) {

		if ($name == 'NULL')
			return 'IS NULL';
		else
			return "='{$name}'";
	}
}
?>
