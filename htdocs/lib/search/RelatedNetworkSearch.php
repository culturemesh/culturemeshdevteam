<?php
namespace search;

class RelatedNetworkSearch extends Search {

	private $origin;
	private $origin_class;
	private $location;
	private $location_class;
	
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

		$this->origin_class = get_class($origin);
		$this->location_class = get_class($location);

		if ($this->origin_class == $this->location_class &&
			(!in_array('dobj\Country', array($this->location_class, $this->origin_class)))) {

			$this->is_combined_search = True;
			$this->running_origin_search = True;

			// This is inconsequential, grouping these is too much work at the moment
			if ($this->origin_class == 'dobj\Country') {
				//$this->combined_search = new PopulousCityGroupSearch( array($origin, $location) );
				$this->combined_search = new NearbyGroupLocationSearch( array($origin, $location) );
			}
			else {
				$this->combined_search = new NearbyGroupLocationSearch( array($origin, $location) );
			}
		}
		else {
			$this->is_combined_search = False;
			$this->running_origin_search = True;

			if ($this->location_class == 'dobj\Country') {
			  $this->nearby_location_search = new PopulousCitySearch($location);
			}
			else {
			   $this->nearby_location_search = new NearbyLocationSearch($location);
			}

			if ($this->origin_class == 'dobj\Country') {
			  $this->nearby_origin_search = new PopulousCitySearch($origin);
			}
			else if ($this->origin_class == 'dobj\Language') {

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

		$c_to_c = $this->class_to_column[ $this->origin_class ];

		if ($this->is_combined_search) {

			$group_results = $this->combined_search->run($dal, $do2db);

			$results = array(
				'location' => array(),
				'origin' => array());

			// Run through these things and turn them into searchables
			// and sort them into location and origin
			//
			for ($i = 0; $i < count($group_results); $i++) {

				$result = $group_results[$i];

				// turn a thing into a searchable
				if (is_subclass_of($result, 'dobj\NearbyLocation')) {

					if ($result->isNeighborTo($this->origin)) {
						array_push($results['origin'], $result->toSearchable($this->origin));
					}
					else if ($result->isNeighborTo($this->location)) {
						array_push($results['location'], $result->toSearchable($this->location));
					}
				}
				else {
					$var = $c_to_c['column'];

					if ( $this->origin->id == $result->$var)
					  array_push($results['origin'], $result);
					else
					  array_push($results['location'], $result);
				}
			}
		}
		else {
			// run both queries
			$results = array();
			$results['location'] = $this->nearby_location_search->run($dal, $do2db);

			if ($this->running_origin_search)
			  $results['origin'] = $this->nearby_origin_search->run($dal, $do2db);
			else
			  $results['origin'] = array();

			// Run through these things and turn them into searchables
			// and sort them into location and origin
			//
			for ($i = 0; $i < count($results['location']); $i++) {

				$result = $results['location'][$i];

				// turn a thing into a searchable
				if (is_subclass_of($result, 'dobj\NearbyLocation')) {
					$results['location'][$i] = $result->toSearchable($this->location);
				}
			}

			for ($i = 0; $i < count($results['origin']); $i++) {

				$result = $results['origin'][$i];

				// turn a thing into a searchable
				if (is_subclass_of($result, 'dobj\NearbyLocation')) {
					$results['origin'][$i] = $result->toSearchable($this->origin);
				}
			}
		}

		// make networks
		$networks = $this->createPossibleNetworks($this->origin, $results['origin'], $this->location, $results['location']);

		// create a hash of possible network titles
		$network_hash = array();

		// populate networks with possible things
		foreach ($networks as $network) {
			$network->existing = False;
			$network_hash[$network->getTitle()] = $network;
		}

		// Run network group search
		$this->network_group_search = new NetworkGroupSearch($networks);
		$final_results = $this->network_group_search->run($dal, $do2db);

		//var_dump($final_results);

		if ($final_results !== False) {

			foreach($final_results as $existing_network) {

				// set network to existing
				$existing_network->existing = True;
				$title = $existing_network->getTitle();

				// replace possible network with existing one
				$network_hash[$title] = $existing_network;
			}
		}

		return array_values( $network_hash );
	}

	/*
	 * Given the original searchables and a list of nearby search results,
	 * this function determines how these suckas make networks
	 *
	 * The usual way...
	 * 	search_origin + (2 nearby locations)
	 * 	(2 nearby origins) + search_location
	 */
	private function createPossibleNetworks($origin, $locations_near_origin, $location, $locations_near_location) {

		$networks = array();

		// Handle the locations
		// 
		if ($locations_near_location !== NULL) {

			// also, if it's a non empty array
			//
			if (count($locations_near_location) > 0) {

				for ($i = 0; $i < 2; $i++) {

					$l = $locations_near_location[$i];

					$network = new \dobj\Network();		
					$network->origin_searchable = $origin;
					$network->location_searchable = $l;

					array_push($networks, $network);
				}
			}
		}

		// Handle the origins
		//
		if ($locations_near_origin !== NULL) {

			// also, if it's a non empty array
			//
			if (count($locations_near_origin) > 0) {

				for ($i = 0; $i < 2; $i++) {

					$o = $locations_near_origin[$i];

					$network = new \dobj\Network();		
					$network->origin_searchable = $o;
					$network->location_searchable = $location;

					array_push($networks, $network);
				}
			}
		}

		return $networks;
	}
}

?>
