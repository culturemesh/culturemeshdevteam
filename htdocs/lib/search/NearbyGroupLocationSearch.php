<?php
namespace search;

class NearbyGroupLocationSearch extends Search {

	private $locations;
	private $class_to_column;

	public function __construct($locations) {

		if ($classes_are_different) {
			throw new \Exception('NearbyGroupLocationSearch: The provided locations are two different types');
		}

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

		$c_to_c = $this->class_to_column[ $this->locations[0]['searchable_class'] ];

		$custom_query->setValues(array(
			'name' => 'NearbyGroupLocationQuery',
			'select_rows' => array(),
			'from_tables' => array($c_to_c['table']),
			'returning_class' => $this->locations[0]['searchable_class'],
			'returning_list' => True 
			)
		);

		$location_ids = array();

		for ($i = 0; $i < count($this->locations); $i++) {
			array_push($location_ids, $this->locations[$i]['id']);
		}

		$custom_query->addAWhere($c_to_c['column'], 'IN', $location_ids);	

	}
}

?>
