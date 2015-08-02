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

	protected $country_tweet_terms;
	protected $country_tweet_terms_override;

	public function __construct() {
		$this->name_values = array('name', 'country_name');
	}

	public static function createFromId($id, $dal, $do2db) {

		$region = new Region();
		$region->id = $id;

		$region = $do2db->execute($dal, $region, 'getRegionById');

		if (get_class($region) == 'PDOStatement')
			return false;

		return $region;
	}
}

?>
