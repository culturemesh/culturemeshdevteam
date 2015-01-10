<?php
namespace dobj;

class Country extends Location {

	protected $name_values = array('name');

	protected $name;
	protected $latitude;
	protected $longitude;
	protected $population;
	protected $feature_code;

	public function __construct() {
		$this->name_values = array('name');
	}

	public function toString() {

		return $this->name;
	}
}

?>
