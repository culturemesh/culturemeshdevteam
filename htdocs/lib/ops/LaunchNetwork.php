<?php
namespace ops;

class LaunchNetwork {

	private $origin;
	private $location;
	private $user;
	private $network_id;

	public function __construct($origin, $location, $user=NULL) {

		$this->origin = $origin;
		$this->location = $location;
		$this->user = $user;
	}

	public function run($dal, $do2db) {

		// get query values
		$query_values = $this->prepareQueryValues();

		// create launch network query
		$query = "INSERT INTO networks ({{ origin_columns }}, {{ location_columns }}, network_class) VALUES ({{ origin_placeholders }}, {{ location_placeholders }}, {{ network_class }})";

		$launchNetworkQuery = new \dal\CustomQuery();
		$launchNetworkQuery->setQuery($query, array(
			'origin_columns' => implode(', ', $query_values['origin_columns']),
			'location_columns' => implode(', ', $query_values['location_columns']),
			'origin_placeholders' => $query_values['origin_placeholders'],
			'location_placeholders' => $query_values['location_placeholders'],
			'network_class' => $query_values['network_class']
		), $query_values['param_object']);

		$dal->register($launchNetworkQuery, array(
			'name' => 'launchNetwork',
			'params' => $query_values['params'],
			'param_types' => $query_values['param_types'],
			'returning' => False));

		$results = $do2db->execute($dal, $query_values['param_object'], 'launchNetwork');

		// get network id
		$this->network_id = $dal->lastInsertId();

		$obj = new \dobj\Blank();
		$obj->id_network = $this->network_id;
	
		// give launched network a twitter query row
		$results = $do2db->execute($dal, $obj, 'insertQueryRow');

		// add user to launched network
		if ($this->user !== NULL) {
			$obj->id_user = $this->user->id;
			$do2db->execute($dal, $obj, 'addUserToNetwork');
		}

		// return network id
		return $this->network_id;
	}

	public function getNetworkId() {
		return $this->network_id;
	}

	/*
	 * Returns an associative array with
	 * the values the CustomQuery requires
	 */
	private function prepareQueryValues() {

		$origin_columns = NULL;
		$location_columns = NULL;
		$origin_placeholders = NULL;
		$location_placeholders = NULL;
		$network_class = NULL;

		$param_object = new \dobj\Blank();

		// we also need params and param types

		//
		// ORIGIN STUFF
		//
		if (get_class($this->origin) === 'dobj\City') {

			// set object values
			$param_object->id_city_origin = $this->origin->id;
			$param_object->city_origin = $this->origin->name;
			$param_object->id_country_origin = $this->origin->country_id;
			$param_object->country_origin = $this->origin->country_name;

			/*
			 * Some cities may not have a region attached
			 */
			if ($this->origin->region_id !== NULL) {

				$origin_columns = array('id_city_origin', 'city_origin', 'id_region_origin', 'region_origin', 'id_country_origin', 'country_origin');

				$param_object->id_region_origin = $this->origin->region_id;
				$param_object->region_origin = $this->origin->region_name;

				$origin_placeholders = '?, ?, ?, ?, ?, ?';
			}
			else {
				$origin_columns = array('id_city_origin', 'city_origin', 'id_country_origin', 'country_origin');
				$origin_placeholders = '?, ?, ?, ?';
			}

			$network_class = 'cc';
		}

		if (get_class($this->origin) === 'dobj\Region') {

			$origin_columns = array('id_region_origin', 'region_origin', 'id_country_origin', 'country_origin');

			// set object values
			$param_object->id_region_origin = $this->origin->id;
			$param_object->region_origin = $this->origin->name;
			$param_object->id_country_origin = $this->origin->country_id;
			$param_object->country_origin = $this->origin->country_name;

			$origin_placeholders = '?, ?, ?, ?';
			$network_class = 'rc';
		}

		if (get_class($this->origin) === 'dobj\Country') {

			$origin_columns = array('id_country_origin', 'country_origin');

			// set object values
			$param_object->id_country_origin = $this->origin->id;
			$param_object->country_origin = $this->origin->name;

			$origin_placeholders = '?, ?';
			$network_class = 'co';
		}

		if (get_class($this->origin) == 'dobj\Language') {

			$origin_columns = array('id_language_origin', 'language_origin');

			// set object values
			$param_object->id_language_origin = $this->origin->id;
			$param_object->language_origin = $this->origin->name;

			$origin_placeholders = '?, ?';
			$network_class = '_l';
		}

		/// LOCATION STUFF
		//
		if (get_class($this->location) === 'dobj\City') {

			// set object values
			$param_object->id_city_cur = $this->location->id;
			$param_object->city_cur = $this->location->name;
			$param_object->id_country_cur = $this->location->country_id;
			$param_object->country_cur = $this->location->country_name;

			/*
			 * Some cities may not have a region attached
			 */
			if ($this->location->region_id !== NULL) {

				$location_columns = array('id_city_cur', 'city_cur', 'id_region_cur', 'region_cur', 'id_country_cur', 'country_cur');

				$param_object->id_region_cur = $this->location->region_id;
				$param_object->region_cur = $this->location->region_name;

				$location_placeholders = '?, ?, ?, ?, ?, ?';
			}
			else {
				$location_columns = array('id_city_cur', 'city_cur', 'id_country_cur', 'country_cur');
				$location_placeholders = '?, ?, ?, ?';
			}

			/*
			$location_columns = array('id_city_cur', 'city_cur', 'id_region_cur', 'region_cur', 'id_country_cur', 'country_cur');

			// set object values
			$param_object->id_city_cur = $this->location->id;
			$param_object->city_cur = $this->location->name;
			$param_object->id_region_cur = $this->location->region_id;
			$param_object->region_cur = $this->location->region_name;
			$param_object->id_country_cur = $this->location->country_id;
			$param_object->country_cur = $this->location->country_name;

			$location_placeholders = '?, ?, ?, ?, ?, ?';
			 */
		}

		if (get_class($this->location) === 'dobj\Region') {

			$location_columns = array('id_region_cur', 'region_cur', 'id_country_cur', 'country_cur');

			// set object values
			$param_object->id_region_cur = $this->location->id;
			$param_object->region_cur = $this->location->name;
			$param_object->id_country_cur = $this->location->country_id;
			$param_object->country_cur = $this->location->country_name;

			$location_placeholders = '?, ?, ?, ?';
		}

		if (get_class($this->location) === 'dobj\Country') {

			$location_columns = array('id_country_cur', 'country_cur');

			// set object values
			$param_object->id_country_cur = $this->location->id;
			$param_object->country_cur = $this->location->name;

			$location_placeholders = '?, ?';
		}

		return array(
			'origin_columns' => $origin_columns,
			'location_columns' => $location_columns,
			'origin_placeholders' => $origin_placeholders,
			'location_placeholders' => $location_placeholders,
			'network_class' => '\'' . $network_class . '\'',
			'param_object' => $param_object,
			'params' => array_merge($origin_columns, $location_columns),
			'param_types' => $origin_types . $location_types
		);
	}
}
