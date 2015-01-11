<?php
namespace dobj;

abstract class DObj {

	protected $id;

	public function __construct() {

	}

	public static function createFromId($id, $dal, $do2db) {

	}

	public static function createFromDataRow($row) {

		$keys = array_keys($row);
		$class = get_called_class();
		$dobj = new $class();

		foreach ($keys as $key) {
			$dobj->$key = $row[$key];
		}

		return $dobj;
	}

	/*
	 * JS style accessors and mutators
	 * 	- just cause I like 'em
	 */
	public function id($value=NULL) {
		
		if ($value == NULL)
			return $this->id;

		if (!is_int($value)) {
			throw new \InvalidArgumentException('Id must be an integer');
			return false;
		}

		$this->id = $value;

		return true;
	}

	// parse name
	public function getName() {

		if (!property_exists($this, 'first_name'))
			throw new \Exception('This class does not have a first_name property');
		if (!property_exists($this, 'last_name'))
			throw new \Exception('This class does not have a first_name property');

		$name = NULL;
		if ($this->first_name == '')
			$name = "UNNAMED USER";
		else {
			$name = $this->first_name;
			if (isset($this->last_name))
				$name .= " ".$this->last_name;
		}

		return $name;
	}

	/*
	 * Returns split for Sected Object
	 */
	public function getSplit($property) {
		return $this->$property;
	}

	/*
	 * Parses values and generates network title for display
	 * on main site
	 */
	public function getNetworkTitle() {

		if ($this->origin == NULL) {

			// set up origin array
			/*
			$origin_keys = array('id_city_origin', 'city_origin', 'id_region_origin',
				'region_origin', 'id_country_origin', 'country_origin',
				'id_language_origin', 'language_origin');
			 */
			$origin_keys = array('city_origin', 'region_origin', 'country_origin', 'language_origin');
			
			$origin_array = array();

			foreach ($origin_keys as $key) {
				$origin_array[$key] = $this->$key;
			}

			$this->origin = \misc\Util::ArrayToSearchable($origin_array);
		}

		if ($this->location == NULL) {

			// set up location array
			/*
			$location_keys = array('id_city_cur', 'city_cur', 'id_region_cur',
				'region_cur', 'id_country_cur', 'country_cur');
			 */

			$location_keys = array('city_cur', 'region_cur', 'country_cur');

			$location_array = array();

			foreach ($location_keys as $key) {
				$location_array[$key] = $this->$key;
			}

			$this->location = \misc\Util::ArrayToSearchable($location_array);
		}

		$origin_str = '';
		$location_str = $this->location->toString();

		// figure out type of origin string
		if (get_class($this->origin) == 'dobj\Language') {
			$origin_str = $this->origin->toString().' speakers';
		}
		else {
			$origin_str = 'From '.$this->origin->toString();
		}
		
		return $origin_str . ' in ' . $location_str;
	}

	public function fill($result) {

	}


	public function __get($name) {

		return $this->$name;
	}

	public function &getReference($name) {
		return $this->$name;
	}

	public function __set($name, $arg) {

		if (method_exists($this, $name)) {
			$this->$name($arg);	
		}
		else{
			$this->$name = $arg;
		}
	}
}

?>
