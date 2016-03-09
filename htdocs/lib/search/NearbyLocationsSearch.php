<?php
namespace search;

class NearbyLocationsSearch extends Search {

	private $locations;
	private $class_to_column;

	public function __construct($locations) {

		$this->locations = $locations;

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

		$custom_query = $do2db->initializeCustomQuery();

		$custom_query->setValues(array(
			'name' => 'NearbyLocationQuery',
			'select_rows' => array(),
			'from_tables' => array('nearby_cities'),
			'returning_class' => 'dobj\Network',
			'returning_list' => False
			)
		);
	}
}

?>
