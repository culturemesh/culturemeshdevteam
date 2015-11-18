<?php
namespace search;

class RelatedNetworkSearch extends Search {

	private $nearby_location_search;
	private $nearby_origin_search;

	public function __construct($origin, $location) {

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
			'name' => 'NetworkSearchQuery',
			'select_rows' => array(),
			'from_tables' => array('nearby_cities'),
			'returning_class' => 'dobj\Network',
			'returning_list' => False
			)
		);

		$custom_query->addAWhere($this->class_to_column[ $this->searchables['origin']['searchable_class'] ]['origin'], '=', $this->searchables['origin']['id'], 'i');
		$custom_query->addAnotherWhere('AND', $this->class_to_column[ $this->searchables['location']['searchable_class'] ]['location'], '=', $this->searchables['location']['id'], 'i');

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
