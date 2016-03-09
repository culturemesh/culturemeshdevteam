<?php
namespace search;

class PopulousCitySearch extends Search {

	private $location;

	public function __construct($location) {

		// Check and see if it's a location
		if (!in_array(get_class($location), array('dobj\Country'))) {
			throw new \Exception('NearbyGroupLocationSearch: Must pass in a country search object');
		}

		$this->location = $location;
	}

	public function run($dal, $do2db) {

		$results = $do2db->execute($dal, $this->location, 'getPopulousCitiesByCountry');

		// Check for no results
		if (get_class($results) == 'PDOStatement') {
			return False;
		}

		return $results;
	}
}

?>
