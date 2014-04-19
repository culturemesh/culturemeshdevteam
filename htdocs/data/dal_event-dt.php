<?php

class EventDT
{
	public $id;	// bigint(20)
	public $network_id;	// bigint(20)
	public $host_id;	// bigint(20)
	
	public $date_created;	// timestamp
	public $event_date;	// datetime
	
	public $title;
	public $email;
	public $address_1;	// varchar(40)
	public $address_2;	// varchar(30)
	public $city;		// varchar(50)
	public $country;	// varchar(50)
	public $description;	// varchar(500)

	public $username;
	public $first_name;
	public $last_name;
}

?>
