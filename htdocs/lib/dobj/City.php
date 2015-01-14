<?php
namespace dobj;

class City extends Location {

	protected $name_values = array('name', 'region_name', 'country_name');

	protected $name;
	protected $region_id;
	protected $region_name;
	protected $country_id;
	protected $country_name;
	protected $latitude;
	protected $longitude;
	protected $population;
	protected $feature_code;

	public function __construct() {

		$this->name_values = array('name', 'region_name', 'country_name');
	}
}

?>
