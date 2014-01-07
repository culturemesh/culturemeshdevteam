<?php
/**
  * @class
  * 
 **/
 
class NetworkDT
{
	public $id;	//bigint(20)
	
	public $city_cur;	// varchar(50)
	public $region_cur;		// varchar(50)
	public $city_origin;	// varchar(50)
	public $region_origin;		// varchar(50)
	public $country_origin;	// varchar(50)
	public $language_origin;	// varchar(50)
	
	public $date_added;	// timestamp
	public $img_link;
	
	/// SOON TO BE LOST
	public $city;
	public $region;
	public $country;
	public $language;
	
	public $member_count;
	public $post_count;
}

?>
