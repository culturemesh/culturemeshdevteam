<?php
namespace search;

class NetworkSearch extends Search {

	private $searchables;
	private $class_to_column;

	public function __construct($origin, $location) {

/*
		$this->class_to_column = array(

			'dobj\City' => array(
				'location' => 'id_city_cur',
				'origin' => 'id_city_origin'),
			'dobj\Region' => array(
				'location' => 'id_region_cur',
				'origin' => 'id_region_origin'),
			'dobj\Country' => array(
				'location' => 'id_country_cur',
				'origin' => 'id_country_origin'),
			'dobj\Language' => array(
				'location' => NULL,
				'origin' => 'id_language_origin'));
*/

		$this->searchables = array(
			'origin' => $origin,
			'location' => $location
		);
	}

	public function run($dal, $do2db) {

		//
		// create search name
	
		$custom_query = $do2db->initializeCustomQuery();

		$custom_query->setValues(array(
			'name' => 'NetworkSearchQuery',
			'select_rows' => array(),
			'from_tables' => array('networks'),
			'returning_class' => 'dobj\Network',
			'returning_list' => False
			)
		);

		$origin_lines = $custom_query->createWhereLinesFromSearchable($this->searchables['origin'], 'networks', 'origin');
		$location_lines = $custom_query->createWhereLinesFromSearchable($this->searchables['location'], 'networks', 'location', 'AND');

		$all_lines = array_merge($origin_lines, $location_lines);

		foreach ($all_lines as $line) {
			$custom_query->insertWhereLine($line);
		}

		/*
		$custom_query->addAWhere($this->class_to_column[ $this->searchables['origin']['searchable_class'] ]['origin'], '=', $this->searchables['origin']['id'], NULL, 'i');
		$custom_query->addAnotherWhere('AND', $this->class_to_column[ $this->searchables['location']['searchable_class'] ]['location'], '=', $this->searchables['location']['id'], NULL, 'i');

		// Depending on the scope of the searchables, we'll have to add some NULLs to the query
		//
		// ...For origin
		if ( in_array($this->searchables['origin']['searchable_class'], array('dobj\Region', 'dobj\Country') )) {
			$custom_query->appendANull('AND', 'id_city_origin');
		}

		if ( $this->searchables['origin']['searchable_class'] == 'dobj\Country' ) {
			$custom_query->appendANull('AND', 'id_region_origin');
		}

		// ...For location
		if ( in_array($this->searchables['location']['searchable_class'], array('dobj\Region', 'dobj\Country') )){
			$custom_query->appendANull('AND', 'id_city_cur');
		}

		if ( $this->searchables['location']['searchable_class'] == 'dobj\Country' ) {
			$custom_query->appendANull('AND', 'id_region_cur');
		}
		*/
		// add to dal
		$dal->customNetworkSearch = function($con=NULL) use ($custom_query) {
			return $custom_query->toDBQuery($con);
		};

		$results = $do2db->execute($dal, $custom_query->getParamObject(), 'customNetworkSearch');

		// Check for no results
		if (get_class($results) == 'PDOStatement') {
			return False;
		}

		return $results;
	}
}

?>
