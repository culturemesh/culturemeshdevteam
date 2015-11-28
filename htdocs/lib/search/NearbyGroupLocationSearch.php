<?php
namespace search;

class NearbyGroupLocationSearch extends Search {

	private $locations;
	private $class_to_column;

	public function __construct($locations) {

		if ($classes_are_different) {
			throw new \Exception('NearbyGroupLocationSearch: The provided locations are two different types');
		}

		if (!is_array($locations)) {
			throw new \Exception('NearbyGroupLocationSearch: Locations must be shoved into an array');
		}

		$this->locations = $locations;

		$this->class_to_column = array(

			'dobj\City' => array(
				'table' => 'nearby_cities',
				'column' => 'city_id'),
			'dobj\Region' => array(
				'table' => 'nearby_regions',
				'column' => 'region_id'),
			'dobj\Country' => array(
				'table' => 'nearby_countries',
				'column' => 'country_id')
			);
	}

	public function run($dal, $do2db) {

		$custom_query = $do2db->initializeCustomQuery();

		$class = get_class($this->locations[0]);
		$c_to_c = $this->class_to_column[ $class ];

		$custom_query->setValues(array(
			'name' => 'customNearbyGroupLocationSearch',
			'select_rows' => array(),
			'from_tables' => array($c_to_c['table']),
			'returning_class' => $class,
			'returning_list' => True 
			)
		);

		$location_ids = array();
		$type_string = '';

		for ($i = 0; $i < count($this->locations); $i++) {
			array_push($location_ids, $this->locations[$i]->id);
			$type_string .= 'i';
		}

		$custom_query->addAWhere($c_to_c['column'], 'IN', $location_ids, $type_string, count($this->locations));

		$dal->customNearbyGroupLocationSearch = function($con=NULL) use ($custom_query) {
			return $custom_query->toDBQuery($con);
		};

		$results = $do2db->execute($dal, $custom_query->getParamObject(), 'customNearbyGroupLocationSearch');

		// Check for no results
		if (get_class($results) == 'PDOStatement') {
			return False;
		}

		return $results;
	}
}

?>
