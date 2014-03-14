<?php
/**
  * @class
  * 
 **/
 
 /*
  * class:
  	_l - language
  	cr - city/region
  	_r - region
  	co - country
 */
class NetworkDT
{
	public $id;	//bigint(20)
	
	public $city_cur;		// varchar(50)
	//public $region_cur;		// varchar(50)
	public $country_cur;		// varchar(50)
	public $city_origin;		// varchar(50)
	public $region_origin;		// varchar(50)
	public $country_origin;		// varchar(50)
	public $language_origin;	// varchar(50
	public $network_class;

	// ids all bigint(20)
	public $id_city_cur;
	public $id_country_cur;
	public $id_city_origin;
	public $id_region_origin;
	public $id_country_origin;
	public $id_language_origin;
	
	public $date_added;		// timestamp
	public $img_link;
	
	public $member_count;
	public $post_count;
	public $existing;		// bool, not in db
}

?>
