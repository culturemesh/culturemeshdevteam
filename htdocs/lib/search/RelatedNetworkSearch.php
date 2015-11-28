<?php
namespace search;

class RelatedNetworkSearch extends Search {

	private $origin;
	private $location;
	
	private $is_combined_search;
	private $combined_search;
	private $nearby_location_search;
	private $nearby_origin_search;
	private $network_group_search;

	public function __construct($origin, $location) {

		$this->class_to_column = array(

			'city' => array(
				'table' => 'nearby_cities',
				'column' => 'city_id'),
			'region' => array(
				'table' => 'nearby_regions',
				'column' => 'region_id'),
			'country' => array(
				'table' => 'nearby_countries',
				'column' => 'country_id')
			);

		$this->origin = $origin;
		$this->location = $location;

		if (get_class($origin) == get_class($location)) {
			$this->is_combined_search = True;
			$this->combined_search = new NearbyGroupLocationSearch($origin, $location);
		}
		else {
			$this->is_combined_search = False;
			$this->nearby_location_search = new NearbyLocationSearch($location);
			$this->nearby_origin_search = new NearbyLocationSearch($origin);
		}

	}

	public function run($dal, $do2db) {

		$results = NULL;

		if ($this->is_combined_search) {
			$results = $this->combined_search->run($dal, $do2db);
		}
		else {
			// run both queries
			$results = array();
			$results['location'] = $this->nearby_location_search->run($dal, $do2db);
			$results['origin'] = $this->nearby_origin_search->run($dal, $do2db);
		}

		// make networks
		$networks = $this->determineNetworks($this->origin, $results['origin'], $this->location, $results['location']);
		$this->network_group_search = new NetworkGroupSearch($networks);

		return $this->network_group_search->run($dal, $do2db);
	}

	/*
	 * Given the original searchables and a list of nearby search results,
	 * this function determines how these suckas make networks
	 *
	 * The usual way...
	 * 	search_origin + (2 nearby locations)
	 * 	(2 nearby origins) + search_location
	 */
	private function determineNetworks($origin, $locations_near_origin, $location, $locations_near_location) {

		$networks = array();

		// Handle the locations
		foreach ($locations_near_location as $l) {
			$network = new \dobj\Network();		
			$network->origin_searchable = $origin;
			$network->location_searchable = $l;

			array_push($networks, $network);
		}

		// Handle the origins
		foreach ($locations_near_origin as $o) {
			$network = new \dobj\Network();		
			$network->origin_searchable = $o;
			$network->location_searchable = $location;

			array_push($networks, $network);
		}

		return $networks;
	}
}

?>
