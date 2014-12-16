<?php
namespace dobj;

class Event extends DisplayDObj {

	protected $id_network;
	protected $id_host;
	protected $date_created;
	protected $event_date;
	protected $title;
	protected $address_1;
	protected $address_2;
	protected $city;
	protected $country;
	protected $description;
	protected $region;

	protected $email;
	protected $username;
	protected $first_name;
	protected $last_name;
	protected $img_link;

	public static function createFromId($id, $dal, $do2db) {

		// stub
	}

	public function display($context) {

	}

	public function getHTML($context) {

	}
}
