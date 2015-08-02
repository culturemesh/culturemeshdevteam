<?php
namespace dobj;

class City extends Location {

	protected $name_values = array('name', 'region_name', 'country_name');

	protected $name;
	protected $region_id;
	protected $region_name;
	protected $country_id;
	protected $country_name;

	protected $region_tweet_terms;
	protected $region_tweet_terms_override;
	protected $country_tweet_terms;
	protected $country_tweet_terms_override;

	public function __construct() {

		$this->name_values = array('name', 'region_name', 'country_name');
	}

	public static function createFromId($id, $dal, $do2db) {

		$city = new City();
		$city->id = $id;

		$city = $do2db->execute($dal, $city, 'getCityById');

		if (get_class($city) == 'PDOStatement')
			return false;

		return $city;
	}
}

?>
