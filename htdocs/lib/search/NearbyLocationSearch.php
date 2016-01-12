<?php
namespace search;

class NearbyLocationSearch extends Search {

	private $location;
	private $class_to_column;

	public function __construct($location) {

		$this->location = $location;

		$this->class_to_column = array(

			'dobj\City' => array(
				'join_table' => 'cities',
				'table' => 'nearby_cities ns',
				'column' => 'city_id',
				'result' => 'dobj\City'),
			'dobj\Region' => array(
				'join_table' => 'regions',
				'table' => 'nearby_regions ns',
				'column' => 'region_id',
				'result' => 'dobj\Region'),
			'dobj\Country' => array(
				'join_table' => 'countries',
				'table' => 'nearby_countries ns',
				'column' => 'country_id',
				'result' => 'dobj\Country')
			);
	}

	public function run($dal, $do2db) {

		$custom_query = $do2db->initializeCustomQuery();

		$location_class = get_class($this->location);
		$c_to_c = $this->class_to_column[$location_class];

		$custom_query->setValues(array(
			'name' => 'customNearbyLocationSearch',
			'select_rows' => array('s.*', 'ns.neighbor_id', 'ns.' . $c_to_c['column']),
			'from_tables' => array($c_to_c['table']),
			'returning_class' => $c_to_c['result'],
			'returning_list' => True
			)
		);

		$custom_query->addJoinStatementRaw("LEFT JOIN (SELECT * 
					FROM " . $c_to_c['join_table'] . ") s
					ON ns.neighbor_id=s.id");

		$custom_query->addAWhere($c_to_c['column'], '=', $this->location->id, 'i', NULL, 'ns.');

		// add to dal
		$dal->customNearbyLocationSearch = function($con=NULL) use ($custom_query) {
			return $custom_query->toDBQuery($con);
		};

		$results = $do2db->execute($dal, $custom_query->getParamObject(), 'customNearbyLocationSearch');

		// Check for no results
		if (get_class($results) == 'PDOStatement') {
			return False;
		}

		return $results;
	}
}

?>
