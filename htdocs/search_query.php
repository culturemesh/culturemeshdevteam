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
		$result = null;
		// topic
		switch ($query[0])
		{
		// HUNT DOWN A NETWORK FOR THE USERS!!!!
		case "co":
			// [1: o_country, 2: city_cur, 3: region_cur, 4: country_cur] in the future, region as well
			$results = Network::getNetworksByCO($query, $con);
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
			return array();
		}
		// get nearby cities here
		//

		// push everything into an array!!
		$networks = array();

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
			
			array_push($networks, $network_dt);
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
