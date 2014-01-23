<?php

include_once("data/dal_network.php");
include_once("data/dal_network-dt.php");
include_once("data/dal_post.php");
include_once("data/dal_network_registration.php");

/* Could perhaps be a query object, constructed with three parameters*/
class SearchQuery
{
	public static function getNetworkSearchResults($topic, $query_str, $location)
	{
		$networks = array();
		
		switch($topic)
		{
		case "language":
			$results = Network::getNetworksByLanguage($query_str);
			break;
		case "origin":
			$results = Network::getNetworksByCountry($query_str);
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
}
?>
