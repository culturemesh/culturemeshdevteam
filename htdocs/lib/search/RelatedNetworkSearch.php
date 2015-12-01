<?php
namespace search;

class RelatedNetworkSearch extends Search {

	private $origin;
	private $location;
	
	private $is_combined_search;
	private $combined_search;
	private $nearby_location_search;
	private $running_origin_search;
	private $nearby_origin_search;
	private $network_group_search;

	public function __construct($origin, $location) {

		$this->class_to_column = array(

			'dobj\City' => array(
				'table' => 'nearby_cities',
				'column' => 'city_id'),
			'dobj\Region' => array(
				'table' => 'nearby_regions',
				'column' => 'region_id'),
			'dobj\Country' => array(
				'table' => 'cities',
				'column' => 'id_country')
			);

		$this->origin = $origin;
		$this->location = $location;

		$origin_class = get_class($origin);
		$location_class = get_class($location);

		if ($origin_class == $location_class) {

			$this->is_combined_search = True;
			$this->running_origin_search = True;

			if ($origin_class == 'dobj\Country') {
				$this->combined_search = new PopulousCityGroupSearch( array($origin, $location) );
			}
			else {
				$this->combined_search = new NearbyGroupLocationSearch( array($origin, $location) );
			}
		}
		else {
			$this->is_combined_search = False;

			if ($location_class == 'dobj\Country') {
			  $this->nearby_location_search = new PopulousCitySearch($location);
			}
			else {
			   $this->nearby_location_search = new NearbyLocationSearch($location);
			}

			if ($origin_class == 'dobj\Country') {
			  $this->nearby_origin_search = new PopulousCitySearch($origin);
			}
			else if ($origin_class == 'dobj\Language') {

				// nothing
				$this->running_origin_search = False;
			}
			else {
			  $this->nearby_origin_search = new NearbyLocationSearch($origin);
			}
		}

	}

	public function run($dal, $do2db) {

		$results = NULL;

		if ($this->is_combined_search) {

			$group_results = $this->combined_search->run($dal, $do2db);

			$results = array(
				'location' => NULL,
				'origin' => NULL);

			// Run through these things and turn them into searchables
			// and sort them into location and origin
			//
			for ($i = 0; $i < count($group_results); $i++) {

				$result = $group_results[$i];

				// turn a thing into a searchable
				if (is_subclass_of($result, 'dobj\NearbyLocation')) {
					$group_results[$i] = $result->toSearchable();
				}
			}
		}
		else {
			// run both queries
			$results = array();
			$results['location'] = $this->nearby_location_search->run($dal, $do2db);

			var_dump($results['location']);

			if ($this->running_origin_search)
			  $results['origin'] = $this->nearby_origin_search->run($dal, $do2db);
			else
			  $results['origin'] = NULL;

			// Run through these things and turn them into searchables
			// and sort them into location and origin
			//
			for ($i = 0; $i < count($results['location']); $i++) {

				$result = $results['location'][$i];

				// turn a thing into a searchable
				if (is_subclass_of($result, 'dobj\NearbyLocation')) {
					$results['location'][$i] = $result->toSearchable();
				}
			}

			for ($i = 0; $i < count($results['origin']); $i++) {

				$result = $results['origin'][$i];

				// turn a thing into a searchable
				if (is_subclass_of($result, 'dobj\NearbyLocation')) {
					$results['origin'][$i] = $result->toSearchable();
				}
			}
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
		/*
		foreach ($locations_near_location as $l) {
			$network = new \dobj\Network();		
			$network->origin_searchable = $origin;
			$network->location_searchable = $l;

			array_push($networks, $network);
		}
		*/

		for ($i = 0; $i < 2; $i++) {

			$l = $locations_near_location[$i];

			$network = new \dobj\Network();		
			$network->origin_searchable = $origin;
			$network->location_searchable = $l;

			array_push($networks, $network);
		}

		/*
		// Handle the origins
		foreach ($locations_near_origin as $o) {
			$network = new \dobj\Network();		
			$network->origin_searchable = $o;
			$network->location_searchable = $location;

			array_push($networks, $network);
		}
		*/

		if ($locations_near_origin !== NULL) {

			for ($i = 0; $i < 2; $i++) {

				$o = $locations_near_origin[$i];

				$network = new \dobj\Network();		
				$network->origin_searchable = $o;
				$network->location_searchable = $location;

				array_push($networks, $network);
			}
		}

		return $networks;
	}
}

?>
