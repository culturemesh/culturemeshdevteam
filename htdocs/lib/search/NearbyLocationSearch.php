<?php
namespace search;

class NearbyLocationSearch extends Search {

	private $location;

	public function __construct($location) {

		$this->location = $location;

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
	}

	public function run($dal, $do2db) {

	}
}

?>
