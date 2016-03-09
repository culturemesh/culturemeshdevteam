<?php
namespace dobj;

class Location extends Searchable {

	protected $name_values;
	protected $latitude;
	protected $longitude;
	protected $population;
	protected $feature_code;

	public function toString() {

		$str = '';

		for($i = 0; $i < count($this->name_values); $i++) {

			// get property name
			$val = $this->name_values[$i];

			// ignore empty stuff
			if ($this->$val != NULL) {
				$str .= $this->$val;

				// add comma if not last
				if (count($this->name_values) - $i > 1)
					$str .= ', ';
			}
		}

		return $str;
	}

	/*
	 * Treats location as a numerically-indexed array
	 *
	 * Returns the location data associated with number
	 * 0 is the base location
	 *
	 * Makes sure to skip elements if they are NULL
	 *
	 * Use: This is for use in mixed location lists. The programmer may not know
	 * the class going in
	 *
	 */
	public function getElement($offset) {

		if ($offset >= 3 || $offset < 0) {
		  throw new \Exception('Location->getElement: Offset ('. $offset .') is not possible');
		}

		$id = NULL;
		$name = NULL;

		// Again, 0 is the base location name
		//
		if ($offset === 0) {
			$id = $this->name;
			$name = $this->id;
		}

		// Stuff is class dependent from here on out
		$class = get_class($this);

		if ($offset === 1) {

			if ($class === 'dobj\City') {

				if ($this->region_id === NULL && $this->region_name === NULL) {
					$id = $this->country_id;
					$name = $this->country_name;
				}
				else {
					$id = $this->region_id;
					$name = $this->region_name;
				}
			}

			if ($class === 'dobj\Region') {
				
				if ($this->country_id === NULL && $this->country_name === NULL) {
					return NULL;
				}
				else {
					$id = $this->country_id;
					$name = $this->country_name;
				}
			}

			if ($class === 'dobj\Country') {
				return NULL;
			}
		}

		if ($offset === 2) {

			if ($class === 'dobj\City') {

				// If city doesn't have region, we've already got the country
				if ($this->region_id === NULL && $this->region_name === NULL) {
					return NULL;
				}
				else {
					$id = $this->country_id;
					$name = $this->country_name;
				}
			}

			if ($class === 'dobj\Region') {
				return NULL;
		  	 // throw new \Exception('Location->getElement: Region cannot have offset ('. $offset .').');
			}

			if ($class === 'dobj\Country') {
				return NULL;
		  	  //throw new \Exception('Location->getElement: Country cannot have offset ('. $offset .').');
			}
		}

		return array(
			'id' => $id,
			'name' => $name
		);
	}

	public function getElementCount() {

		// Stuff is class dependent from here on out
		$class = get_class($this);

		if ($class === 'dobj\City') {

			if ($this->region_id === NULL && $this->region_name === NULL
				&& $this->country_id === NULL && $this->country_name === NULL) {
				return 1;
			}

			else if ($this->region_id === NULL && $this->region_name === NULL) {
				return 2;
			}
			else {
				return 3;
			}
		}

		if ($class === 'dobj\Region') {
			
			if ($this->country_id === NULL && $this->country_name === NULL) {
				return 1;
			}
			else {
				return 2;
			}
		}

		if ($class === 'dobj\Country') {
			return 1;
		}
	}
}

?>
