<?php
namespace dobj;

class Region extends Location {

	protected $name_values = array('name', 'country_name');

	protected $name;
	protected $country_id;
	protected $country_name;
	protected $latitude;
	protected $longitude;
	protected $population;
	protected $feature_code;

	public function __construct() {
		$this->name_values = array('name', 'country_name');
	}
}

?>
