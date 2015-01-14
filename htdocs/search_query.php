<?php
//ini_set('display_errors', true);
//error_reporting(E_ALL ^ E_NOTICE);

include_once("data/dal_network.php");
include_once("data/dal_network-dt.php");
include_once("data/dal_post.php");
include_once("data/dal_network_registration.php");
include_once("data/dal_location.php");
include_once("data/dal_language.php");

/* Could perhaps be a query object, constructed with three parameters*/
class SearchQuery
{
	private static $exceptions = array(
		'Washington, D.C., United States' => array('Washington, D.C.', 'NULL', 'United States')
	);

	private static function minimumCandidate($candidates) {
		// stop if no candidates can be found
		if (count($candidates) == 0)
			return false;

		$min = $candidates[0];
		for ($i = 0; $i < count($candidates); $i++) {
			if ($min['distance'] > $candidates[$i]['distance'])
				$min = $candidates[$i];
		}

		return $min;
	}

	// goes through a file line by line
	// returns  a list of candidates, 
	// 	or returns a match
	private static function checkFile($filename, $input, $type) {
		// get file lines
		$lines = file($filename);

		// initialize i, and candidates
		$candidates = array();
		$i = 0;

		for (; $i < count($lines); $i++) {
			// strip whitespace that
			// shows up for no reason
			$line = trim($lines[$i]);

			// get distance
			$distance = levenshtein($input, $line);

			// do stuff based on distance
			if ($distance == 0){
				$match = true;
				break;
			}
			else if ($distance < 4)
				array_push($candidates, array(
					'name' => $name,
					'distance' => $distance,
					'type' => $type));

		}

		// return if match
		if($match == true)
			return array(
				'type' => $type,
				'match' => true,
				'value' => trim($lines[$i]));

		return $candidates;
	}

	private static function salvageInput($input, $verb)
	{
		// lowercase input, get rid of commas
		$input = strtolower($input);
		$input = str_replace(',', '', $input);

		// separate based on type
		if ($verb == 'arefrom') {
			// check cities
			$r1 = self::checkFile('data/s_citynames.txt', $input, 'city');

			if ($r1['match'] == true)
				return $r1;

			// check regions
			$r2 = self::checkFile('data/s_regionnames.txt', $input, 'region');

			if ($r2['match'] == true)
				return $r2;

			// check countries
			$r3 = self::checkFile('data/s_countrynames.txt', $input, 'country');

			if ($r3['match'] == true)
				return $r3;

			// return minimum of all arrays,
			// 	-- if we made it this far
			return self::minimumCandidate(array_merge($r1, $r2, $r3));
		}
		if ($verb == 'speak') {

			$results = self::checkFile('data/s_langnames.txt', $input, 'language');
			return minimumCandidates($results);
		}
	}


	private static function figureLocationType($values) {
		// country
		if(count($values) == 1)
		{
			return array('co', NULL, NULL, $values[0]);
		}
		// region
		if (count($values) == 2)
		{
			return array('rc', NULL, $values[0], $values[1]);
		}

		// city
		if (count($values) == 3)
		{
			return array('cc', $values[0], $values[1], $values[2]);
		}
	}

	private static function handleGoodInput($input, $verb=NULL)
	{
		// if it's a language...easy
		if ($verb == 'speak')
			// return array with class
			return array('_l', $input);

		// else it's a location
		if (array_key_exists($input, self::$exceptions)) {
			$raw_values = self::$exceptions[$input];
		}
		else {
			$raw_values = explode(', ', $input);
		}

		return self::figureLocationType($raw_values);
	}

	private static function resultRowsToNumArray($values, $type) {
		if ($type == 'location') {
			$array = array(NULL, NULL, NULL, NULL);

			// city
			if (isset($values['region_name'])
				&& isset($values['country_name'])) {
				// set array
				$array[0] = 'cc';
				$array[1] = $values['name'];
				$array[2] = $values['region_name'];
				$array[3] = $values['country_name'];
			}
			// region
			else if (isset($values['country_name'])) {
				$array[0] = 'rc';
				$array[2] = $values['name'];
				$array[3] = $values['country_name'];
			}
			else
			{
				$array[0] = 'co';
				$array[3] = $values['name'];
			}

			// return array
			return $array; 
		}

		if ($type == 'language') {
			// initialize language array
			return array('_l', $values['name']);
		}
	}


