<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

include_once("data/dal_network.php");
include_once("data/dal_network-dt.php");
include_once("data/dal_post.php");
include_once("data/dal_network_registration.php");
include_once("data/dal_location.php");
include_once("data/dal_language.php");

/* Could perhaps be a query object, constructed with three parameters*/
class SearchQuery
{
	/*
	public static function getNetworkSearchResults($topic, $query_str, $location)
	{
		$networks = array();
		
		switch($topic)
		{
		case "language":	// language
			$results = Network::getNetworksByLanguage($query_str);
			break;
		case "origin":	// region
			$results = Network::getNetworksByOrigin($query_str);
			break;
		}
		
		while ($row = mysqli_fetch_array($results))
		{
			$network_dt = new NetworkDT();
			
			$network_dt->id = $row['id'];
			$network_dt->city_cur = $row['city_cur'];
			$network_dt->region_cur = $row['region_cur'];
			$network_dt->city_origin = $row['city_origin'];
			$network_dt->region_origin = $row['region_origin'];
			$network_dt->country_origin = $row['country_origin'];
			$network_dt->language_origin = $row['language_origin'];
			$network_dt->network_class = $row['network_class'];
			$network_dt->member_count = NetworkRegistration::getMemberCount($network_dt->id);
			$network_dt->post_count = Post::getPostCount($network_dt->id);
			
			array_push($networks, $network_dt);
		}
		
		return $networks;
	}
	 */

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
				echo 'here';
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
			echo '<br><br>';

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
