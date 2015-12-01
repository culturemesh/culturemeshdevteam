<?php
namespace search;

class NearbyGroupLocationSearch extends Search {

	private $locations;

	public function __construct($locations) {

		// Check and see if is array
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

		$this->locations = $locations;
	}

	public function run($dal, $do2db) {

		// Create custom query
		//
		$custom_query = $do2db->initializeCustomQuery();
		$custom_query->setValues(array(
			'name' => 'customPopulousCityGroupSearch',
			'select_rows' => array(),
			'from_tables' => array('cities'),
			'returning_class' => 'dobj\City',
			'returning_list' => True,
			'limit_offset' => 0,
			'limit_row_count' => 10
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

		$custom_query->addAWhere('country_id', 'IN', $location_ids, $type_string, count($location_ids));

		$dal->customPopulousCityGroupSearch = function($con=NULL) use ($custom_query) {
			return $custom_query->toDBQuery($con);
		};

		$results = $do2db->execute($dal, $custom_query->getParamObject(), 'customPopulousCityGroupSearch');

		// Check for no results
		if (get_class($results) == 'PDOStatement') {
			return False;
		}

		return $results;
	}
}

?>
