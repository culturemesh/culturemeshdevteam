<?php
namespace dobj;

class Network extends DisplayDObj {

	protected $id_city_cur;
	protected $city_cur;		// varchar(50)
	protected $id_region_cur;
	protected $region_cur;		// varchar(50)
	protected $id_country_cur;
	protected $country_cur;		// varchar(50)
	protected $id_city_origin;
	protected $city_origin;		// varchar(50)
	protected $id_region_origin;
	protected $region_origin;		// varchar(50)
	protected $id_country_origin;
	protected $country_origin;		// varchar(50)
	protected $id_language_origin;
	protected $language_origin;	// varchar(50)
	protected $network_class;

	protected $origin;
	protected $location;

	protected $date_added;		// timestamp
	protected $img_link;
	
	protected $member_count;
	protected $post_count;
	protected $join_date;
	protected $existing;		// bool, not in db

	public static function createFromId($id, $dal, $do2db) {

		$network = new Network();
		$network->id = $id;

		$network = $do2db->execute($dal, $network, 'getNetworkById');

		// set up origin array
		$origin_keys = array('id_city_origin', 'city_origin', 'id_region_origin',
			'region_origin', 'id_country_origin', 'country_origin',
			'id_language_origin', 'language_origin');
		
		$origin_array = array();

		foreach ($origin_keys as $key) {
			$origin_array[$key] = $network->$key;
		}

		// set up location array
		$location_keys = array('id_city_cur', 'city_cur', 'id_region_cur',
			'region_cur', 'id_country_cur', 'country_cur');

		$location_array = array();

		foreach ($location_keys as $key) {
			$location_array[$key] = $network->$key;
		}

		// now process, separate into origin and 
		$network->origin  = \misc\Util::ArrayToSearchable($origin_array);
		$network->location = \misc\Util::ArrayToSearchable($location_array);

		return $network;
	}

	public function display($context) {

	}

	public function getHTML($context) {

	}

	/*
	 * Parses values and generates title for display
	 * on main site
	 */
	public function getTitle() {

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
}
