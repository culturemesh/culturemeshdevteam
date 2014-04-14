<?php
/**
  * @class
  * 
 **/
 
 /*
  * class:
  	_l - language
  	cr - city/region
  	rc - region
  	co - country
 */
class NetworkDT
{
	public $id;	//bigint(20)
	
	public $id_city_cur;
	public $city_cur;		// varchar(50)
	public $id_region_cur;
	public $region_cur;		// varchar(50)
	public $id_country_cur;
	public $country_cur;		// varchar(50)
	public $id_city_origin;
	public $city_origin;		// varchar(50)
	public $id_region_origin;
	public $region_origin;		// varchar(50)
	public $id_country_origin;
	public $country_origin;		// varchar(50)
	public $id_language_origin;
	public $language_origin;	// varchar(50
	public $network_class;

	public $date_added;		// timestamp
	public $img_link;
	
	public $member_count;
	public $post_count;
	public $join_date;
	public $existing;		// bool, not in db
}

?>
