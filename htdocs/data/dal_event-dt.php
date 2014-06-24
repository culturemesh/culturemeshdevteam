<?php

class EventDT
{
	public $id;	// bigint(20)
	public $id_network;	// bigint(20)
	public $id_host;	// bigint(20)
	
	public $date_created;	// timestamp
	public $event_date;	// datetime
	
	public $title;
	public $email;
	public $address_1;	// varchar(40)
	public $address_2;	// varchar(30)
	public $city;		// varchar(50)
	public $region;
	public $country;	// varchar(50)
	public $description;	// varchar(500)

	public $username;
	public $first_name;
	public $last_name;
	public $img_link;

	public $city_cur;
	public $region_cur;
	public $country_cur;
	public $city_origin;
	public $region_origin;
	public $country_origin;
	public $language_origin;
	public $network_class;

	public $attending;
}

?>
