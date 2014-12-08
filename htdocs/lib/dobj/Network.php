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
	protected $language_origin;	// varchar(50
	protected $network_class;

	protected $date_added;		// timestamp
	protected $img_link;
	
	protected $member_count;
	protected $post_count;
	protected $join_date;
	protected $existing;		// bool, not in db

	public static function createFromId($id, $dal, $do2db) {

		$network = new Network();
		$network->id = $id;

		return $do2db->execute($dal, $network, 'getNetworkById');
	}

	public function display($context) {

	}

	public function getHTML($context) {

	}
}
