<?php

namespace search;

class SearchableGeolocationSearch extends Search {

	private $latitude;
	private $longitude;

	private $bound_interval = 0.05;

	public function __construct($input) {

		$this->latitude = (float) $input['latitude'];
		$this->longitude = (float) $input['longitude'];
	}

	public function run($dal, $do2db) {

		$param_obj = new \dobj\Blank();
		$param_obj->latitude_upper_bound = $this->latitude + $this->bound_interval;
		$param_obj->latitude_lower_bound = $this->latitude - $this->bound_interval;
		$param_obj->longitude_upper_bound = $this->longitude + $this->bound_interval;
		$param_obj->longitude_lower_bound = $this->longitude - $this->bound_interval;

		$results = $do2db->execute($dal, $param_obj, 'getLocationsByGeolocation');

		// if no results were found
		// create a NullResult Object
		if (get_class($results) == 'PDOStatement') {
			$results = new NullSearchResult();
		}

		return $results;
	}
}

?>