	private static function fillQuery($query, $data, $type)
	{
		// check for null values
		for ($i = 0; $i < count($data); $i++) {
			if ($data[$i] == 'NULL') {
				$data[$i] = '';
			}
		}

		// add types n shit
		if ($type == 'query') {
			$query[0] = $data[0];
			$query[4] = $data[1];
			$query[5] = $data[2];
			$query[6] = $data[3];
		}
		if ($type == 'location') {
			$query[1] = $data[1];
			$query[2] = $data[2];
			$query[3] = $data[3];
		}

		return $query;
	}

	public static function buildQuery($search1, $search2,
					$topic, $verb,
					$clik1, $clik2, $con)
	{
		// SAAALLLVAAGGEEE
		$input_1 = NULL;
		$input_2 = NULL;

		/////////////////////////////////
		// check clik1
		//  if manual input
		//  special case, click 1 is the query
		if ($clik1 == 0) {
			$input_1 = self::salvageInput($search1, $verb);

			// if we could salvage input, continue
			if ($input_1) {
				// talk to database about gettin the full story
				// will probably simplify to something else
				$candidate = NULL;
				if ($verb == 'arefrom') {
					$candidate = Location::getLocation($input_1, $con);
					// get rows
					$input_1 = QueryHandler::getRows($candidate);
					$input_1 = self::resultRowsToNumArray($input_1[0], 'location');
				}
				if ($verb == 'speak') {
					$candidate = Language::getLanguage($input_1, $con);
					// get rows
					$input_1 = QueryHandler::getRows($candidate);
					$input_1 = self::resultRowsToNumArray($input_1[0], 'language');
				}
			}
		}
		else {
			$input_1 = self::handleGoodInput($search1, $verb);
		}

		//////////////////////////////
		//  check clik2
		//  //////////
		if ($clik2 == 0) {
			$input_2 = self::salvageInput($search2, $verb);

			// if we could salvage input
			if ($input_2) {
				$candidate = Location::getLocation($input_2, $con);
				$input_2 = QueryHandler::getRows($candidate);
				$input_2 = self::resultRowsToNumArray($input_2[0], 'location');
			}
		}
		else {
			$input_2 = self::handleGoodInput($search2);
		}

		///////////////////////////////////
		// we should have all the good nooch
		// //////////
		$query = self::fillQuery($query, $input_1, 'query');
		$query = self::fillQuery($query, $input_2, 'location');

		// return
		return array($query, $input_1, $input_2);
	}

