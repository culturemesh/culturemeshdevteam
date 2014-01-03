<?php

class EventDT
{
	public $id;	// bigint(20)
	public $network_id;	// bigint(20)
	public $host_id;	// bigint(20)
	
	public $date_created;	// timestamp
	public $event_date;	// datetime
	
	public $address_1;	// varchar(40)
	public $address_2;	// varchar(30)
	public $city;		// varchar(50)
	public $region;		// varchar(50)
	public $description;	// varchar(500)
}

?>