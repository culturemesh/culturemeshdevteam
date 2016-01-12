<?php
namespace search;

class NearbyGroupLocationSearch extends Search {

	private $locations;
	private $class_to_column;

	public function __construct($locations) {

		// CHeck and see if is array
		//
		if (!is_array($locations)) {
			throw new \Exception('NearbyGroupLocationSearch: Locations must be shoved into an array');
		}

		// Make sure all the stuff is of the same
		// class
		//
		$class_check = get_class($locations[0]);
		$classes_are_different = False;

		for ($i=1; $i < count($locations); $i++) {
			if (get_class($locations[$i]) !== $class_check) {
				$classes_are_different = True;
				break;
			}
		}

		if ($classes_are_different) {
			throw new \Exception('NearbyGroupLocationSearch: The provided locations are two different types');
		}

		// Okay, now begin the work
		//
		$this->locations = $locations;

		$this->class_to_column = array(

			'dobj\City' => array(
				'table' => 'nearby_cities',
				'join_table' => 'cities',
				'column' => 'city_id',
				'result' => 'dobj\City'),
			'dobj\Region' => array(
				'table' => 'nearby_regions',
				'join_table' => 'regions',
				'column' => 'region_id',
				'result' => 'dobj\Region'),
			'dobj\Country' => array(
				'table' => 'nearby_countries',
				'join_table' => 'countries',
				'column' => 'country_id',
				'result' => 'dobj\Country')
			);
	}

	public function run($dal, $do2db) {

		$class = get_class($this->locations[0]); // class
		$c_to_c = $this->class_to_column[ $class ]; // query column

		// Create custom query
		// 
		$custom_query = $do2db->initializeCustomQuery();
		$custom_query->setValues(array(
			'name' => 'customNearbyGroupLocationSearch',
			'select_rows' => array('s.*', 'ns.neighbor_id', 'ns.' . $c_to_c['column']),
			'from_tables' => array($c_to_c['table'] . ' ns'),
			'returning_class' => $c_to_c['result'],
			'returning_list' => True 
			)
		);

		// Collect all of the location ids
		//
		$location_ids = array();
		$type_string = '';

		for ($i = 0; $i < count($this->locations); $i++) {
			array_push($location_ids, $this->locations[$i]->id);
			$type_string .= 'i';
		}

		$custom_query->addJoinStatementRaw("LEFT JOIN (SELECT * 
			FROM " . $c_to_c['join_table'] . ") s
			ON s.id=ns.neighbor_id");

		// Add statement to the query
		$custom_query->addAWhere($c_to_c['column'], 'IN', $location_ids, $type_string, count($this->locations), 'ns.');

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