	public static function getNetworkSearchResults($query, $con)
	{
		//var_dump($query);
		$results = null;
		$org_result_count = 2;
		$loc_result_count = 2;
		// topic
		switch ($query[0])
		{
		// HUNT DOWN A NETWORK FOR THE USERS!!!!
		case "co":
			// [1: o_country, 2: city_cur, 3: region_cur, 4: country_cur] in the future, region as well
			// [1: city_cur, 2: region_cur, 3: country_cur, 6: o_country]
			$results = Network::getNetworksByCO($query, $con);
			//var_dump($row);
			break;
		case "cc":
			// [1: o_city, 2: o_country, 3: city_cur, 4: region_cur, 5: country_cur]
			$results = Network::getNetworksByCC($query, $con);
			break;
		case "rc":
			// [1: o_region, 2: o_country, 3: city_cur, 4: region_cur, 5: country_cur]
			$results = Network::getNetworksByRC($query, $con);
			break;
		case "_l":
			// [1: o_language, 2: city_cur, 3: region_cur, 4: country_cur]
			$results = Network::getNetworksByL($query, $con);
			break;
		default:
			return array("Invalid query");
		}

		// push everything into an array!!
		$networks = array();

		if (mysqli_num_rows($results) > 0) {
			while ($row = mysqli_fetch_array($results))
			{
				$network_dt = new NetworkDT();
				
				$network_dt->id = $row['id'];
				$network_dt->city_cur = $row['city_cur'];
				$network_dt->region_cur = $row['region_cur'];
				$network_dt->country_cur = $row['country_cur'];
				$network_dt->city_origin = $row['city_origin'];
				$network_dt->region_origin = $row['region_origin'];
				$network_dt->country_origin = $row['country_origin'];
				$network_dt->language_origin = $row['language_origin'];
				$network_dt->network_class = $row['network_class'];
				$network_dt->member_count = NetworkRegistration::getMemberCount($network_dt->id);
				$network_dt->post_count = Post::getPostCount($network_dt->id);
				$network_dt->existing = true;
				
				array_push($networks, $network_dt);

				// ensure we only do it once, in case of duplicates
				break;
			}
		}
		else
		{
			$network_dt = Network::fillDTWithQuery($alt_query);
			array_push($networks, $network_dt);
		}

		//var_dump($query);
		// GET CURRENT LOCATION THINGS
		if ($query[1] != null)
		{
			// pull from nearby cities
			$cities = Location::getNearbyCities($query[1], $con);
			$cities_nm = QueryHandler::getRows($cities);
			$alt_query = $query;

			// get from two origin cities
			for ($i = 0; $i < $org_result_count && $i < count($cities_nm); $i++)
			{
				$alt_query[1] = $cities_nm[$i]['name'];
				$alt_query[2] = $cities_nm[$i]['region_name'];
				$alt_query[3] = $cities_nm[$i]['country_name'];
				$results = Network::getNetworksAllClasses($alt_query, $con);
				if (mysqli_num_rows($results) > 0)
				  { $network_dt = Network::fillNetworkDT($results, $con); }
				else
				  { $network_dt = Network::fillDTWithQuery($alt_query); }

				array_push($networks, $network_dt);
			}
		}
		else if ($query[2] != null)
		{
			// pull from nearby regions
			$regions = Location::getNearbyRegions($query[2], $con);
			$regions_nm = QueryHandler::getRows($regions);
			$alt_query = $query;

			// get from two origin regions 
			for ($i = 0; $i < $org_result_count && $i < count($regions_nm); $i++)
			{
				$alt_query[2] = $regions_nm[$i]['name'];
				$alt_query[3] = $regions_nm[$i]['country_name'];

				$results = Network::getNetworksAllClasses($alt_query, $con);
				if (mysqli_num_rows($results) > 0)
				  { $network_dt = Network::fillNetworkDT($results, $con); }
				else
				  { $network_dt = Network::fillDTWithQuery($alt_query); }

				array_push($networks, $network_dt);
			}
		}
		else
		{
			// get cities by country
			$cities = Location::getCitiesByCountry($query[3], $con);
			$cities_nm = QueryHandler::getRows($cities);
			$alt_query = $query;

			// get from two origin cities within
			for ($i = 0; $i < $org_result_count && $i < count($cities_nm); $i++)
			{
				$alt_query[1] = $cities_nm[$i]['name'];
				$alt_query[2] = mysqli_real_escape_string($con, $cities_nm[$i]['region_name']);
				$alt_query[3] = $cities_nm[$i]['country_name'];

				$results = Network::getNetworksAllClasses($alt_query, $con);
				if (mysqli_num_rows($results) > 0)
				  { $network_dt = Network::fillNetworkDT($results, $con); }
				else
				  { $network_dt = Network::fillDTWithQuery($alt_query); }

				array_push($networks, $network_dt);
			}
			/*
			// pull from regions within country
			$regions = Location::getRegionsByCountry($query[3], $con);
			$regions_nm = QueryHandler::getRows($regions);
			$alt_query = $query;

			// get from two origin regions within
			for ($i = 0; $i < $org_result_count && $i < count($regions_nm); $i++)
			{
				$alt_query[2] = $regions_nm[$i]["name"];
				$alt_query[3] = $regions_nm[$i]["country_name"];
				$results = Network::getNetworksAllClasses($alt_query, $con);
				if (mysqli_num_rows($results) > 0)
				  { $network_dt = Network::fillNetworkDT($results, $con); }
				else
				  { $network_dt = Network::fillDTWithQuery($alt_query); }

				array_push($networks, $network_dt);
			}
			 */
		}

		// get nearby data here
		//  - origin country
		//  	- 2 from origin regions within
		//  	- 2 from current regions within
		//  - current location
		switch ($query[0])
		{
		// GET COUNTRY STUFF
		case "co":
			// get cities by country
			$cities = Location::getCitiesByCountry($query[6], $con);
			$cities_nm = QueryHandler::getRows($cities);
			$alt_query = $query;

			// get from two origin cities within
			for ($i = 0; $i < $org_result_count && $i < count($cities_nm); $i++)
			{
				$alt_query[0] = 'cc';
				$alt_query[4] = $cities_nm[$i]['name'];
				$alt_query[5] = mysqli_real_escape_string($con, $cities_nm[$i]['region_name']);
				$alt_query[6] = $cities_nm[$i]['country_name'];

				$results = Network::getNetworksAllClasses($alt_query, $con);
				if (mysqli_num_rows($results) > 0)
				  { $network_dt = Network::fillNetworkDT($results, $con); }
				else
				  { $network_dt = Network::fillDTWithQuery($alt_query); }

				array_push($networks, $network_dt);
			}
			// get regions by country
			/*
			$regions = Location::getRegionsByCountry($query[6], $con);
			$regions_nm = QueryHandler::getRows($regions);
			$alt_query = $query;

			// get from two origin regions within
			for ($i = 0; $i < $org_result_count && $i < count($regions_nm); $i++)
			{
				$alt_query[0] = 'rc';
				$alt_query[5] = $regions_nm[$i]['name'];
				$alt_query[6] = $regions_nm[$i]['country_name'];

				$results = Network::getNetworksByRC($alt_query, $con);
				if (mysqli_num_rows($results) > 0)
				  { $network_dt = Network::fillNetworkDT($results, $con); }
				else
				  { $network_dt = Network::fillDTWithQuery($alt_query); }

				array_push($networks, $network_dt);
			}

			 */

			break;

		// GET ORIGIN CITY STUFF
		case "cc":
			// get nearby cities
			$cities = Location::getNearbyCities($query[4], $con);
			$cities_nm = QueryHandler::getRows($cities);
			$alt_query = $query;

			// get from two origin cities
			for ($i = 0; $i < $org_result_count && $i < count($cities_nm); $i++)
			{

				// reset query stuff
				$alt_query[4] = $cities_nm[$i]['name'];
				$alt_query[5] = $cities_nm[$i]['region_name'];
				$alt_query[6] = $cities_nm[$i]['country_name'];
				$results = Network::getNetworksByCC($alt_query, $con);
				if (mysqli_num_rows($results) > 0)
				  { $network_dt = Network::fillNetworkDT($results); }
				else
				  { $network_dt = Network::fillDTWithQuery($alt_query); }

				array_push($networks, $network_dt);
			}
			break;
		// GET ORIGIN REGION STUFF
		case "rc":
			// get nearby regions
			$regions = Location::getNearbyRegions($query[5], $con);
			$regions_nm = QueryHandler::getRows($regions);
			$alt_query = $query;

			// get from two origin regions 
			for ($i = 0; $i < $org_result_count && $i < count($regions_nm); $i++)
			{
				$alt_query[5] = $regions_nm[$i]['name'];
				$alt_query[6] = $regions_nm[$i]['country_name'];

				$results = Network::getNetworksByRC($alt_query, $con);
				if (mysqli_num_rows($results) > 0)
				  { $network_dt = Network::fillNetworkDT($results, $con); }
				else
				  { $network_dt = Network::fillDTWithQuery($alt_query); }

				array_push($networks, $network_dt);
			}
			break;
		// GET LANGUAGE STUFF
		case "_l":
			// tis none
			break;
		default:
			break;
		}

		return $networks;
	}
	// make sure language is in database
	// ONLY LEGIT THINGS ALLOWED
	// 	- later, add checks for alternate spellings
	public static function checkValue($value, $prompt, $con)
	{
		switch ($prompt)
		{
		case "language":
			$result = Language::getLanguage($value, $con);
			break;
		case "country":
			$result = Location::getCountry($value, $con);
			break;
		case "city/region":
			$result = Location::getCity($value, $con);
			if ($result->num_rows == 0)
			  { $result = Location::getRegion($value, $con); }
			break;
		}

		if ($result->num_rows == 0)
		  { return false; }
		else
		  { return true; }
	}
}
?>
